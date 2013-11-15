<?php

namespace SafeStartApi\Controller;

use SafeStartApi\Base\PublicAccessRestController;
use SafeStartApi\Entity\Vehicle;

class PublicVehicleController extends PublicAccessRestController
{

    public function getInfoByPlantIdAction()
    {
        if (!$this->_requestIsValid('vehicle/getinfobyplantid')) return $this->_showBadRequest();

        $plantId = (int)$this->data->plantId;
        $vehRep = $this->em->getRepository('SafeStartApi\Entity\Vehicle');
        $vehicle = $vehRep->findOneBy(array('plantId' => $plantId));
        if(empty($vehicle)) return $this->_showNotFound();

        $vehicleData = $vehicle->toResponseArray();

        $this->answer = array(
            'vehicleData' => $vehicleData,
        );

        return $this->AnswerPlugin()->format($this->answer);
    }

    public function checklistToEmailAction()
    {
        if (!$this->_requestIsValid('vehicle/checklisttoemail')) return $this->_showBadRequest();

        $emails = $this->data->emails;

        $projectName = isset($this->data->projectName) ? $this->data->projectName : '';
        $projectNumber = isset($this->data->projectNumber) ? $this->data->projectNumber : 0;
        $registrationNumber = isset($this->data->registrationNumber) ? $this->data->registrationNumber : '';
        $serviceDueHours = isset($this->data->serviceDueHours) ? $this->data->serviceDueHours : 0;
        $serviceDueKm = isset($this->data->serviceDueKm) ? $this->data->serviceDueKm : 0;
        $title = isset($this->data->title) ? $this->data->title : '';
        $type = isset($this->data->vehicleType) ? $this->data->vehicleType : '';

        $userData = array(
            'firstName' => isset($this->data->firstName) ? $this->data->firstName : '',
            'lastName' => isset($this->data->lastName) ? $this->data->lastName : '',
            'signature' => isset($this->data->signature) ? $this->data->signature : '',
        );

        $vehicleRepository = $this->em->getRepository('SafeStartApi\Entity\Vehicle');

        // save checklist
        $persist = false;
        if(!empty($this->data->plantId)) {
            $plantId = strtoupper($this->data->plantId);
            $vehicle = $vehicleRepository->findOneBy(array('plantId' => $plantId));
            if ($vehicle) {
                $company = $vehicle->getCompany();
                if(!is_null($company)) return $this->_showKeyExists('Vehicle with such Plant ID already exists');
            } else {
                $vehicle = new Vehicle();
                $persist = true;
            }
        } else {
            $plantId = uniqid('vehicle');
            $testVehicle = $vehicleRepository->findOneBy(array('plantId' => $plantId));
            while($testVehicle) {
                $plantId = uniqid('vehicle');
                $testVehicle = $vehicleRepository->findOneBy(array('plantId' => $plantId));
            }
            $vehicle = new Vehicle();
            $vehicle->setEnabled(1);
            $persist = true;
        }

        $vehicle->setPlantId($plantId);
        $vehicle->setProjectName($projectName);
        $vehicle->setProjectNumber($projectNumber);
        $vehicle->setServiceDueHours($serviceDueHours);
        $vehicle->setServiceDueKm($serviceDueKm);
        $vehicle->setTitle($title);
        $vehicle->setType($type);

        if($persist) {
            $this->em->persist($vehicle);
        }

        $query = $this->em->createQuery('SELECT f FROM SafeStartApi\Entity\DefaultField f WHERE f.deleted = 0 AND f.enabled = 1');
        $items = $query->getResult();

        $fieldsStructure = $this->GetDataPlugin()->getChecklistStructure($items);

        $fieldsStructure = json_encode($fieldsStructure);
        $fieldsData = json_encode($this->data->fields);

        $checkList = new \SafeStartApi\Entity\CheckList();
        $checkList->setVehicle($vehicle);
        $checkList->setFieldsStructure($fieldsStructure);
        $checkList->setFieldsData($fieldsData);
        $checkList->setGpsCoords((isset($this->data->gps) && !empty($this->data->gps)) ? $this->data->gps : null);
        $checkList->setLocation((isset($this->data->location) && !empty($this->data->location)) ? $this->data->location : null);

        if (isset($this->data->operator_name) && !empty($this->data->operator_name)) $checkList->setOperatorName($this->data->operator_name);
        else $checkList->setOperatorName($userData['firstName'] ." ". $userData['lastName']);

        if ((isset($this->data->odometer) && !empty($this->data->odometer))) {
            $checkList->setCurrentOdometer($this->data->odometer);
            $vehicle->setCurrentOdometerKms($this->data->odometer);
        } else {
            $checkList->setCurrentOdometer(null);
            $vehicle->setCurrentOdometerKms(null);
        }

        if ((isset($this->data->odometer_hours) && !empty($this->data->odometer_hours))) {
            $checkList->setCurrentOdometerHours($this->data->odometer_hours);
            $vehicle->setCurrentOdometerHours($this->data->odometer_hours);
        } else {
            $checkList->setCurrentOdometer(null);
            $vehicle->setCurrentOdometerKms(null);
        }

        $checkList->setUserData($userData);
        $checkList->setEmailMode(1);
        $uniqId = uniqid();
        $checkList->setHash($uniqId);
        $this->em->persist($checkList);

        // save new alerts
        $alerts = array();
        if (!empty($this->data->alerts) && is_array($this->data->alerts)) {
            $alerts = $this->data->alerts;
            foreach ($alerts as $alert) {
                $field = $this->em->find('SafeStartApi\Entity\DefaultField', $alert->fieldId);
                if ($field === null) {
                    continue;
                }
                $newAlert = new \SafeStartApi\Entity\DefaultAlert();
                $newAlert->setDefaultField($field);
                $newAlert->setCheckList($checkList);
                $newAlert->setDescription((isset($alert->comment) && !empty($alert->comment)) ? $alert->comment : null);
                $newAlert->setImages((isset($alert->images) && !empty($alert->images)) ? $alert->images : array());
                $newAlert->setVehicle($vehicle);

                $this->em->persist($newAlert);
            }
        }
        $this->em->flush();

        $this->answer = array(
            'checklist' => $checkList->getHash(),
        );

        if (APP_RESQUE) {
            \Resque::enqueue('new_email_checklist_uploaded', '\SafeStartApi\Jobs\NewEmailCheckListUploaded', array(
                'checkListId' => $checkList->getId(),
                'emails' => $emails
            ));
            return $this->AnswerPlugin()->format($this->answer);
        } else {
            $pdf = $this->inspectionPdf()->create($checkList);
            $this->processChecklistPlugin()->setInspectionStatistic($checkList);
            if (file_exists($pdf)) {
                foreach($emails as $email) {
                    $email = (array) $email;
                    $this->MailPlugin()->send(
                        $this->moduleConfig['params']['emailSubjects']['new_vehicle_inspection'],
                        $email['email'],
                        'checklist.phtml',
                        array(
                            'name' => isset($email['name']) ? $email['name'] : 'friend',
                            'emailStaticContentUrl' => $this->moduleConfig['params']['email_static_content_url']
                        ),
                        $pdf
                    );
                }
                return $this->AnswerPlugin()->format($this->answer);
            } else {
                $this->answer = array(
                    'errorMessage' => 'PDF document was not generated'
                );
                return $this->AnswerPlugin()->format($this->answer, 500, 500);
            }
        }
    }

