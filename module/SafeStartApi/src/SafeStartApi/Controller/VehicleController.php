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

        $cache = \SafeStartApi\Application::getCache();
        $cashKey = "getUserVehiclesList" . $user->getId();

        $vehiclesList = array();

        if ($cache->hasItem($cashKey)) {
            $vehiclesList = $cache->getItem($cashKey);
        } else {
            $vehicles = $user->getVehicles();

            foreach ($vehicles as $vehicle) {
                $vehicleData = $vehicle->toResponseArray();
                $vehiclesList[] = array(
                    'vehicleId' => $vehicle->getId(),
                    'type' => $vehicle->getType(),
                    'vehicleName' => $vehicle->getTitle(),
                    'projectName' => $vehicle->getProjectName(),
                    'projectNumber' => $vehicle->getProjectNumber(),
                    'expiryDate' => $vehicle->getCompany()->getExpiryDate(),
                    'kmsUntilNext' => $vehicle->getServiceDueKm(),
                    'hoursUntilNext' => $vehicle->getServiceDueHours(),
                    'role' => 'user'
                );
            }

            $responsibleVehicles = $user->getResponsibleForVehicles();
            foreach ($responsibleVehicles as $vehicle) {
                $vehiclesList[] = array(
                    'vehicleId' => $vehicle->getId(),
                    'type' => $vehicle->getType(),
                    'vehicleName' => $vehicle->getTitle(),
                    'projectName' => $vehicle->getProjectName(),
                    'projectNumber' => $vehicle->getProjectNumber(),
                    'expiryDate' => $vehicle->getCompany()->getExpiryDate(),
                    'kmsUntilNext' => $vehicle->getServiceDueKm(),
                    'hoursUntilNext' => $vehicle->getServiceDueHours(),
                    'role' => 'responsible'
                );
            }
            $cache->setItem($cashKey, $vehiclesList);
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

        if ($cache->hasItem($cashKey) && !$inspection) {
            $checklist = $cache->getItem($cashKey);
        } else {
            $query = $this->em->createQuery('SELECT f FROM SafeStartApi\Entity\Field f WHERE f.deleted = 0 AND f.enabled = 1 AND f.vehicle = ?1');
            $query->setParameter(1, $vehicle);
            $items = $query->getResult();
            $checklist = $this->GetDataPlugin()->buildChecklist($items, $inspection);
            if (!$inspection) $cache->setItem($cashKey, $checklist);
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
                        'alerts' => $queryResult[0]->getAlertsArray(),
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

        if ($inspection) {
            $checkList = $inspection;
            $checkList->setPdfLink(NULL);

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
        $checkList->setCurrentOdometerHours((isset($this->data->odometer_hours) && !empty($this->data->odometer_hours)) ? $this->data->odometer_hours : null);

        $this->em->persist($checkList);
        $this->em->flush();

        // delete existing alerts
        if ($inspection) {
            $prevAlerts = $inspection->getAlerts();
            if ($prevAlerts) {
                foreach ($prevAlerts as $prevAlert) {
                    $prevAlert->setDeleted(1);
                    $this->em->flush();
                }
            }
        }

        // save new alerts
        $alerts = array();
        if (!empty($this->data->alerts) && is_array($this->data->alerts)) {
            $alerts = $this->data->alerts;
            foreach ($alerts as $alert) {
                $field = $this->em->find('SafeStartApi\Entity\Field', $alert->fieldId);
                if ($field === null) {
                    continue;
                }
                $addNewAlert = true;
                $filedAlerts = $field->getAlerts();
                if ($filedAlerts) {
                    foreach ($filedAlerts as $filedAlert) {
                        if ($filedAlert->getVehicle()->getId() == $vehicleId
                            && $filedAlert->getStatus() == \SafeStartApi\Entity\Alert::STATUS_NEW
                            && !$filedAlert->getDeleted()
                        ) {
                            $addNewAlert = false;
                            $filedAlert->setCheckList($checkList);
                            if (!empty($alert->comment)) $filedAlert->addComment($alert->comment);
                            if (!empty($alert->images)) $filedAlert->setImages(array_merge((array)$filedAlert->getImages(), (array)$alert->image));
                        }
                    }
                }
                if ($addNewAlert) {
                    $newAlert = new \SafeStartApi\Entity\Alert();
                    $newAlert->setField($field);
                    $newAlert->setCheckList($checkList);
                    $newAlert->setDescription(!empty($alert->comment) ? $alert->comment : null);
                    $newAlert->setImages(!empty($alert->images) ? $alert->images : array());
                    $newAlert->setVehicle($vehicle);
                    $this->em->persist($newAlert);
                }

            }
            $this->em->flush();
        }

        $cache = \SafeStartApi\Application::getCache();
        $cashKey = "getVehicleInspections" . $vehicleId;
        if ($cache->hasItem($cashKey)) $cache->removeItem($cashKey);
        $cashKey = "getAlertsByVehicle" . $vehicleId;
        if ($cache->hasItem($cashKey)) $cache->removeItem($cashKey);
        $cashKey = "getAlertsByCompany" . $vehicle->getCompany()->getId();
        if ($cache->hasItem($cashKey)) $cache->removeItem($cashKey);
        $cashKey = "getCompanyVehiclesList";
        if ($cache->hasItem($cashKey)) $cache->removeItem($cashKey);

        $this->answer = array(
            'checklist' => $checkList->getHash(),
        );

        $this->_pushNewChecklistNotification($vehicle, $alerts);

        return $this->AnswerPlugin()->format($this->answer);
    }

    private function _pushNewChecklistNotification(Vehicle $vehicle, $alerts = array())
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

        $message = '';
        $badge = 0;
        if (!empty($alerts)) {
            $message =
                "Vehicle Alert \n\r" .
                "Vehicle ID#" . $vehicle->getId() . " has a critical error with its: \n\r";
            foreach ($alerts as $alert) {
                $badge++;
                $message .= isset($alert->comment) ? $alert->comment : '' . "\n\r";
            }
        } else {
            $badge = 1;
            $message .= 'Checklist for Vehicle ID #' . $vehicle->getId() . ' added';
        }

        if (!empty($androidDevices)) $this->PushNotificationPlugin()->android($androidDevices, $message, $badge);
        if (!empty($iosDevices)) $this->PushNotificationPlugin()->ios($iosDevices, $message, $badge);
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

            $vehicles = array_merge($vehicles, $respVehicles);

            if(count($vehicles) > 0) {
                $query = $this->em->createQuery('SELECT a FROM SafeStartApi\Entity\Alert a WHERE a.vehicle IN (?1) AND a.creation_date > ?2');
                $query->setParameter(1, $vehicles);
            } else {
                $query = $this->em->createQuery('SELECT a FROM SafeStartApi\Entity\Alert a WHERE a.creation_date > ?2');
            }
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
            $cache = \SafeStartApi\Application::getCache();
            $cashKey = "getVehicleInspections" . $vehicleId;
            if ($cache->hasItem($cashKey)) {
                $inspections = $cache->getItem($cashKey);
            } else {
                $query = $this->em->createQuery("SELECT cl FROM SafeStartApi\Entity\CheckList cl WHERE cl.deleted = 0 AND cl.vehicle = :id");
                $query->setParameters(array('id' => $vehicle));
                $items = $query->getResult();

                if (is_array($items) && !empty($items)) {
                    foreach ($items as $checkList) {
                        $checkListData = $checkList->toArray();

                        $checkListData['checkListId'] = $checkList->getId();
                        $checkListData['title'] = $checkList->getCreationDate()->format($this->moduleConfig['params']['date_format'] .' '. $this->moduleConfig['params']['time_format']);

                        $inspections[] = $checkListData;
                    }
                }
                $cache->setItem($cashKey, $inspections);
            }
            $page = (int)$this->getRequest()->getQuery('page');
            $limit = (int)$this->getRequest()->getQuery('limit');
            if (count($inspections) < ($page - 1) * $limit) {
                $this->answer = array();
                return $this->AnswerPlugin()->format($this->answer);
            }
            $iteratorAdapter = new \Zend\Paginator\Adapter\ArrayAdapter($inspections);
            $paginator = new \Zend\Paginator\Paginator($iteratorAdapter);
            $paginator->setCurrentPageNumber($page ? $page : 1);
            $paginator->setItemCountPerPage($limit ? $limit : 10);
            $items = $paginator->getCurrentItems() ? $paginator->getCurrentItems()->getArrayCopy() : array();
            $this->answer = $items;
            return $this->AnswerPlugin()->format($this->answer);
        } else {
            $this->_showBadRequest();
        }
    }

    public function getInspectionAlertsAction()
    {
        $inspectionId = $this->params('inspectionId');
        $inspection = $this->em->find('SafeStartApi\Entity\CheckList', $inspectionId);
        if (!$inspection) return $this->_showNotFound("Inspection not found.");
        $vehicle = $inspection->getVehicle();
        if (!$vehicle->haveAccess($this->authService->getStorage()->read())) return $this->_showUnauthorisedRequest();
        $this->answer = $inspection->getAlertsArray();
        return $this->AnswerPlugin()->format($this->answer);
    }

    public function updateAlertAction()
    {
        $alertId = $this->params('alertId');
        $alert = $this->em->find('SafeStartApi\Entity\Alert', $alertId);
        if (!$alert) return $this->_showNotFound("Alert not found.");
        $vehicle = $alert->getVehicle();
        if (!$vehicle->haveAccess($this->authService->getStorage()->read())) return $this->_showUnauthorisedRequest();

        if (isset($this->data->status)) $alert->setStatus($this->data->status);
        if (isset($this->data->new_comment) && !empty($this->data->new_comment)) $alert->addComment($this->data->new_comment);
        $this->em->persist($alert);
        $this->em->flush();

        $cache = \SafeStartApi\Application::getCache();
        $cashKey = "getAlertsByVehicle" . $vehicle->getId();
        if ($cache->hasItem($cashKey)) $cache->removeItem($cashKey);
        $cashKey = "getAlertsByCompany" . $vehicle->getCompany()->getId();
        if ($cache->hasItem($cashKey)) $cache->removeItem($cashKey);

        $this->answer = array(
            'done' => true,
        );

        return $this->AnswerPlugin()->format($this->answer);
    }

    public function deleteAlertAction()
    {
        $alertId = $this->params('alertId');
        $alert = $this->em->find('SafeStartApi\Entity\Alert', $alertId);
        if (!$alert) return $this->_showNotFound("Alert not found.");
        $vehicle = $alert->getVehicle();
        if (!$vehicle->haveAccess($this->authService->getStorage()->read())) return $this->_showUnauthorisedRequest();

        $alert->setDeleted(1);

        $this->em->flush();

        $cache = \SafeStartApi\Application::getCache();
        $cashKey = "getCompanyVehiclesList";
        if ($cache->hasItem($cashKey)) $cache->removeItem($cashKey);
        $cashKey = "getAlertsByVehicle" . $vehicle->getId();
        if ($cache->hasItem($cashKey)) $cache->removeItem($cashKey);
        $cashKey = "getAlertsByCompany" . $vehicle->getCompany()->getId();
        if ($cache->hasItem($cashKey)) $cache->removeItem($cashKey);

        $this->answer = array(
            'done' => true
        );

        return $this->AnswerPlugin()->format($this->answer);
    }

    public function deleteInspectionAction()
    {
        $inspectionId = $this->params('inspectionId');
        $inspection = $this->em->find('SafeStartApi\Entity\CheckList', $inspectionId);
        if (!$inspection) return $this->_showNotFound("Inspection not found.");
        $vehicle = $inspection->getVehicle();
        if (!$vehicle->haveAccess($this->authService->getStorage()->read())) return $this->_showUnauthorisedRequest();

        $inspection->setDeleted(1);
        $this->em->flush();

        // delete existing alerts
        if ($inspection) {
            $prevAlerts = $inspection->getAlerts();
            if ($prevAlerts) {
                foreach ($prevAlerts as $prevAlert) {
                    $prevAlert->setDeleted(1);
                    $this->em->flush();
                }
            }
        }

        $cache = \SafeStartApi\Application::getCache();
        $cashKey = "getVehicleInspections" . $vehicle->getId();
        if ($cache->hasItem($cashKey)) $cache->removeItem($cashKey);
        $cashKey = "getCompanyVehiclesList";
        if ($cache->hasItem($cashKey)) $cache->removeItem($cashKey);
        $cashKey = "getAlertsByVehicle" . $vehicle->getId();
        if ($cache->hasItem($cashKey)) $cache->removeItem($cashKey);
        $cashKey = "getAlertsByCompany" . $vehicle->getCompany()->getId();
        if ($cache->hasItem($cashKey)) $cache->removeItem($cashKey);

        $this->answer = array(
            'done' => true
        );

        return $this->AnswerPlugin()->format($this->answer);
    }

    public function getStatisticAction()
    {
        $vehicleId = (int)$this->params('id');
        $vehicle = $this->em->find('SafeStartApi\Entity\Vehicle', $vehicleId);
        if (!$vehicle) return $this->_showNotFound("Vehicle not found.");
        if (!$vehicle->haveAccess($this->authService->getStorage()->read())) return $this->_showUnauthorisedRequest();

        $from = null;
        if (isset($this->data->form) && !empty($this->data->form)) {
            $from = new \DateTime();
            $from->setTimestamp((int)$this->data->form);
        }

        $to = null;
        if (isset($this->data->to) && !empty($this->data->to)) {
            $to = new \DateTime();
            $to->setTimestamp((int)$this->data->to);
        }

        $this->answer = array(
            'done' => true,
            'statistic' => $vehicle->getStatistic($from, $to)
        );

        return $this->AnswerPlugin()->format($this->answer);
    }
}
