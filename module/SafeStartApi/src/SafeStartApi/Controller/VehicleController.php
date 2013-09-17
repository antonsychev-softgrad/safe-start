<?php

namespace SafeStartApi\Controller;

use SafeStartApi\Base\RestrictedAccessRestController;
use SafeStartApi\Entity\Vehicle;

class VehicleController extends RestrictedAccessRestController
{

    public function getListAction()
    {
        if (!$this->_requestIsValid('vehicle/getlist')) return $this->_showBadRequest();

        $user = $this->authService->getIdentity();

        $vehicles = $user->getVehicles();

        $vehiclesList = array();
        foreach ($vehicles as $vehicle) {
            $vehiclesList[] = array(
                'vehicleId' => $vehicle->getId(),
                'type' => $vehicle->getType(),
                'vehicleName' => $vehicle->getTitle(),
                'role' => 'user'
            );
        }

        $responsibleVehicles = $user->getResponsibleForVehicles();
        foreach ($responsibleVehicles as $vehicle) {
            $vehiclesList[] = array(
                'vehicleId' => $vehicle->getId(),
                'type' => $vehicle->getType(),
                'vehicleName' => $vehicle->getTitle(),
                'role' => 'responsible'
            );
        }

        $this->answer = array(
            'vehicles' => $vehiclesList,
        );

        return $this->AnswerPlugin()->format($this->answer);
    }

    public function getDataByIdAction()
    {
        if (!$this->_requestIsValid('vehicle/getinfo')) return $this->_showBadRequest();

        $id = (int)$this->params('id');
        $vehRep = $this->em->getRepository('SafeStartApi\Entity\Vehicle');
        $veh = $vehRep->findOneById($id);
        if (empty($veh)) return $this->_showNotFound();

        if (!$veh->haveAccess($this->authService->getStorage()->read())) return $this->_showUnauthorisedRequest();

        $vehicleData = $veh->toResponseArray();

        $this->answer = array(
            'vehicleData' => $vehicleData,
        );

        return $this->AnswerPlugin()->format($this->answer);
    }

    public function getChecklistAction()
    {
        if (!$this->_requestIsValid('vehicle/getchecklist')) return $this->_showBadRequest();

        $vehicleId = (int)$this->params('id');
        $vehicle = $this->em->find('SafeStartApi\Entity\Vehicle', $vehicleId);

        if (!$vehicle) return $this->_showNotFound("Vehicle not found.");
        if (!$vehicle->haveAccess($this->authService->getStorage()->read())) return $this->_showUnauthorisedRequest();

        $inspection = null;
        $checklistId = (int)$this->getRequest()->getQuery('checklistId'); //todo: also check by hash
        if ($checklistId) {
            $inspection = $this->em->find('SafeStartApi\Entity\CheckList', $checklistId);
            if (!$inspection) return $this->_showNotFound("Requested inspection does not exist.");
        }

        $cache = \SafeStartApi\Application::getCache();
        $cashKey = "getVehicleChecklist" . $vehicle->getId();

        if ($cache->hasItem($cashKey)) {
            $checklist = $cache->getItem($cashKey);
        } else {
            $query = $this->em->createQuery('SELECT f FROM SafeStartApi\Entity\Field f WHERE f.deleted = 0 AND f.enabled = 1 AND f.vehicle = ?1');
            $query->setParameter(1, $vehicle);
            $items = $query->getResult();
            $checklist = $this->GetDataPlugin()->buildChecklist($items, $inspection);
            $cache->setItem($cashKey, $checklist);
        }

        $this->answer = array(
            'checklist' => $checklist,
        );

        return $this->AnswerPlugin()->format($this->answer);
    }