    public function getChecklistByHashAction()
    {
        if (!$this->_requestIsValid('vehicle/getchecklistbyhash')) return $this->_showBadRequest();

        $hash = $this->data->hash;
        $inspection = $this->em->getRepository('SafeStartApi\Entity\Checklist')->findOneBy(array(
            'hash' => $hash,
            'deleted' => 0,
        ));

        if (!$inspection) return $this->_showNotFound("Inspection not found.");

        $vehicle = $inspection->getVehicle();
        if (!$vehicle) return $this->_showNotFound("Vehicle with this checklist not found.");

        $items = $vehicle->getFields();
        $checklist = $this->GetDataPlugin()->buildChecklist($items, $inspection);

        $this->answer = array(
            'checklist' => $checklist,
        );

        return $this->AnswerPlugin()->format($this->answer);
    }

    public function sendCheckListToEmailsAction()
    {
        if (!$this->_requestIsValid('vehicle/sendCheckListToEmails')) return $this->_showBadRequest();

        $hash = $this->data->hash;
        $inspection = $this->em->getRepository('SafeStartApi\Entity\Checklist')->findOneBy(array(
            'hash' => $hash,
            'deleted' => 0,
        ));
        if (!$inspection) return $this->_showNotFound("Inspection not found.");
        $emails = $this->data->emails;


        $this->answer = array(
            'done' => true,
        );

        if (APP_RESQUE) {
            \Resque::enqueue('default', '\SafeStartApi\Jobs\CheckListResend', array(
                'checkListId' => $inspection->getId(),
                'emails' => $emails
            ));
            return $this->AnswerPlugin()->format($this->answer);
        } else {
            $link = $inspection->getPdfLink();
            $cache = \SafeStartApi\Application::getCache();
            $cashKey = $link;
            $path = '';
            if ($cashKey && $cache->hasItem($cashKey)) {
                $path = $this->inspectionPdf()->getFilePathByName($link);
            }
            if (!$link || !file_exists($path)) $path = $this->inspectionPdf()->create($inspection);

            if (file_exists($path)) {
                foreach($emails as $email) {
                    $email = (array) $email;
                    $this->MailPlugin()->send(
                        $this->moduleConfig['params']['emailSubjects']['new_vehicle_inspection'],
                        $email['email'],
                        'checklist.phtml',
                        array(
                            'name' => isset($email['name']) ? $email['name'] : 'friend',
                            'emailStaticContentUrl' => $this->moduleConfig['params']['email_static_content_url']
                        ),
                        $path
                    );
                }
                return $this->AnswerPlugin()->format($this->answer);
            } else {
                $this->answer = array(
                    'errorMessage' => 'PDF document was not generated'
                );
                return $this->AnswerPlugin()->format($this->answer, 500, 500);
            }
        }

        return $this->AnswerPlugin()->format($this->answer);
    }


