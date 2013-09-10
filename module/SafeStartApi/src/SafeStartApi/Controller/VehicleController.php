<?php

namespace SafeStartApi\Controller;

use SafeStartApi\Base\RestrictedAccessRestController;

class VehicleController extends RestrictedAccessRestController
{

    public function checkPlantIdAction()
    {
        if (!$this->authService->hasIdentity()) return $this->_showUnauthorisedRequest();
        if (!$this->_requestIsValid('vehicle/checkplantid')) return $this->_showBadRequest();

        $plantId = $this->data->plantId;

        $vehRep = $this->em->getRepository('SafeStartApi\Entity\Vehicle');
        $veh = $vehRep->findBy(array('plantId' => $plantId));

        $inDb = !empty($veh);

        $this->answer = array(
            'foundInDatabase' => (bool)$inDb,
        );

        return $this->AnswerPlugin()->format($this->answer);
    }

    public function getListAction()
    {
        if (!$this->authService->hasIdentity()) return $this->_showUnauthorisedRequest();
        if (!$this->_requestIsValid('vehicle/getlist')) return $this->_showBadRequest();

        $user = $this->authService->getIdentity();
        $vehicles = $user->getVehicles();

        $vehiclesList = array();
        foreach($vehicles as $vehicle) {
            $vehiclesList[] = array(
                'vehicleId' => $vehicle->getId(),
                'type' => $vehicle->getType(),
                'vehicleName' => $vehicle->getTitle(),
            );
        }

        $this->answer = array(
            'vehicles' => $vehiclesList,
        );

        return $this->AnswerPlugin()->format($this->answer);
    }

    public function getDataByIdAction()
    {
        if (!$this->authService->hasIdentity()) return $this->_showUnauthorisedRequest();
        if (!$this->_requestIsValid('vehicle/getinfo')) return $this->_showBadRequest();

        $id = (int)$this->params('id');
        $vehRep = $this->em->getRepository('SafeStartApi\Entity\Vehicle');
        $veh = $vehRep->findOneById($id);
        if(empty($veh)) return $this->_showNotFound();

        $vehicleData = $veh->toInfoArray();

        $this->answer = array(
            'vehicleData' => $vehicleData,
        );

        return $this->AnswerPlugin()->format($this->answer);
    }

    public function getChecklistAction()
    {
        if (!$this->authService->hasIdentity()) return $this->_showUnauthorisedRequest();
        if (!$this->_requestIsValid('vehicle/getchecklist')) return $this->_showBadRequest();

        $vehicleId = (int)$this->params('id');
        $vehicle = $this->em->find('SafeStartApi\Entity\Vehicle', $vehicleId);

        if (!$vehicle) return $this->_showNotFound("Vehicle not found.");

        $query = $this->em->createQuery('SELECT f FROM SafeStartApi\Entity\Field f WHERE f.deleted = 0 AND f.enabled = 1 AND f.vehicle = ?1');
        $query->setParameter(1, $vehicle);
        $items = $query->getResult();

        $checklist = $this->GetDataPlugin()->buildChecklist($items);


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
            if(is_array($queryResult) && !empty($queryResult)) {
                if(isset($queryResult[0])) {
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
        if (!$this->authService->hasIdentity()) return $this->_showUnauthorisedRequest();
        //todo: check why bad request with alerts
       // if (!$this->_requestIsValid('vehicle/completechecklist')) return $this->_showBadRequest();

        // save checklist
        $vehicleId = $this->params('id');
        $vehicle = $this->em->find('SafeStartApi\Entity\Vehicle', $vehicleId);
        if (!$vehicle) return $this->_showNotFound("Vehicle not found.");

        $user = $this->authService->getStorage()->read();

        $query = $this->em->createQuery('SELECT f FROM SafeStartApi\Entity\Field f WHERE f.deleted = 0 AND f.enabled = 1 AND f.vehicle = ?1');
        $query->setParameter(1, $vehicle);
        $items = $query->getResult();

        $fieldsStructure = $this->GetDataPlugin()->getChecklistStructure($items);

        $fieldsStructure = json_encode($fieldsStructure);
        $fieldsData = json_encode($this->data->fields);

        $checkList = new \SafeStartApi\Entity\CheckList();
        $checkList->setVehicle($vehicle);
        $checkList->setUser($user);
        $checkList->setFieldsStructure($fieldsStructure);
        $checkList->setFieldsData($fieldsData);
        $checkList->setHash(null);
        $checkList->setGpsCoords((isset($this->data->gps) && !empty($this->data->gps)) ? $this->data->gps : null);

        $this->em->persist($checkList);
        $this->em->flush();

        $md5 = md5($checkList->getId());
        $uniqId = hash('adler32', $md5);
        $uniqId .= hash('crc32', $md5);

        $checkList->setHash($uniqId);
        $this->em->persist($checkList);
        $this->em->flush();

        // save alerts
        if(!empty($this->data->alerts) && is_array($this->data->alerts)) {
            $alerts = $this->data->alerts;
            foreach($alerts as $alert) {
                $field = $this->em->find('SafeStartApi\Entity\Field', $alert->fieldId);
                if($field === null) {
                    continue;
                }
                $newAlert = new \SafeStartApi\Entity\Alert();
                $newAlert->setField($field);
                $newAlert->setCheckList($checkList);
                $newAlert->setDescription(!empty($alert->comment) ? $alert->comment : null);
                $newAlert->setImages(!empty($alert->images) ?  $alert->images : array());
                $this->em->persist($newAlert);
            }
            $this->em->flush();
        }

        $this->answer = array(
            'checklist' => $checkList->getHash(),
        );

        $this->_pushNewChecklistNotification($vehicle, $this->answer);

        return $this->AnswerPlugin()->format($this->answer);
    }

    private function _pushNewChecklistNotification(\SafeStartApi\Entity\Vehicle $vehicle, $data = array()) {

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

        if (!empty($androidDevices)) $this->PushNotificationPlugin()->android($androidDevices, $data);
        if (!empty($iosDevices)) $this->PushNotificationPlugin()->android($iosDevices, $data);

    }
}
