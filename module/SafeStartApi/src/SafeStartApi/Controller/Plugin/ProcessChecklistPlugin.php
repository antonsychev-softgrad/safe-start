<?php
namespace SafeStartApi\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class ProcessChecklistPlugin extends AbstractPlugin
{
    public function pushNewChecklistNotification(\SafeStartApi\Entity\CheckList $checkList)
    {
        $vehicle = $checkList->getVehicle();
        $alerts = $checkList->getAlerts();

        $androidDevices = array();
        $iosDevices = array();
        $currentUser = $checkList->getUser();
        $responsibleUsers = $vehicle->getResponsibleUsers();
        $vehicleUsers = $vehicle->getUsers();
        $pushCriticalAlerts = false;
        foreach ($alerts as $alert) {
            if ($alert->getField()->getAlertCritical()) {
                $pushCriticalAlerts = true;
                break;
            }
        }

        foreach ($responsibleUsers as $responsibleUser) {
            if ($currentUser->getId() == $responsibleUser->getId()) continue;
            $responsibleUserInfo = $responsibleUser->toInfoArray();

            if (!$pushCriticalAlerts) continue;
            // send email to responsible
            $checkList = $vehicle->getLastInspection();
            $link = $checkList->getFaultPdfLink();
            $path = $this->getController()->inspectionFaultPdf()->getFilePathByName($link);
            if (!$link || !file_exists($path)) $path = $this->getController()->inspectionFaultPdf()->create($checkList);

            if (file_exists($path)) {
                try {
                    $this->getController()->MailPlugin()->send(
                        'New inspection fault report',
                        $responsibleUserInfo['email'],
                        'checklist_fault.phtml',
                        array(
                            'name' => $responsibleUserInfo['firstName'] . ' ' . $responsibleUserInfo['lastName']
                        ),
                        $path
                    );
                } catch (\Exception $e) {
                    $logger = $this->getController()->getServiceLocator()->get('ErrorLogger');
                    $logger->debug(json_encode($e->getMessage()));
                }
            }

            switch (strtolower($responsibleUserInfo['device'])) {
                case 'android':
                    $androidDevices[] = $responsibleUserInfo['deviceId'];
                    break;
                case 'ios':
                    $iosDevices[] = $responsibleUserInfo['deviceId'];
                    break;
            }
        }

        foreach ($vehicleUsers as $vehicleUser) {
            if ($currentUser->getId() == $vehicleUser->getId()) continue;
            $vehicleUserInfo = $vehicleUser->toInfoArray();
            switch (strtolower($vehicleUserInfo['device'])) {
                case 'android':
                    $androidDevices[] = $vehicleUserInfo['deviceId'];
                    break;
                case 'ios':
                    $iosDevices[] = $vehicleUserInfo['deviceId'];
                    break;
            }
        }

        $message = '';
        $badge = 0;
        if (!empty($alerts) && $pushCriticalAlerts) {
            $message =
                "Vehicle Alert \n\r" .
                "Vehicle ID#" . $vehicle->getPlantId() . " has a critical error with its: \n\r";
            foreach ($alerts as $alert) {
                if ($alert->getField()->getAlertCritical()) continue;
                $badge++;
                $message .= $alert->getField()->getAlertDescription() ? $alert->getField()->getAlertDescription() : $alert->getField()->getAlertTitle() . "\n\r";
            }
        } else {
            $badge = 1;
            $message .= 'Checklist for Vehicle ID #' . $vehicle->getPlantId() . ' added';
        }

        if (!empty($androidDevices)) $this->getController()->pushNotificationPlugin()->android($androidDevices, $message, $badge);
        if (!empty($iosDevices)) $this->getController()->pushNotificationPlugin()->ios($iosDevices, $message, $badge);
    }

    public function setInspectionStatistic(\SafeStartApi\Entity\CheckList $checkList)
    {
        $fieldsDataValues = array();
        $fieldsStructure = json_decode($checkList->getFieldsStructure());
        $fieldsData = json_decode($checkList->getFieldsData(), true);
        foreach ($fieldsData as $fieldData) $fieldsDataValues[$fieldData['id']] = $fieldData['value'];

        $query = $this->getController()->em->createQuery('DELETE FROM \SafeStartApi\Entity\InspectionBreakdown f WHERE f.check_list = ?1');
        $query->setParameter(1, $checkList);
        $query->getResult();

        foreach ($fieldsStructure as $group) {
            if ($this->isEmptyGroup($group, $fieldsDataValues)) continue;
            $record = new \SafeStartApi\Entity\InspectionBreakdown();

            $record->setDefault(0);
            $record->setAdditional((int)$group->additional);
            $record->setKey($group->groupName);
            $record->setFieldId($group->id);
            $record->setCheckList($checkList);

            $this->getController()->em->persist($record);
            $this->getController()->em->flush();
        }
    }

    private function isEmptyGroup($group, $fieldsDataValues)
    {
        if (isset($group->items) && is_array($group->items)) {
            $fields = $group->items;
        } elseif (isset($group->fields) && is_array($group->fields)) {
            $fields = $group->fields;
        } else {
            return true;
        }
        foreach ($fields as $field) {
            if ($field->type == 'group') {
                if (!$this->isEmptyGroup($field, $fieldsDataValues)) return false;
            }
            if (!empty($fieldsDataValues[$field->id])) {
                return false;
            }
        }
        return true;
    }
}