    public function getChecklistDataAction()
    {
        if (($checklistId = (int)$this->params('id')) !== null) {
            $checklist = null;

            $query = $this->em->createQuery("SELECT cl FROM SafeStartApi\Entity\CheckList cl WHERE cl.id = :id");
            $query->setParameters(array('id' => $checklistId));
            $queryResult = $query->getResult();
            if (is_array($queryResult) && !empty($queryResult)) {
                if (isset($queryResult[0])) {
                    $checklist = array(
                        'id' => $queryResult[0]->getid(),
                        'gpsCoords' => $queryResult[0]->getGpsCoords(),
                        'fieldsStructure' => json_decode($queryResult[0]->getFieldsStructure()),
                        'fieldsData' => json_decode($queryResult[0]->getFieldsData()),
                        'alerts' => $queryResult[0]->getAlerts(),
                        'creationDate' => $queryResult[0]->getCreationDate(),
                    );
                }
            }

            if ($checklist !== null) {
                $this->answer = array(
                    'checklist' => $checklist,
                );
                return $this->AnswerPlugin()->format($this->answer);
            } else {
                $this->answer = array(
                    "errorMessage" => "CheckList not found."
                );
                return $this->AnswerPlugin()->format($this->answer, 404);
            }
        } else {
            $this->_showBadRequest();
        }
    }

    public function completeChecklistAction()
    {
        //todo: check why bad request with alerts
        // if (!$this->_requestIsValid('vehicle/completechecklist')) return $this->_showBadRequest();

        // save checklist
        $vehicleId = $this->params('id');
        $vehicle = $this->em->find('SafeStartApi\Entity\Vehicle', $vehicleId);

        if (!$vehicle) return $this->_showNotFound("Vehicle not found.");
        if (!$vehicle->haveAccess($this->authService->getStorage()->read())) return $this->_showUnauthorisedRequest();

        $user = $this->authService->getStorage()->read();

        $query = $this->em->createQuery('SELECT f FROM SafeStartApi\Entity\Field f WHERE f.deleted = 0 AND f.enabled = 1 AND f.vehicle = ?1');
        $query->setParameter(1, $vehicle);
        $items = $query->getResult();

        $fieldsStructure = $this->GetDataPlugin()->getChecklistStructure($items);

        $fieldsStructure = json_encode($fieldsStructure);
        $fieldsData = json_encode($this->data->fields);

        $inspection = null;
        $checklistId = (int)$this->getRequest()->getQuery('checklistId'); //todo: also check by hash
        if ($checklistId) {
            $inspection = $this->em->find('SafeStartApi\Entity\CheckList', $checklistId);
            if (!$inspection) return $this->_showNotFound("Requested inspection does not exist.");
        }

        if (!$inspection) {
            $checkList = $inspection;
        } else {
            $checkList = new \SafeStartApi\Entity\CheckList();
            $uniqId = uniqid();
            $checkList->setHash($uniqId);
        }

        $checkList->setVehicle($vehicle);
        $checkList->setUser($user);
        $checkList->setFieldsStructure($fieldsStructure);
        $checkList->setFieldsData($fieldsData);
        $checkList->setGpsCoords((isset($this->data->gps) && !empty($this->data->gps)) ? $this->data->gps : null);
        $checkList->setCurrentOdometer((isset($this->data->odometer) && !empty($this->data->odometer)) ? $this->data->odometer : null);
        $checkList->setCurrentOdometerHours((isset($this->data->odometer_hours) && !empty($this->data->oodometer_hours)) ? $this->data->odometer_hours : null);

        $this->em->persist($checkList);
        $this->em->flush();

        // save alerts
        $alerts = array();
        if (!empty($this->data->alerts) && is_array($this->data->alerts)) {
            $alerts = $this->data->alerts;
            foreach ($alerts as $alert) {
                $field = $this->em->find('SafeStartApi\Entity\Field', $alert->fieldId);
                if ($field === null) {
                    continue;
                }
                $newAlert = new \SafeStartApi\Entity\Alert();
                $newAlert->setField($field);
                $newAlert->setCheckList($checkList);
                $newAlert->setDescription(!empty($alert->comment) ? $alert->comment : null);
                $newAlert->setImages(!empty($alert->images) ? $alert->images : array());
                $newAlert->setVehicle($vehicle);
                $this->em->persist($newAlert);
            }
            $this->em->flush();
        }

        $this->answer = array(
            'checklist' => $checkList->getHash(),
        );


        $this->_pushNewChecklistNotification($vehicle, $this->answer, $alerts);

        return $this->AnswerPlugin()->format($this->answer);
    }