    private function _setInspectionStatistic(\SafeStartApi\Entity\CheckList $checkList)
    {
        $fieldsDataValues = array();
        $fieldsStructure = json_decode($checkList->getFieldsStructure());
        $fieldsData = json_decode($checkList->getFieldsData(), true);
        foreach ($fieldsData as $fieldData) $fieldsDataValues[$fieldData['id']] = $fieldData['value'];

        $query = $this->em->createQuery('DELETE FROM \SafeStartApi\Entity\InspectionBreakdown f WHERE f.check_list = ?1');
        $query->setParameter(1, $checkList);
        $query->getResult();

        foreach ($fieldsStructure as $group) {
            if ($this->_isEmptyGroup($group, $fieldsDataValues)) continue;
            $record = new \SafeStartApi\Entity\InspectionBreakdown();

            $record->setDefault(0);
            $record->setAdditional((int)$group->additional);
            $record->setKey($group->groupName);
            $record->setFieldId($group->id);
            $record->setCheckList($checkList);

            $this->em->persist($record);
            $this->em->flush();
        }
    }

    private function _isEmptyGroup($group, $fieldsDataValues)
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
                if (!$this->_isEmptyGroup($field, $fieldsDataValues)) return false;
            }
            if (!empty($fieldsDataValues[$field->id])) {
                return false;
            }
        }
        return true;
    }

    public function sendTestEmailAction() {

        $mapUrl = 'http://maps.googleapis.com/maps/api/staticmap?center=53.912739,%2027.513294&zoom=12&size=400x400&markers=color:blue|53.9128749,27.5135608&sensor=false&format=png';
        $moduleConfig = $this->getServiceLocator()->get('Config');
        $mapPath =  dirname(__FILE__) . "/../../../../.." . $moduleConfig['defUsersPath'] . uniqid() . "test-test.PNG";
        echo $mapPath;
        file_put_contents($mapPath, file_get_contents($mapUrl));

        /*
        $email = 'test21141@gmail.com';
        $this->MailPlugin()->send(
            'New inspection report',
            $email,
            'checklist.phtml',
            array(
                'name' => 'Artem'
            ),
            '/var/www/safe-start.dev/data/users/pdf/checklist_review_4_2_113_at_2013-09-17.pdf'
        );
        */
    }
}
