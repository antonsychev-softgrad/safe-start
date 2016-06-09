<?php

namespace SafeStartApi\Controller;

use SafeStartApi\Base\RestrictedAccessRestController;
use SafeStartApi\Entity\Vehicle;
use SafeStartApi\Entity\Alert;
use SafeStartApi\Entity\ServiceReport;
use SafeStartApi\Application;

class VehicleController extends RestrictedAccessRestController
{
  const MONITOR_DAYS = 60;

  public function getListAction()
  {
    if (!$this->_requestIsValid('vehicle/getlist')) return $this->_showBadRequest();

    $user = $this->authService->getIdentity();

    $cache = \SafeStartApi\Application::getCache();
    $cashKey = "getUserVehiclesList" . $user->getId();

    $vehiclesList = array();

    /* if ($cache->hasItem($cashKey)) {
         $vehiclesList = $cache->getItem($cashKey);
     } else {*/
    $vehicles = array();

    if ($user->getRole() === 'companyAdmin') {
      $company = $user->getCompany();
      if ($company) {
        $vehicles = $company->getVehicles();
      }
    } else {
      $vehicles = $user->getVehicles();
    }

    foreach ($vehicles as $vehicle) {
      $vehicleData = $vehicle->toResponseArray();
      $vehicleInfo = array(
        'vehicleId' => $vehicleData['vehicleId'],
        'type' => $vehicleData['type'],
        'vehicleName' => $vehicleData['vehicleName'],
        'projectName' => $vehicleData['projectName'],
        'projectNumber' => $vehicleData['projectNumber'],
        'expiryDate' => $vehicleData['expiryDate'],
        'kmsUntilNext' => $vehicleData['kmsUntilNext'],
        'hoursUntilNext' => $vehicleData['hoursUntilNext'],
        'role' => 'user'
      );
      $vehiclesList[] = array_merge($vehicleInfo, $vehicle->toInfoArray());
    }

    $responsibleVehicles = $user->getResponsibleForVehicles();
    foreach ($responsibleVehicles as $vehicle) {
      $vehicleData = $vehicle->toResponseArray();
      $vehicleInfo = array(
        'vehicleId' => $vehicleData['vehicleId'],
        'type' => $vehicleData['type'],
        'vehicleName' => $vehicleData['vehicleName'],
        'projectName' => $vehicleData['projectName'],
        'projectNumber' => $vehicleData['projectNumber'],
        'expiryDate' => $vehicleData['expiryDate'],
        'kmsUntilNext' => $vehicleData['kmsUntilNext'],
        'hoursUntilNext' => $vehicleData['hoursUntilNext'],
        'role' => 'responsible'
      );
      $vehiclesList[] = array_merge($vehicleInfo, $vehicle->toInfoArray());
    }
    $cache->setItem($cashKey, $vehiclesList);
    /* }*/

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
//
//    if ($cache->hasItem($cashKey) && !$inspection) {
//      $checklist = $cache->getItem($cashKey);
//    } else {
      $query = $this->em->createQuery('SELECT f FROM SafeStartApi\Entity\Field f WHERE f.deleted = 0 AND f.enabled = 1 AND f.vehicle = ?1 ORDER BY f.order ASC');
      $query->setParameter(1, $vehicle);
      $items = $query->getResult();
      $checklist = $this->GetDataPlugin()->buildChecklist($items, $inspection);
      if (!$inspection) $cache->setItem($cashKey, $checklist);
//    }

    $cashKey = "getNewAlertsByVehicle" . $vehicle->getId();
    $alerts = array();
    $filters = array();

    $filters['status'] = \SafeStartApi\Entity\Alert::STATUS_NEW;

    if ($cache->hasItem($cashKey)) {
      $alerts = $cache->getItem($cashKey);
    } else {
      $checkLists = $vehicle->getCheckLists();
      if (!empty($checkLists)) {
        foreach ($checkLists as $checkList) {
          $alerts = array_merge($alerts, $checkList->getAlertsArray($filters));
        }
      }
      $cache->setItem($cashKey, $alerts);
    }

    $alerts = array_reverse($alerts);
    //check expiry date of vehicle
    $curDate = new \DateTime();
    if ((($vehicle->getExpiryDate() - $curDate->getTimestamp()) / (60 * 60 * 24) < 1)) {
      $alert = $this->checkCurrentInfoAlerts($vehicle, \SafeStartApi\Entity\Alert::EXPIRY_DATE);
      array_unshift($alerts, $alert);
    }
    $dueForServiceAlertTrigger = false;
    if (count($vehicle->checkLists) > 2) {
      $serviceDate = null;
      if ($vehicle->getNextServiceDay()) $serviceDate = \DateTime::createFromFormat('d/m/Y', $vehicle->getNextServiceDay());
      if ($serviceDate && (($serviceDate->getTimestamp() - $curDate->getTimestamp()) / (60 * 60 * 24) < 1)) {
        $alert = $this->checkCurrentInfoAlerts($vehicle, \SafeStartApi\Entity\Alert::DUE_SERVICE);
        array_unshift($alerts, $alert);
        $dueForServiceAlertTrigger = true;
      }
    }
    if (($vehicle->getCurrentOdometerKms() > $vehicle->getServiceDueKm()) || ($vehicle->getCurrentOdometerHours() > $vehicle->getServiceDueHours())) {
      $alert = $this->checkCurrentInfoAlerts($vehicle, \SafeStartApi\Entity\Alert::INACCURATE_KM_HR);
      if(!$dueForServiceAlertTrigger){
          $alertService = $this->checkCurrentInfoAlerts($vehicle, \SafeStartApi\Entity\Alert::DUE_SERVICE);
          if($alertService){
              array_unshift($alerts, $alertService);
          }
      }
      array_unshift($alerts, $alert);
    }
    //end of expiry date check functionality

    $this->answer = array(
      'checklist' => $checklist,
      'alerts' => $alerts
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
            'hash' => $queryResult[0]->getHash(),
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
    //if (!$this->_requestIsValid('vehicle/completechecklist')) return $this->_showBadRequest();
    if (!isset($this->data->fields)) return $this->_showBadRequest();

    // save checklist
    $vehicleId = $this->params('id');
    $vehicle = $this->em->find('SafeStartApi\Entity\Vehicle', $vehicleId);

    if (!$vehicle) return $this->_showNotFound("Vehicle not found.");
    if (!$vehicle->haveAccess($this->authService->getStorage()->read())) return $this->_showUnauthorisedRequest();

    $user = $this->authService->getStorage()->read();

    $cache = \SafeStartApi\Application::getCache();
    $cashKey = "getVehicleChecklistFieldsStructure" . $vehicle->getId();

    if ($cache->hasItem($cashKey)) {
      $fieldsStructure = $cache->getItem($cashKey);
    } else {
      $query = $this->em->createQuery('SELECT f FROM SafeStartApi\Entity\Field f WHERE f.deleted = 0 AND f.enabled = 1 AND f.vehicle = ?1');
      $query->setParameter(1, $vehicle);
      $items = $query->getResult();
      $fieldsStructure = $this->GetDataPlugin()->getChecklistStructure($items);
      $cache->setItem($cashKey, $fieldsStructure);
    }

    $inspection = null;
    $checklistId = (int)$this->getRequest()->getQuery('checklistId'); //todo: also check by hash
    if ($checklistId) {
      $inspection = $this->em->find('SafeStartApi\Entity\CheckList', $checklistId);
      if (!$inspection) return $this->_showNotFound("Requested inspection does not exist.");
    }

    if ($inspection) {
      $checkList = $inspection;
      $checkList->setPdfLink(NULL);
      $checkList->setFaultPdfLink(NULL);
      $checkList->clearWarnings();
    } else {
      $checkList = new \SafeStartApi\Entity\CheckList();
      $checkList->setCurrentServiceDueKms($vehicle->getServiceDueKm());
      $checkList->setCurrentServiceDueHours($vehicle->getServiceDueHours());
      $uniqId = uniqid();
      $checkList->setHash($uniqId);
    }

    $checkList->setVehicle($vehicle);
    $checkList->setUser($user);
    $checkList->setFieldsStructure(json_encode($fieldsStructure));
    $checkList->setFieldsData(json_encode($this->data->fields));
    $checkList->setGpsCoords((isset($this->data->gps) && !empty($this->data->gps)) ? $this->data->gps : null);
    $checkList->setLocation((isset($this->data->location) && !empty($this->data->location)) ? $this->data->location : null);

    if (isset($this->data->operator_name) && !empty($this->data->operator_name)) $checkList->setOperatorName($this->data->operator_name);
    else $checkList->setOperatorName($user->getFullName());

    // set usage warning
    if ($vehicle->getInspectionDueKms() && $vehicle->getInspectionDueHours() && (isset($this->data->odometer) && !empty($this->data->odometer))) {
      if (!$inspection) $lastInspectionDay = $vehicle->getLastInspectionDay();
      else $lastInspectionDay = $vehicle->getPrevInspectionDay();
      if ($lastInspectionDay) {
        $interval = time() - $vehicle->getLastInspectionDay();
        $intervals = ($interval / (60 * 60)) / $vehicle->getInspectionDueHours();
      } else {
        $intervals = 1;
      }
      $maxKms = $intervals * $vehicle->getInspectionDueKms();

      if ($maxKms < $this->data->odometer) {
        $checkList->addWarning(\SafeStartApi\Entity\CheckList::WARNING_DATA_INCORRECT);
      }
    }

    $warningServiceDue = false;
    // set current odometer data and check service due
    if ((isset($this->data->odometer) && !empty($this->data->odometer))) {
      $warningKms = false;
      $checkList->setCurrentOdometer($this->data->odometer);
      if ($this->data->odometer < $vehicle->getCurrentOdometerKms()) {
        $warningKms = true;
        $checkList->addWarning(\SafeStartApi\Entity\CheckList::WARNING_DATA_DISCREPANCY_KMS);
      }
      $vehicle->setCurrentOdometerKms($this->data->odometer);

      if ($vehicle->getCurrentOdometerKms() >= $vehicle->getServiceDueKm() - $vehicle->getServiceThresholdKm()) {
        $warningServiceDue = true;
        $checkList->addWarning(\SafeStartApi\Entity\CheckList::WARNING_SERVICE_DUE);
      }

    } else {
      $checkList->setCurrentOdometer($vehicle->getCurrentOdometerKms());
    }
    if ((isset($this->data->odometer_hours) && !empty($this->data->odometer_hours))) {
      $warningHours = false;
      $checkList->setCurrentOdometerHours($this->data->odometer_hours);
      if ($this->data->odometer_hours < $vehicle->getCurrentOdometerHours()) {
        $warningHours = true;
        $checkList->addWarning(\SafeStartApi\Entity\CheckList::WARNING_DATA_DISCREPANCY_HOURS);
      }
      if (
        !$warningHours
        && $vehicle->getLastInspectionDay()
        && (($this->data->odometer_hours - $vehicle->getCurrentOdometerHours()) < ($checkList->getCreationDate()->getTimestamp() - $vehicle->getLastInspectionDay()))
      ) {
        $warningHours = true;
        $checkList->addWarning(\SafeStartApi\Entity\CheckList::WARNING_DATA_DISCREPANCY_HOURS);
      }
      $vehicle->setCurrentOdometerHours($this->data->odometer_hours);

      if (!$warningServiceDue && $vehicle->getCurrentOdometerHours() >= $vehicle->getServiceDueHours() - $vehicle->getServiceThresholdHours()) {
        $checkList->addWarning(\SafeStartApi\Entity\CheckList::WARNING_SERVICE_DUE);
      }
    }else {
      $checkList->getCurrentOdometerHours($vehicle->getCurrentOdometerHours());
    }

    if (!$checkList->getEmailMode()) {
      $warnings = $this->processChecklistPlugin()->getWarningsFromInspectionFields($checkList);
    }
    if (!empty($warnings)) $checkList->setWarnings($warnings);

      $vehiclePlugin = $this->processAdditionalVehiclePlugin();
      $trailerPlantId = $vehiclePlugin->getVehiclePlantIdFromChecklist($checkList, $vehiclePlugin::TRAILER);
      $repository = $this->em->getRepository('SafeStartApi\Entity\Vehicle');
      $trailer = $repository->findOneBy(array(
          'plantId' => $trailerPlantId,
          'deleted' => 0,
      ));
//      if($trailer){
//          return $this->_showKeyExists('Vehicle with such Plant ID already exists. Please specify a new one for Trailer.');
//      }

      $auxMotorPlantId = $vehiclePlugin->getVehiclePlantIdFromChecklist($checkList, $vehiclePlugin::AUXILIARY_MOTOR);
      $auxMotor = $repository->findOneBy(array(
          'plantId' => $auxMotorPlantId,
          'deleted' => 0,
      ));
//      if($auxMotor){
//          return $this->_showKeyExists('Vehicle with such Plant ID already exists. Please specify a new one for Auxiliary motor.');
//      }

      if (!$inspection) $this->em->persist($checkList);

    // save vehicle fields
    if(isset($this->data->vehicleFields)){
      foreach($this->data->vehicleFields as $vehicleField){
        $field = $this->em->find('SafeStartApi\Entity\VehicleField', $vehicleField->id);
        if($field){
          $field->setDefaultValue($vehicleField->value);
          $this->em->persist($field);
        }
      }
    }

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
    $newAlerts = array();
    if (!empty($this->data->alerts) && is_array($this->data->alerts)) {
      $alerts = $this->data->alerts;
      foreach ($alerts as $alert) {
        $field = null;
        if ($alert->fieldId > 0) {
            $field = $this->em->find('SafeStartApi\Entity\Field', $alert->fieldId);
        }
        if ($field === null) {

          if ($alert->fieldId == 0)
          {
              $newAlert = new \SafeStartApi\Entity\Alert();
              $newAlert->setCheckList($checkList);
              $newAlert->setDefaultsForRenew();
              $newAlert->setFaultReport(true);
              $newAlert->setVehicle($vehicle);
              $newAlert->setDescription(!empty($alert->comment) ? $alert->comment : null);
              $newAlert->setImages(!empty($alert->images) ? $alert->images : array());
              $dueDate = new \DateTime();
              $dueDate->add(new \DateInterval('P'. ($alert->iscritical ? '7' : '14'). 'D'));
              $newAlert->setDueDate($dueDate);

              $this->em->persist($newAlert);
              if (!empty($alert->faultdescription))
              {
                  $this->em->flush();

                  $newAlert->addComment($alert->faultdescription);
                  $this->em->persist($newAlert);
              }
              $newAlerts[] = $newAlert;
          }

          continue;
        }
        $addNewAlert = true;
        $filedAlerts = $field->getAlerts();
        if ($filedAlerts) {
          foreach ($filedAlerts as $filedAlert) {
            if ($filedAlert->getVehicle()
              && $filedAlert->getVehicle()->getId() == $vehicleId
              && $filedAlert->getStatus() == \SafeStartApi\Entity\Alert::STATUS_NEW
              && !$filedAlert->getDeleted()
            ) {
              $addNewAlert = false;
              $filedAlert->setCheckList($checkList);
              if (!empty($alert->comment)) $filedAlert->addComment($alert->comment);
              if (!empty($alert->images)) $filedAlert->setImages(array_merge((array)$filedAlert->getImages(), (array)$alert->images));
              $filedAlert->addHistoryItem(\SafeStartApi\Entity\Alert::ACTION_REFRESHED);
              $filedAlert->setDefaultsForRenew();
              $newAlerts[] = $filedAlert;
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
          $newAlert->setDefaultsForRenew();
          $this->em->persist($newAlert);
          $newAlerts[] = $newAlert;
        }

      }

      $this->em->flush();

      $reminderPlugin = $this->AlertReminderPlugin();
      foreach ($newAlerts as $newAlert)
      {
          $reminderPlugin->sendReminder($newAlert, 'initialalertmail.phtml');
      }
    }

    $cache = \SafeStartApi\Application::getCache();
    $cashKey = "getVehicleInspections" . $vehicleId;
    if ($cache->hasItem($cashKey)) $cache->removeItem($cashKey);
    $cashKey = "getAlertsByVehicle" . $vehicleId;
    if ($cache->hasItem($cashKey)) $cache->removeItem($cashKey);
    $cashKey = "getAlertsByCompany" . $vehicle->getCompany()->getId();
    if ($cache->hasItem($cashKey)) $cache->removeItem($cashKey);
    $cashKey = "getCompanyVehicles" . $vehicle->getCompany()->getId();
    if ($cache->hasItem($cashKey)) $cache->removeItem($cashKey);
    $cashKey = "getNewAlertsByVehicle" . $vehicle->getId();
    if ($cache->hasItem($cashKey)) $cache->removeItem($cashKey);

//    $vehiclePlugin = $this->processAdditionalVehiclePlugin();
//    $trailerPlantId = $vehiclePlugin->getVehiclePlantIdFromChecklist($checkList, $vehiclePlugin::TRAILER);
//    $repository = $this->em->getRepository('SafeStartApi\Entity\Vehicle');
//    $trailer = $repository->findOneBy(array(
//      'plantId' => $trailerPlantId,
//      'deleted' => 0,
//    ));
//    if($trailer){
//        return $this->_showKeyExists('Vehicle with such Plant ID already exists');
//    }
    $company = $vehicle->getCompany();

    $vehicleLimitReached = false;
    if (!$trailer) {
      if ($company->getRestricted()
        && ((count($company->getVehicles()) + 1) > $company->getMaxVehicles())
      ) {
        $vehicleLimitReached = $vehiclePlugin->processHiddenVehicle($checkList, $newAlerts, $vehiclePlugin::TRAILER);
      } else {
        $vehiclePlugin->processVehicle(null, $checkList, $newAlerts, $vehiclePlugin::TRAILER);
      }
    } else {
      $vehiclePlugin->processVehicle($trailer, $checkList, $newAlerts, $vehiclePlugin::TRAILER);
    }

    //aux motor as separate vehicle
//    $auxMotorPlantId = $vehiclePlugin->getVehiclePlantIdFromChecklist($checkList, $vehiclePlugin::AUXILIARY_MOTOR);
//    $auxMotor = $repository->findOneBy(array(
//      'plantId' => $auxMotorPlantId,
//      'deleted' => 0,
//    ));
//      if($auxMotor){
//          return $this->_showKeyExists('Vehicle with such Plant ID already exists');
//      }
      if (!$auxMotor) {
      if ($company->getRestricted()
        && ((count($company->getVehicles()) + 1) > $company->getMaxVehicles())
      ) {
        $vehicleLimitReached = $vehiclePlugin->processHiddenVehicle($checkList, $newAlerts, $vehiclePlugin::AUXILIARY_MOTOR);
      } else {
        $vehiclePlugin->processVehicle(null, $checkList, $newAlerts, $vehiclePlugin::AUXILIARY_MOTOR);
      }
    } else {
      $vehiclePlugin->processVehicle($auxMotor, $checkList, $newAlerts, $vehiclePlugin::AUXILIARY_MOTOR);
    }
    //aux end

    $this->processChecklistPlugin()->sendCheckListEmails($checkList);

    $this->answer = array(
      'checklist' => $checkList->getHash(),
    );

    if (empty($newAlerts) && $inspection) return $this->AnswerPlugin()->format($this->answer);

//        if () {
//            \Resque::enqueue('new_checklist_uploaded', '\SafeStartApi\Jobs\NewDbCheckListUploaded', array(
//                'checkListId' => $checkList->getId()
//            ));
//        } else {
    $this->processChecklistPlugin()->pushNewChecklistNotification($checkList);
    $this->processChecklistPlugin()->setInspectionStatistic($checkList);
//        }

    if ($vehicleLimitReached) {
      return $this->_showVehicleLimitReached();
    }

    return $this->AnswerPlugin()->format($this->answer);
  }

  public function getAlertsAction()
  {
    if (!$this->_requestIsValid('vehicle/getalerts')) return $this->_showBadRequest();
//    $date = new \DateTime('07-07-2014');
//    $tm = $date->getTimestamp();
        $period = isset($this->data->period) ? (int)$this->data->period : 0;
//    $period = isset($tm) ? (int)$tm : 0;
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
      $currentUser = $this->authService->getIdentity();
      if($currentUser->getRole() == 'companyAdmin'){
        $company = $currentUser->getCompany();
          if($company){
              $data = array();
              $query = $this->em->createQuery('SELECT v FROM SafeStartApi\Entity\Vehicle v WHERE v.deleted = 0 AND v.company = ?1');
              $query->setParameter(1, $company);
              $vehicles = $query->getResult();
              if (!empty($vehicles)) {
                  foreach ($vehicles as $vehicle) {
                      if ($vehicle->haveAccess($this->authService->getStorage()->read())) {
                          $data = array_merge($data, $this->getAlertsByVehicle($vehicle, $filters = array()));
                      }
                  }
              }

              $this->answer = array(
                  'alerts' => $data,
              );
              return $this->AnswerPlugin()->format($this->answer);
          }
      }
      $vehicles = $currentUser->getVehicles();
      $respVehicles = $currentUser->getResponsibleForVehicles();
      $vehicles = !empty($vehicles) ? $vehicles->toArray() : array();
      $respVehicles = !empty($respVehicles) ? $respVehicles->toArray() : array();
      $vehicles = array_merge($vehicles, $respVehicles);
      if (count($vehicles) > 0) {
        $query = $this->em->createQuery('SELECT a FROM SafeStartApi\Entity\Alert a WHERE a.vehicle IN (?1) AND a.creation_date > ?2 AND a.deleted = 0 ORDER BY a.creation_date DESC');
        $query->setParameter(1, $vehicles);
        $query->setParameter(2, $time);
        $items = $query->getResult();
      } else {
        $items = array();
      }
    }

    $alerts = array();
    if (!empty($items)) {
      foreach ($items as $item) {
        $alerts[] = $item->toArray();
      }
    }

    $this->answer = array(
      'alerts' => $alerts,
    );

    return $this->AnswerPlugin()->format($this->answer);
  }

  public function getInspectionsAction()
  {
    if (!($vehicleId = (int)$this->params('id'))) {
      $this->_showBadRequest();
      return;
    }
    $vehicle = $this->em->find('SafeStartApi\Entity\Vehicle', $vehicleId);
    $inspections = array();
    $cache = \SafeStartApi\Application::getCache();
    $cashKey = "getVehicleInspections" . $vehicleId;
    /* if ($cache->hasItem($cashKey)) {
         $inspections = $cache->getItem($cashKey);
     } else {*/
    $query = $this->em->createQuery("SELECT cl FROM SafeStartApi\Entity\CheckList cl WHERE cl.deleted = 0 AND cl.vehicle = :id");
    $query->setParameters(array('id' => $vehicle));
    $items = $query->getResult();

    if (is_array($items) && !empty($items)) {
      foreach ($items as $checkList) {
        $checkListData = $checkList->toArray();

        $checkListData['checkListId'] = $checkList->getId();
        $checkListData['title'] = $checkList->getCreationDate()->format($this->moduleConfig['params']['date_format'] . ' ' . $this->moduleConfig['params']['time_format']);

        $warnings = $checkList->getWarnings();
        $vehicle = $checkList->getVehicle();
        if ($vehicle->getNextServiceDay()) {
          $date = \DateTime::createFromFormat('d/m/Y', $vehicle->getNextServiceDay());
          if ($date) {
            $nextServiceDate = $date->getTimestamp();
            $days = ($nextServiceDate - $checkList->getCreationDate()->getTimestamp()) / (60 * 60 * 24);
            if ($days < 1) {
              $warnings[] = array(
                'action' => 'next_service_due',
                'text' => 'Estimated Date of Next Service Is ' . $vehicle->getNextServiceDay(),
              );
            } else if ($days < 30) {
              $warnings[] = array(
                'action' => 'next_service_due',
                'text' => 'Next service In ' . ceil($days) . ' Days',
              );
            }
          }
        }
        if ($vehicle->getCompany()) {
          if ($vehicle->getExpiryDate()) {
            $days = ($vehicle->getExpiryDate() - $checkList->getCreationDate()->getTimestamp()) / (60 * 60 * 24);
            if ($days < 1) {
              $warnings[] = array(
                'action' => 'subscription_ending',
                'text' => Alert::EXPIRY_DATE,
              );
            } else if ($days < 30) {
              $warnings[] = array(
                'action' => 'subscription_ending',
                'text' => sprintf('Vehicle registration expires in %d days', ceil($days)),
              );
            }
          }
        }
        $checkListData['warnings'] = $warnings;

        $inspections[] = $checkListData;
      }
    }
    /*         $cache->setItem($cashKey, $inspections);
         }*/
    $page = (int)$this->getRequest()->getQuery('page');
    $limit = (int)$this->getRequest()->getQuery('limit');
    $inspections = array_reverse($inspections);
    if (count($inspections) <= ($page - 1) * $limit) {
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
    $alertId = (int)$this->params('alertId');
    $alert = $this->em->find('SafeStartApi\Entity\Alert', $alertId);
    if (!$alert) return $this->_showNotFound("Alert not found.");
    $vehicle = $alert->getVehicle();
    if (!$vehicle->haveAccess($this->authService->getStorage()->read())) return $this->_showUnauthorisedRequest();

    $done = true;

    if (isset($this->data->status)) {
      if ($alert->getStatus() != $this->data->status) {
        switch ($this->data->status) {
          case \SafeStartApi\Entity\Alert::STATUS_NEW:
            $alert->addHistoryItem(\SafeStartApi\Entity\Alert::ACTION_STATUS_CHANGED_NEW);
            break;
          case \SafeStartApi\Entity\Alert::STATUS_CLOSED:
            $alert->addHistoryItem(\SafeStartApi\Entity\Alert::ACTION_STATUS_CHANGED_CLOSED);
            break;
        }
      }
      $alert->setStatus($this->data->status);
    } else if (isset($this->data->dueDate)) {
        if ($alert->getMonitor() == 0)
        {
            $prev_value = clone $alert->getDueDate();
            $next_value = new \DateTime();
            $next_value->setTimestamp($this->data->dueDate);
            $alert->addHistoryItem(\SafeStartApi\Entity\Alert::ACTION_FAULT_RECTIFICATION_EXTEND, array(
                'prev_value' => $prev_value->format('Y-m-d'),
                'next_value' => $next_value->format('Y-m-d'),
            ));
            $alert->setDueDate($next_value);
            $alert->setMailStatus(\SafeStartApi\Entity\Alert::MAIL_STATUS_SENT_INITIAL);
        } else {
            $done = false;
        }
    } else if (isset($this->data->action) && $this->data->action == 'monitor') {
        $alert->addHistoryItem(\SafeStartApi\Entity\Alert::ACTION_FAULT_RECTIFICATION_MONITOR);
        // $dueDate = clone $alert->getCreationDate();
        $dueDate = clone $alert->getDueDate();
        $dueDate->add(new \DateInterval('P'. self::MONITOR_DAYS. 'D'));
        $alert->setDueDate($dueDate);
        $alert->setMonitor(1);
        $alert->setMailStatus(\SafeStartApi\Entity\Alert::MAIL_STATUS_SENT_INITIAL);
    }

    if (isset($this->data->new_comment) && !empty($this->data->new_comment)) $alert->addComment($this->data->new_comment);
    $this->em->persist($alert);
    $this->em->flush();

    $cache = \SafeStartApi\Application::getCache();
    $cashKey = "getAlertsByVehicle" . $vehicle->getId();
    if ($cache->hasItem($cashKey)) $cache->removeItem($cashKey);
    $cashKey = "getAlertsByCompany" . $vehicle->getCompany()->getId();
    if ($cache->hasItem($cashKey)) $cache->removeItem($cashKey);

    $openAlertsCount = count($vehicle->getOpenAlerts());

    $this->answer = array(
      'done' => $done,
      'openAlertsCount' => $openAlertsCount,
    );

    return $this->AnswerPlugin()->format($this->answer);
  }

  public function deleteAlertAction()
  {
    $alertId = isset($this->data->id) ? (int)$this->data->id : (int)$this->params('alertId');
    $alert = $this->em->find('SafeStartApi\Entity\Alert', $alertId);
    if (!$alert) return $this->_showNotFound("Alert not found.");
    $vehicle = $alert->getVehicle();
    if (!$vehicle->haveAccess($this->authService->getStorage()->read())) return $this->_showUnauthorisedRequest();

    $alert->setDeleted(1);

    $this->em->flush();

    $cache = \SafeStartApi\Application::getCache();
    $cashKey = "getCompanyVehicles" . $vehicle->getCompany()->getId();
    if ($cache->hasItem($cashKey)) $cache->removeItem($cashKey);
    $cashKey = "getAlertsByVehicle" . $vehicle->getId();
    if ($cache->hasItem($cashKey)) $cache->removeItem($cashKey);
    $cashKey = "getAlertsByCompany" . $vehicle->getCompany()->getId();
    if ($cache->hasItem($cashKey)) $cache->removeItem($cashKey);

    $openAlertsCount = count($vehicle->getOpenAlerts());

    $this->answer = array(
      'done' => true,
      'openAlertsCount' => $openAlertsCount,
    );

    return $this->AnswerPlugin()->format($this->answer);
  }

  public function deleteInspectionAction()
  {
    $inspectionId = isset($this->data->id) ? (int)$this->data->id : (int)$this->params('inspectionId');
    $repository = $this->em->getRepository('SafeStartApi\Entity\CheckList');
    $inspection = $repository->find($inspectionId);
    if (!$inspection) {
      $inspection = $repository->findOneBy(array(
        'hash' => $inspectionId,
      ));
      if (!$inspection) return $this->_showNotFound("Inspection not found.");
    }
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
    $cashKey = "getCompanyVehicles" . $vehicle->getCompany()->getId();
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
    if (isset($this->data->from) && !empty($this->data->from)) {
      $from = new \DateTime();
      $from->setTimestamp((int)$this->data->from);
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

  public function getAlertsStatisticAction()
  {
    $vehicleId = (int)$this->params('id');

    $vehicle = $this->em->find('SafeStartApi\Entity\Vehicle', $vehicleId);
    if (!$vehicle) return $this->_showNotFound("Vehicle not found.");
    if (!$vehicle->haveAccess($this->authService->getStorage()->read())) return $this->_showUnauthorisedRequest();

    $from = null;
    if (isset($this->data->from) && !empty($this->data->from)) {
      $from = new \DateTime();
      $from->setTimestamp((int)$this->data->from);
    }

    $to = null;
    if (isset($this->data->to) && !empty($this->data->to)) {
      $to = new \DateTime();
      $to->setTimestamp((int)$this->data->to);
    }

    $this->answer = array(
      'done' => true,
      'alerts' => $vehicle->getAlertsByPeriod($from, $to)
    );

    return $this->AnswerPlugin()->format($this->answer);
  }

  public function getInspectionBreakdownsStatisticAction()
  {
    $vehicleId = (int)$this->params('id');

    $vehicle = $this->em->find('SafeStartApi\Entity\Vehicle', $vehicleId);
    if (!$vehicle) return $this->_showNotFound("Vehicle not found.");
    if (!$vehicle->haveAccess($this->authService->getStorage()->read())) return $this->_showUnauthorisedRequest();

    $from = null;
    if (isset($this->data->from) && !empty($this->data->from)) {
      $from = new \DateTime();
      $from->setTimestamp((int)$this->data->from);
    }

    $to = null;
    if (isset($this->data->to) && !empty($this->data->to)) {
      $to = new \DateTime();
      $to->setTimestamp((int)$this->data->to);
    }

    $range = 'monthly';
    if (isset($this->data->range) && !empty($this->data->range)) {
      $range = $this->data->range;
    }

    $this->answer = array(
      'done' => true,
      'chart' => $vehicle->getInspectionBreakdowns($from, $to, $range)
    );

    return $this->AnswerPlugin()->format($this->answer);
  }

  public function printStatisticAction()
  {
    $vehicleId = (int)$this->params('id');

    $vehicle = $this->em->find('SafeStartApi\Entity\Vehicle', $vehicleId);
    if (!$vehicle) return $this->_showNotFound("Vehicle not found.");
    if (!$vehicle->haveAccess($this->authService->getStorage()->read())) return $this->_showUnauthorisedRequest();

    $from = null;
    if ((int)$this->params('from')) {
      $from = new \DateTime();
      $from->setTimestamp((int)$this->params('from'));
    }

    $to = null;
    if ((int)$this->params('to')) {
      $to = new \DateTime();
      $to->setTimestamp((int)$this->params('to'));
    }

    $pdf = $this->vehicleReportPdf()->create($vehicle, $from, $to);

    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename={$pdf['name']}");
    header("Content-Transfer-Encoding:Binary");
    header('Content-Length: ' . filesize($pdf['path']));
    echo file_get_contents($pdf['path']);
    return true;
  }

    public function exportAction()
    {
        $vehicleId = (int)$this->params('id');

        $vehicle = $this->em->find('SafeStartApi\Entity\Vehicle', $vehicleId);
        if (!$vehicle)
            return $this->_showNotFound("Vehicle not found.");
        if (!$vehicle->haveAccess($this->authService->getStorage()->read()))
            return $this->_showUnauthorisedRequest();

        $from = new \DateTime();
        $from->setTimestamp((int)$this->params('from', 0));

        $to = new \DateTime();
        $to->setTimestamp((int)$this->params('to', 0));

        $vehicleData = $vehicle->getExportData($from, $to);
        $vehicleData[] = array('');
        $vehicleData[] = array('');

        return $this->ExportToCsvPlugin()->export($vehicleData);
    }

  public function verifyPrintActionListAction()
  {
    $vehicles = array();
    $vehicleId = (int)$this->params('id');
    if (!$vehicleId) {
      $user = $this->authService->getIdentity();
      $responsibleVehicles = $user->getResponsibleForVehicles();
      if ($responsibleVehicles) foreach ($responsibleVehicles as $vehicle) $vehicles[] = $vehicle;
      $operatorVehicles = $user->getVehicles();
      if ($operatorVehicles) foreach ($operatorVehicles as $vehicle) $vehicles[] = $vehicle;
    } else {
      $vehicle = $this->em->find('SafeStartApi\Entity\Vehicle', $vehicleId);
      if (!$vehicle) return $this->_showNotFound("Vehicle not found.");
      if (!$vehicle->haveAccess($this->authService->getStorage()->read())) return $this->_showUnauthorisedRequest();
      $vehicles[] = $vehicle;
    }

    if (empty($vehicles)) {
      $this->answer = array(
        'errorMessage' => 'No vehicles available for getting Action List',
      );
      return $this->AnswerPlugin()->format($this->answer, 204);
    }

    $this->answer = array();

    return $this->AnswerPlugin()->format($this->answer, 0);
  }

  public function printActionListAction()
  {
    $vehicles = array();
    $vehicleId = (int)$this->params('id');
    if (!$vehicleId) {
      $user = $this->authService->getIdentity();
      $responsibleVehicles = $user->getResponsibleForVehicles();
      if ($responsibleVehicles) foreach ($responsibleVehicles as $vehicle) $vehicles[] = $vehicle;
      $operatorVehicles = $user->getVehicles();
      if ($operatorVehicles) foreach ($operatorVehicles as $vehicle) $vehicles[] = $vehicle;
    } else {
      $vehicle = $this->em->find('SafeStartApi\Entity\Vehicle', $vehicleId);
      if (!$vehicle) return $this->_showNotFound("Vehicle not found.");
      if (!$vehicle->haveAccess($this->authService->getStorage()->read())) return $this->_showUnauthorisedRequest();
      $vehicles[] = $vehicle;
    }

    if (empty($vehicles)) {
      $this->answer = array(
        'errorMessage' => 'No vehicles available for getting Action List',
      );
      return $this->AnswerPlugin()->format($this->answer, 204);
    }

    $pdf = $this->vehicleActionListPdf()->create($vehicles);

    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename={$pdf['name']}");
    header("Content-Transfer-Encoding:Binary");
    header('Content-Length: ' . filesize($pdf['path']));
    echo file_get_contents($pdf['path']);
    return true;
  }

  public function sendActionListAction()
  {
    $vehicles = array();
    $vehicleId = (int)$this->params('id');
    if (!$vehicleId) {
      $user = $this->authService->getIdentity();
      $responsibleVehicles = $user->getResponsibleForVehicles();
      if ($responsibleVehicles) foreach ($responsibleVehicles as $vehicle) $vehicles[] = $vehicle;
      $operatorVehicles = $user->getVehicles();
      if ($operatorVehicles) foreach ($operatorVehicles as $vehicle) $vehicles[] = $vehicle;
    } else {
      $vehicle = $this->em->find('SafeStartApi\Entity\Vehicle', $vehicleId);
      if (!$vehicle) return $this->_showNotFound("Vehicle not found.");
      if (!$vehicle->haveAccess($this->authService->getStorage()->read())) return $this->_showUnauthorisedRequest();
      $vehicles[] = $vehicle;
    }

    if (empty($vehicles)) {
      $this->answer = array(
        'errorMessage' => 'No vehicles available for getting Action List',
      );
      return $this->AnswerPlugin()->format($this->answer, 204);
    }

    $sentResponses = array();

    $pdf = $this->vehicleActionListPdf()->create($vehicles);
    $config = $this->getServiceLocator()->get('Config');
    foreach ($vehicles as $vehicle) {
      $responsibles = $vehicle->getResponsibleUsers();
      if (!$responsibles) continue;
      foreach ($responsibles as $responsible) {
        try {
          $this->MailPlugin()->send(
              $this->moduleConfig['params']['emailSubjects']['vehicle_action_list'],
              $responsible->email,
              'actionlist.phtml',
              array(
                  'plantId'               => $vehicle->getPlantId(),
                  'siteUrl'               => $config['params']['site_url'],
                  'emailStaticContentUrl' => $config['params']['email_static_content_url']
              ), $pdf['path']);
          $sentResponses[] = array('email'=>$responsible->email);
        } catch(\Exception $e) {

        }
      }
    }

    $this->answer = array(
      'done' => true,
      'responses' => $sentResponses,
    );

    return $this->AnswerPlugin()->format($this->answer);

  }

  public function addServiceReportAction()
  {
    if (!$this->_requestIsValid('vehicle/addservicereport')) return $this->_showBadRequest();

    $id = $this->params('id');
    $alert = $this->em->find('SafeStartApi\Entity\Alert', $id);

    if (!$alert) return $this->_showNotFound("Alert not found.");

    $vehicle = $alert->getVehicle();

    if (!$vehicle) return $this->_showNotFound("Vehicle not found.");
    if (!$vehicle->haveAccess($this->authService->getStorage()->read())) return $this->_showUnauthorisedRequest();

    $user = $this->authService->getStorage()->read();

    if ($this->data->repaired)
    {
        $status = \SafeStartApi\Entity\Alert::STATUS_CLOSED;
        $alert->addHistoryItem(\SafeStartApi\Entity\Alert::ACTION_STATUS_CHANGED_CLOSED);
        $alert->setStatus($status);
        $this->em->persist($alert);
    }

    $serviceReport = new \SafeStartApi\Entity\ServiceReport();
    $serviceReport->setUser($user);
    $serviceReport->setAlert($alert);
    $serviceReport->setGpsCoords((isset($this->data->gps) && !empty($this->data->gps)) ? $this->data->gps : null);
    $serviceReport->setLocation((isset($this->data->location) && !empty($this->data->location)) ? $this->data->location : null);
    $serviceReport->setDescription((isset($this->data->description) && !empty($this->data->description)) ? $this->data->description : null);
    $serviceReport->setRepaired($this->data->repaired);

    if (isset($this->data->operator_name) && !empty($this->data->operator_name))
    {
        $serviceReport->setOperatorName($this->data->operator_name);
    }
    else
    {
        $serviceReport->setOperatorName($user->getFullName());
    }

    if ((isset($this->data->odometer) && !empty($this->data->odometer)))
    {
      $serviceReport->setCurrentOdometer($this->data->odometer);
    }
    else
    {
      $serviceReport->setCurrentOdometer($vehicle->getCurrentOdometerKms());
    }

    if ((isset($this->data->odometer_hours) && !empty($this->data->odometer_hours)))
    {
      $serviceReport->setCurrentOdometerHours($this->data->odometer_hours);
    }
    else
    {
      $serviceReport->setCurrentOdometerHours($vehicle->getCurrentOdometerHours());
    }

    $this->em->persist($serviceReport);
    $this->em->flush();

    $this->answer = array(
      'done' => true
    );

    return $this->AnswerPlugin()->format($this->answer);
  }

  public function addFaultReportAction()
  {
    if (!$this->_requestIsValid('vehicle/addfaultreport')) return $this->_showBadRequest();

    $vehicleId = $this->params('id');
    $vehicle = $this->em->find('SafeStartApi\Entity\Vehicle', $vehicleId);

    if (!$vehicle) return $this->_showNotFound("Vehicle not found.");
    if (!$vehicle->haveAccess($this->authService->getStorage()->read())) return $this->_showUnauthorisedRequest();

    $faultSummary = $this->data->faultSummary;
    $faultDescription = $this->data->faultDescription;
    $isCritical = (bool)$this->data->isCritical;

    $user = $this->authService->getStorage()->read();
    $faultReport = new \SafeStartApi\Entity\FaultReport();
    $faultReport->setUser($user);
    $faultReport->setGpsCoords((isset($this->data->gps) && !empty($this->data->gps)) ? $this->data->gps : null);
    $faultReport->setLocation((isset($this->data->location) && !empty($this->data->location)) ? $this->data->location : null);

    if (isset($this->data->operator_name) && !empty($this->data->operator_name))
    {
        $faultReport->setOperatorName($this->data->operator_name);
    }
    else
    {
        $faultReport->setOperatorName($user->getFullName());
    }

    if ((isset($this->data->odometer) && !empty($this->data->odometer)))
    {
      $faultReport->setCurrentOdometer($this->data->odometer);
    }
    else
    {
      $faultReport->setCurrentOdometer($vehicle->getCurrentOdometerKms());
    }

    if ((isset($this->data->odometer_hours) && !empty($this->data->odometer_hours)))
    {
      $faultReport->setCurrentOdometerHours($this->data->odometer_hours);
    }
    else
    {
      $faultReport->setCurrentOdometerHours($vehicle->getCurrentOdometerHours());
    }

    $this->em->persist($faultReport);
    $this->em->flush();

    $newAlert = new \SafeStartApi\Entity\Alert();
    $newAlert->setDefaultsForRenew();
    $newAlert->setFaultReport($faultReport);
    $newAlert->setVehicle($vehicle);
    $newAlert->setDescription($faultSummary);
    $newAlert->setImages(!empty($data->images) ? $data->images : array());
    $dueDate = new \DateTime();
    $dueDate->add(new \DateInterval('P'. ($isCritical ? '7' : '14'). 'D'));
    $newAlert->setDueDate($dueDate);

    $this->em->persist($newAlert);
    $this->em->flush();

    $newAlert->addComment($faultDescription);

    $this->em->persist($newAlert);
    $this->em->flush();

    $reminderPlugin = $this->AlertReminderPlugin();
    $reminderPlugin->sendReminder($newAlert, 'initialalertmail.phtml');

    $this->answer = array(
      'done' => true
    );

    return $this->AnswerPlugin()->format($this->answer);
  }

  private function checkCurrentInfoAlerts(Vehicle $vehicle, $desc)
  {
    $newAlert = null;
    $newAlerts = array();
    $alertRep = $this->em->getRepository('SafeStartApi\Entity\Alert');
    $alert = $alertRep->findOneBy(array('description' => $desc, 'vehicle' => $vehicle->getId()));
    if ($alert && $alert->getStatus() == \SafeStartApi\Entity\Alert::STATUS_NEW) {
      $alert->addHistoryItem(\SafeStartApi\Entity\Alert::ACTION_REFRESHED);
      $newAlerts[] = $alert;
    } elseif (!$alert) {
      $newAlert = new \SafeStartApi\Entity\Alert();
      $newAlert->setDescription($desc);
      $newAlert->setVehicle($vehicle);
      $this->em->persist($newAlert);
    }
    $this->em->flush();
    $alert = $newAlert ? $newAlert->toArray() : (count($newAlerts) ? $newAlerts[0]->toArray() : array());
    return $alert;
  }

    private function getAlertsByVehicle(\SafeStartApi\Entity\Vehicle $vehicle, $filters = array())
    {
        $alerts = array();
        $checkLists = $vehicle->getCheckLists();
        if (!empty($checkLists)) {
            foreach ($checkLists as $checkList) {
                $alerts = array_merge($alerts, $checkList->getAlertsArray($filters));
            }
        }
        $curDate = new \DateTime();
        if ((($vehicle->getExpiryDate() - $curDate->getTimestamp()) / (60 * 60 * 24) < 1)) {
            $alert = $this->checkCurrentInfoAlerts($vehicle, \SafeStartApi\Entity\Alert::EXPIRY_DATE);
            array_unshift($alerts, $alert);
        }

        $dueForServiceAlertTrigger = false;
        if (count($vehicle->checkLists) > 2) {
            $serviceDate = null;
            if ($vehicle->getNextServiceDay()) $serviceDate = \DateTime::createFromFormat('d/m/Y', $vehicle->getNextServiceDay());
            if ($serviceDate && (($serviceDate->getTimestamp() - $curDate->getTimestamp()) / (60 * 60 * 24) < 1)) {
                $alert = $this->checkCurrentInfoAlerts($vehicle, \SafeStartApi\Entity\Alert::DUE_SERVICE);
                array_unshift($alerts, $alert);
                $dueForServiceAlertTrigger = true;
            }
        }
        if (($vehicle->getCurrentOdometerKms() > $vehicle->getServiceDueKm()) || ($vehicle->getCurrentOdometerHours() > $vehicle->getServiceDueHours())) {
            $alert = $this->checkCurrentInfoAlerts($vehicle, \SafeStartApi\Entity\Alert::INACCURATE_KM_HR);
            if(!$dueForServiceAlertTrigger){
                $alertService = $this->checkCurrentInfoAlerts($vehicle, \SafeStartApi\Entity\Alert::DUE_SERVICE);
                if($alertService){
                    array_unshift($alerts, $alertService);
                }
            }
            array_unshift($alerts, $alert);
        }

        $alerts = array_filter($alerts, function($value) {
            return !empty($value) && is_array($value) && sizeof($value) > 0;
        });
        $alerts = array_values($alerts);

        return $alerts;
    }
}