    public function getAlertsAction()
    {
        if (!$this->_requestIsValid('vehicle/getalerts')) return $this->_showBadRequest();

        $period = !is_null($this->params('period')) ? $this->params('period') : 0;
        $time = time() - $period;

        if (isset($this->data->id)) {
            $vehicleId = $this->data->id;
            $vehicle = $this->em->find('SafeStartApi\Entity\Vehicle', $vehicleId);

            if (!$vehicle) return $this->_showNotFound("Vehicle not found.");
            if (!$vehicle->haveAccess($this->authService->getStorage()->read())) return $this->_showUnauthorisedRequest();

            $query = $this->em->createQuery('SELECT a FROM SafeStartApi\Entity\Alert a WHERE a.vehicle = ?1 AND a.creation_date > ?2');
            $query->setParameter(1, $vehicle);
            $query->setParameter(2, $time);
            $items = $query->getResult();

        } else {
            $currentUser = \SafeStartApi\Application::getCurrentUser();

            $vehicles = $currentUser->getVehicles();
            $respVehicles = $currentUser->getResponsibleForVehicles();

            $vehicles = !empty($vehicles) ? $vehicles->toArray() : array();
            $respVehicles = !empty($respVehicles) ? $respVehicles->toArray() : array();

            array_merge($vehicles, $respVehicles);

            $query = $this->em->createQuery('SELECT a FROM SafeStartApi\Entity\Alert a WHERE a.vehicle IN (?1) AND a.creation_date > ?2');
            $query->setParameter(1, $vehicles);
            $query->setParameter(2, $time);
            $items = $query->getResult();
        }

        $alerts = array();
        foreach ($items as $item) {
            $alerts[] = $item->toArray();
        }

        $this->answer = array(
            'alerts' => $alerts,
        );

        return $this->AnswerPlugin()->format($this->answer);
    }

    public function getInspectionsAction()
    {

        if (($vehicleId = (int)$this->params('id')) !== null) {
            $vehicle = $this->em->find('SafeStartApi\Entity\Vehicle', $vehicleId);

            $inspections = array();

            $query = $this->em->createQuery("SELECT cl FROM SafeStartApi\Entity\CheckList cl WHERE cl.vehicle = :id");
            $query->setParameters(array('id' => $vehicle));
            $items = $query->getResult();

            if(is_array($items) && !empty($items)) {
                foreach($items as $checkList) {
                    $checkListData = array();

                    $checkListData['checkListId'] = $checkList->getId();
                    $checkListData['title'] = $checkList->getCreationDate()->format("g:i A d/m/y");

                    $inspections[] = $checkListData;
                }
            }

            $this->answer = $inspections;
            return $this->AnswerPlugin()->format($this->answer);
        } else {
            $this->_showBadRequest();
        }
    }

    public function updateAlertAction()
    {
        $alertId = $this->params('alertId');
        $alert = $this->em->find('SafeStartApi\Entity\Alert', $alertId);
        if (!$alert) return $this->_showNotFound("Alert not found.");
        $vehicle = $alert->getVehicle();
        if(!$vehicle->haveAccess($this->authService->getStorage()->read())) return $this->_showUnauthorisedRequest();

        if (isset($this->data->status)) $alert->setStatus($this->data->status);
        if (isset($this->data->new_comment) && !empty($this->data->new_comment)) $alert->addComment($this->data->new_comment);
        $this->em->persist($alert);
        $this->em->flush();

        $this->answer = array(
            'done' => true,
        );

        return $this->AnswerPlugin()->format($this->answer);

    }

    private function _pushNewChecklistNotification(Vehicle $vehicle, $data = array(), $alerts = array())
    {

        $androidDevices = array();
        $iosDevices = array();
        $currentUser = \SafeStartApi\Application::getCurrentUser();
        $responsibleUsers = $vehicle->getResponsibleUsers();
        $vehicleUsers = $vehicle->getUsers();

        foreach ($responsibleUsers as $responsibleUser) {
            if ($currentUser->getId() == $responsibleUser->getId()) continue;
            $responsibleUserInfo = $responsibleUser->toInfoArray();
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

        if (!empty($androidDevices)) $this->PushNotificationPlugin()->android($androidDevices, $data, $alerts, $vehicle);
        if (!empty($iosDevices)) $this->PushNotificationPlugin()->ios($iosDevices, $data, $alerts, $vehicle);

    }
}
