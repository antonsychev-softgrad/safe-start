<?php

namespace SafeStartApi\Controller;

use SafeStartApi\Base\RestrictedAccessRestController;
use Doctrine\Common\Collections\ArrayCollection;
use SafeStartApi\Entity\User;


/**
 * Class CompanyController
 * @package SafeStartApi\Controller
 */
class CompanyController extends RestrictedAccessRestController
{
  public function updateAction()
  {
    $companyId = (int)$this->params('id');
    $company = $this->em->find('SafeStartApi\Entity\Company', $companyId);
    if (!$company) {
      $this->answer = array(
        "errorMessage" => "Company not found."
      );
      return $this->AnswerPlugin()->format($this->answer, 404);
    }

    if (!$company->haveAccess($this->authService->getStorage()->read())) return $this->_showUnauthorisedRequest();

    // set company data
    $company->setTitle($this->data->title);
    $company->setAddress($this->data->address);
    $company->setPhone($this->data->phone);
    $company->setDescription($this->data->description);
    $company->setLogo(isset($this->data->logo) ? $this->data->logo : '');

    $this->em->flush();

    $this->answer = array(
      'done' => true,
      'companyId' => $company->getId(),
    );

    return $this->AnswerPlugin()->format($this->answer);

  }

  /**
   * @return mixed
   */
  public function getVehiclesAction()
  {
    $companyId = (int)$this->getRequest()->getQuery('companyId');
    $company = $this->em->find('SafeStartApi\Entity\Company', $companyId);

    if (!$company) {
      $this->answer = array(
        "errorMessage" => "Company not found."
      );
      return $this->AnswerPlugin()->format($this->answer, 404);
    }

    $this->answer = array();

    $node = (int)$this->getRequest()->getQuery('node');

    // $cache = \SafeStartApi\Application::getCache();
    // $cashKey = "getCompanyVehicles" . $companyId;

    // if ($cache->hasItem($cashKey) && !$node) {
    //     $this->answer = $cache->getItem($cashKey);
    // } else {
    if (!$node) {
      $query = $this->em->createQuery('SELECT v FROM SafeStartApi\Entity\Vehicle v WHERE v.deleted = 0 AND v.company = ?1');
      $query->setParameter(1, $company);
      $items = $query->getResult();
      foreach ($items as $vehicle) {
        if ($vehicle->haveAccess($this->authService->getStorage()->read())) {
          $this->answer[] = $vehicle->toMenuArray();
        }
      }
      //$cache->setItem($cashKey, $this->answer);
    } else {
      $vehicle = $this->em->find('SafeStartApi\Entity\Vehicle', $node);
      if ($vehicle) {
        $this->answer = $vehicle->getMenuItems();
      }
    }
    // }

    return $this->AnswerPlugin()->format($this->answer);
  }

  /**
   * @param $vehicle
   * @param null $defParent
   * @param null $parent
   */
  protected function copyVehicleDefFields($vehicle, $defParent = null, $parent = null)
  {
    $repField = $this->em->getRepository('SafeStartApi\Entity\DefaultField');

    $defFields = new \Doctrine\Common\Collections\ArrayCollection();
    $newFields = new \Doctrine\Common\Collections\ArrayCollection();
    if ($defParent === null) {
      $defFields = $repField->findBy(array('parent' => null));
    } else {
      $defFields = $repField->findBy(array('parent' => $defParent->getId()));
    }

    foreach ($defFields as $defField) {
      $newField = new \SafeStartApi\Entity\Field();

      $newField->setParent($parent);
      $newField->setVehicle($vehicle);
      $newField->setTitle($defField->getTitle());
      $newField->setDescription($defField->getDescription());
      $newField->setType($defField->getType());
      $newField->setAdditional($defField->getAdditional());
      $newField->setTriggerValue($defField->getTriggerValue());
      $newField->setAlertTitle($defField->getAlertTitle());
      $newField->setAlertDescription($defField->getAlertDescription());
      $newField->setAlertCritical($defField->getAlertCritical());
      $newField->setOrder($defField->getOrder());
      $newField->setEnabled($defField->getEnabled());
      $newField->setDeleted($defField->getDeleted());
      $newField->setAuthor($defField->getAuthor());

      if ($parent !== null) {
        $parent->addChildred($newField);
        $this->em->persist($parent);
      }

      $this->copyVehicleDefFields($vehicle, $defField, $newField);
      $this->em->persist($newField);
      $vehicle->addField($newField);
      $this->em->persist($vehicle);
    }
  }

  /**
   * @return mixed
   */
    public function updateVehicleAction()
    {
        if (isset($this->data->companyId)) {
            $company = $this->em->find('SafeStartApi\Entity\Company', $this->data->companyId);
            if (!$company) {
                $this->answer = array(
                    "errorMessage" => "Company not found."
                );

                return $this->AnswerPlugin()->format($this->answer, 404);
            }
        } else {
            return $this->_showBadRequest();
        }

        $vehicleId = (int)$this->params('id');
        //$user = \SafeStartApi\Application::getCurrentUser();
        $plantId    = strtoupper($this->data->plantId);
        $repository = $this->em->getRepository('SafeStartApi\Entity\Vehicle');
        if ($vehicleId) {
            $vehicle = $repository->find($vehicleId);
            if (!$vehicle) {
                $this->answer = array(
                    "errorMessage" => "Vehicle not found."
                );

                return $this->AnswerPlugin()->format($this->answer, 404);
            }
            if (!$vehicle->haveAccess($this->authService->getStorage()->read()))
                return $this->_showUnauthorisedRequest();
            if ($vehicle->getPlantId() != $plantId) {
                $vehicleWithSentPlantId = $repository->findOneBy(array(
                    'plantId' => $plantId,
                    'deleted' => 0,
                ));
                if (!is_null($vehicleWithSentPlantId))
                    return $this->_showKeyExists('Vehicle with this Plant ID already exists');
            }
        } else {
            $vehicle = $repository->findOneBy(array(
                'plantId' => $plantId,
                'deleted' => 0,
            ));
            if (!is_null($vehicle)) {
                return $this->_showKeyExists('Vehicle with this Plant ID already exists');
            }
            if (!$company->haveAccess($this->authService->getStorage()->read())) {
                return $this->_showUnauthorisedRequest();
            }
            if ($company->getRestricted() && ((count($company->getVehicles()) + 1) > $company->getMaxVehicles())) {
                return $this->_showCompanyLimitReached('Company limit of vehicles reached');
            }
            $vehicle = new \SafeStartApi\Entity\Vehicle();
            //$vehicle->addResponsibleUser($user);
            $this->copyVehicleDefFields($vehicle);

            //set User role for vehicle users
            $users = $company->getUsers();
            foreach ($users as $user) {
                if ($user->getRole() === User::ADMIN_ROLE) {
                    continue;
                }
                $vehicle->addUser($user);
            }
        }
        $alertRep = $this->em->getRepository('SafeStartApi\Entity\Alert');
        $curDate  = new \DateTime();
        $vehicle->setCompany($company);
        $vehicle->setPlantId($plantId);
        //    $vehicle->setTitle($this->data->title);
        //    $vehicle->setType($this->data->type);
        $vehicle->setEnabled((int)$this->data->enabled);
        //    $vehicle->setProjectName($this->data->projectName);
        //    $vehicle->setProjectNumber($this->data->projectNumber);
        $vehicle->setServiceDueKm((int)$this->data->serviceDueKm);
        $vehicle->setServiceDueHours((int)$this->data->serviceDueHours);
        $vehicle->setServiceThresholdKm((int)$this->data->serviceThresholdKm);
        $vehicle->setServiceThresholdHours((int)$this->data->serviceThresholdHours);
        if (isset($this->data->expiryDate) && !empty($this->data->expiryDate)) {
            $expiryDate = new \DateTime();
            $expiryDate->setTimestamp($this->data->expiryDate);
            $vehicle->setExpiryDate($expiryDate);
            //update expiry date's alert if exist
            $alert = $alertRep->findOneBy(array(
                'description' => \SafeStartApi\Entity\Alert::EXPIRY_DATE,
                'vehicle'     => $vehicle->getId(),
                'status'      => \SafeStartApi\Entity\Alert::STATUS_NEW
            ));
            if ($alert && ((($vehicle->getExpiryDate() - $curDate->getTimestamp()) / (60 * 60 * 24)) > 0)) {
                $alert->setStatus(\SafeStartApi\Entity\Alert::STATUS_CLOSED);
                $this->em->remove($alert);
            }
            //end
        }
        $vehicle->setCurrentOdometerHours(isset($this->data->currentOdometerHours) ? (float)$this->data->currentOdometerHours : 0);
        $vehicle->setCurrentOdometerKms(isset($this->data->currentOdometerKms) ? (float)$this->data->currentOdometerKms : 0);
        $vehicle->setInspectionDueKms(isset($this->data->inspectionDueKms) ? (int)$this->data->inspectionDueKms : 0);
        $vehicle->setInspectionDueHours(isset($this->data->inspectionDueHours) ? (int)$this->data->inspectionDueHours : 0);
        if (isset($this->data->warrantyStartOdometer))
            $vehicle->setWarrantyStartOdometer($this->data->warrantyStartOdometer);
        if (isset($this->data->warrantyStartDate)) {
            $warrantyStartDate = new \DateTime();
            $warrantyStartDate->setTimestamp((int)$this->data->warrantyStartDate);
            $vehicle->setWarrantyStartDate($warrantyStartDate);
        }

        if (count($vehicle->checkLists) > 2) {
            $serviceDate = \DateTime::createFromFormat('d/m/Y', $vehicle->getNextServiceDay());
            $alert       = $alertRep->findOneBy(array(
                'description' => \SafeStartApi\Entity\Alert::DUE_SERVICE,
                'vehicle'     => $vehicle->getId(),
                'status'      => \SafeStartApi\Entity\Alert::STATUS_NEW
            ));
            if ($alert && ((($serviceDate->getTimestamp() - $curDate->getTimestamp()) / (60 * 60 * 24)) > 1)) {
                $alert->setStatus(\SafeStartApi\Entity\Alert::STATUS_CLOSED);
                $this->em->remove($alert);
            }
        }
        if (($vehicle->getCurrentOdometerKms() < $vehicle->getServiceDueKm()) && ($vehicle->getCurrentOdometerHours() < $vehicle->getServiceDueHours())) {
            $alert = $alertRep->findOneBy(array(
                'description' => \SafeStartApi\Entity\Alert::INACCURATE_KM_HR,
                'vehicle'     => $vehicle->getId(),
                'status'      => \SafeStartApi\Entity\Alert::STATUS_NEW
            ));
            if ($alert) {
                $alert->setStatus(\SafeStartApi\Entity\Alert::STATUS_CLOSED);
                $this->em->remove($alert);
            }
        }

        if (isset($this->data->customFields)) {
            $customFields = (array)$this->data->customFields;
            foreach ($customFields as $key => $customField) {
                $field = $this->em->find('SafeStartApi\Entity\VehicleField', $key);
                if ($field) {
                    $field->setDefaultValue($customField);
                    $this->em->persist($field);
                }
            }
        } else {
            $customFields = array();
        }

        $this->em->persist($vehicle);
        $this->em->flush();

        $vehiclePlugin = $this->processAdditionalVehiclePlugin();
        $vehiclePlugin->checkDefaultCustomFields($vehicle, $customFields);

        $cache   = \SafeStartApi\Application::getCache();
        $cashKey = "getCompanyVehicles" . $company->getId();
        if ($cache->hasItem($cashKey))
            $cache->removeItem($cashKey);

        $this->answer = array(
            'done'      => true,
            'vehicleId' => $vehicle->getId(),
        );

        return $this->AnswerPlugin()->format($this->answer);
    }

  /**
   * @return mixed
   */
  public function deleteVehicleAction()
  {
    $vehicleId = (int)$this->params('id');
    $vehicle = $this->em->find('SafeStartApi\Entity\Vehicle', $vehicleId);

    if (!$vehicle) {
      $this->answer = array(
        "errorMessage" => "Vehicle not found."
      );
      return $this->AnswerPlugin()->format($this->answer, 404);
    }

    if (!$vehicle->haveAccess($this->authService->getStorage()->read())) return $this->_showUnauthorisedRequest();

    $users = $vehicle->getUsers();
    $responsibleUsers = $vehicle->getResponsibleUsers();

    $users = new ArrayCollection(array_merge($users->toArray(), $responsibleUsers->toArray()));

    $cache = \SafeStartApi\Application::getCache();
    foreach ($users as $user) {
      $cashKey = "getUserVehiclesList" . $user->getId();
      $cache->removeItem($cashKey);
    }

    $vehicle->setPlantId(time() . " " . $vehicle->getPlantId());
    $vehicle->setDeleted(1);

    $this->em->flush();

    $this->answer = array(
      'done' => true
    );

    return $this->AnswerPlugin()->format($this->answer);
  }

  /**
   * @return mixed
   */
  public function getUsersAction()
  {
    $companyId = (int)$this->getRequest()->getQuery('companyId');
    $company = $this->em->find('SafeStartApi\Entity\Company', $companyId);

    if (!$company) {
      $this->answer = array(
        "errorMessage" => "Company not found."
      );
      return $this->AnswerPlugin()->format($this->answer, 404);
    }

    $this->answer = array();

    $query = $this->em->createQuery('SELECT u FROM SafeStartApi\Entity\User u WHERE u.deleted = 0 AND u.company = ?1');
    $query->setParameter(1, $company);
    $items = $query->getResult();

    foreach ($items as $item) {
      if ($item->getId() == $this->authService->getIdentity()->getId()) continue;
      $this->answer[] = $item->toArray();
    }

    return $this->AnswerPlugin()->format($this->answer);

  }

  /**
   * @return mixed
   */
  public function getVehicleUsersAction()
  {
    $vehicleId = (int)$this->params('id');
    $vehicle = $this->em->find('SafeStartApi\Entity\Vehicle', $vehicleId);

    if (!$vehicle) {
      $this->answer = array(
        "errorMessage" => "Vehicle not found."
      );
      return $this->AnswerPlugin()->format($this->answer, 404);
    }

    if (!$vehicle->haveAccess($this->authService->getStorage()->read())) return $this->_showUnauthorisedRequest();

    $this->answer = array();
    $query = $this->em->createQuery('SELECT u FROM SafeStartApi\Entity\User u WHERE u.deleted = 0 AND u.company = ?1');
    $query->setParameter(1, $vehicle->getCompany());

    $companyUsers = $query->getResult();
    $responsibleUsers = $vehicle->getResponsibleUsers();
    $vehicleUsers = $vehicle->getUsers();

    foreach ($companyUsers as $companyUser) {
      $user = $companyUser->toInfoArray();
      $user['assigned'] = 'no';
      if ($responsibleUsers->contains($companyUser)) $user['assigned'] = 'responsible';
      if ($vehicleUsers->contains($companyUser)) $user['assigned'] = 'user';
      $this->answer[] = $user;
    }

    return $this->AnswerPlugin()->format($this->answer);

  }

  /**
   * @return mixed
   */
  public function updateVehicleUsersAction()
  {
    $vehicleId = (int)$this->params('id');
    $vehicle = $this->em->find('SafeStartApi\Entity\Vehicle', $vehicleId);

    if (!$vehicle) {
      $this->answer = array(
        "errorMessage" => "Vehicle not found."
      );
      return $this->AnswerPlugin()->format($this->answer, 404);
    }

    if (!$vehicle->haveAccess($this->authService->getStorage()->read())) return $this->_showUnauthorisedRequest();

    $vehicle->removeResponsibleUsers();
    $vehicle->removeUsers();
    $this->em->flush();

    foreach ((array)$this->data->value as $value) {
      $value = (array)$value;
      $user = $this->em->find('SafeStartApi\Entity\User', (int)$value['userId']);
      if ($user) {
        $cache = \SafeStartApi\Application::getCache();
        $cashKey = "getUserVehiclesList" . $user->getId();
        $cache->removeItem($cashKey);
        switch ($value['assigned']) {
          case 'responsible':
            $vehicle->addResponsibleUser($user);
            break;
          case 'user':
            $vehicle->addUser($user);
            break;
        }
      }
    }

    $this->em->flush();

    $this->answer = array('done' => true);
    return $this->AnswerPlugin()->format($this->answer);
  }

  /**
   * @return mixed
   */
  public function getVehicleChecklistAction()
  {
    $vehicleId = (int)$this->getRequest()->getQuery('vehicleId');
    $vehicle = $this->em->find('SafeStartApi\Entity\Vehicle', $vehicleId);
    if (!$vehicle) {
      $this->answer = array(
        "errorMessage" => "Vehicle not found."
      );
      return $this->AnswerPlugin()->format($this->answer, 404);
    }

    if (!$vehicle->haveAccess($this->authService->getStorage()->read())) return $this->_showUnauthorisedRequest();

    $cache = \SafeStartApi\Application::getCache();
    $cashKey = "getVehicleForEditChecklist" . $vehicleId;

    if ($cache->hasItem($cashKey)) {
      $this->answer = $cache->getItem($cashKey);
    } else {
      $query = $this->em->createQuery('SELECT f FROM SafeStartApi\Entity\Field f WHERE f.deleted = 0 AND f.vehicle = ?1');
      $query->setParameter(1, $vehicle);
      $items = $query->getResult();
      $this->answer = $this->GetDataPlugin()->buildChecklistTree($items);
      $cache->setItem($cashKey, $this->answer);
    }

    return $this->AnswerPlugin()->format($this->answer);
  }

  /**
   * @return mixed
   */
  public function updateVehicleChecklistFiledAction()
  {
    $vehicleId = (int)$this->data->vehicleId;
    $vehicle = $this->em->find('SafeStartApi\Entity\Vehicle', $vehicleId);
    if (!$vehicle) {
      $this->answer = array(
        "errorMessage" => "Vehicle not found."
      );
      return $this->AnswerPlugin()->format($this->answer, 404);
    }

    if (!$vehicle->haveAccess($this->authService->getStorage()->read())) return $this->_showUnauthorisedRequest();

    $action = 'create';
    $fieldId = (int)$this->params('id');
    if ($fieldId) {
      $field = $this->em->find('SafeStartApi\Entity\Field', $fieldId);
      if (!$field) {
        $this->answer = array(
          "errorMessage" => "Checklist Filed not found."
        );
        return $this->AnswerPlugin()->format($this->answer, 404);
      }
      $action = 'update';
    } else {
      $field = new \SafeStartApi\Entity\Field();
    }

    if (!empty($this->data->parentId) && $this->data->parentId != "NaN") {
      $parentField = $this->em->find('SafeStartApi\Entity\Field', (int)$this->data->parentId);
      if (!$parentField) {
        $this->answer = array(
          "errorMessage" => "Wrong parent filed."
        );
        return $this->AnswerPlugin()->format($this->answer, 401);
      }
      $field->setParent($parentField);
    }

    if (!in_array($this->data->type, array('root', 'text', 'group', 'radio', 'checkbox', 'photo', 'datePicker', 'label'))) {
      $this->answer = array(
        "errorMessage" => "Wrong field type."
      );
      return $this->AnswerPlugin()->format($this->answer, 401);
    }

    $oldTitle = $field->getTitle();
    $oldType = $field->getType();
    if ($this->data->type == 'root' && $this->data->parentId) $this->data->type = 'text';

    $field->setTitle($this->data->title);
    $field->setDescription($this->data->description);
    $field->setType($this->data->type);
    $field->setOrder((int)$this->data->sort_order);
    $field->setAdditional($this->data->type == 'root' ? (int)$this->data->additional : 0);
    $field->setAlertTitle(isset($this->data->alert_title) ? $this->data->alert_title : '');
    $field->setAlertDescription(isset($this->data->alert_description) ? $this->data->alert_description : '');
    $field->setTriggerValue($this->data->trigger_value);
    $field->setEnabled((int)$this->data->enabled);
    $field->setDefaultValue($this->data->default_value);
    $field->setAlertCritical((int)$this->data->alert_critical);
    $field->setVehicle($vehicle);

    if (!$fieldId) $this->em->persist($field);
    $field->setAuthor($this->authService->getStorage()->read());

    $this->em->flush();

    $cache = \SafeStartApi\Application::getCache();
    $cashKey = "getVehicleChecklist" . $vehicle->getId();
    $cashKey2 = "getVehicleForEditChecklist" . $vehicle->getId();
    $cashKey3 = "getVehicleChecklistFieldsStructure" . $vehicle->getId();
    if ($cache->hasItem($cashKey)) $cache->removeItem($cashKey);
    if ($cache->hasItem($cashKey2)) $cache->removeItem($cashKey2);
    if ($cache->hasItem($cashKey3)) $cache->removeItem($cashKey3);

    if ($oldTitle != $this->data->title || $oldType != $this->data->type) {
      $record = new \SafeStartApi\Entity\InspectionChanges();
      $record->setAction($action);
      $record->setKey($field->getTitle());
      $record->setPrevKey($oldTitle);
      $record->setType($field->getType());
      $record->setFieldId($field->getId());

      $user = \SafeStartApi\Application::getCurrentUser();
      $record->setCompanyName($user->getCompany() ? $user->getCompany()->getTitle() : '');
      $record->setCompanyId($user->getCompany() ? $user->getCompany()->getId() : '');
      $record->setUserName($user->getFirstName() . " " . $user->getLastName());
      $record->setUserId($user->getId());

      $this->em->persist($record);
      $this->em->flush();
    }

    $this->answer = array(
      'done' => true,
      'fieldId' => $field->getId(),
    );

    return $this->AnswerPlugin()->format($this->answer);

  }

  /**
   * @return mixed
   */
  public function deleteVehicleChecklistFiledAction()
  {
    $fieldId = (int)$this->params('id');

    $field = $this->em->find('SafeStartApi\Entity\Field', $fieldId);
    if (!$field) {
      $this->answer = array(
        "errorMessage" => "Checklist Filed not found."
      );
      return $this->AnswerPlugin()->format($this->answer, 404);
    }

    $field->setDeleted(1);
    $this->em->flush();

    $record = new \SafeStartApi\Entity\InspectionChanges();
    $record->setAction('delete');
    $record->setKey($field->getTitle());
    $record->setType($field->getType());
    $record->setFieldId($field->getId());

    $user = \SafeStartApi\Application::getCurrentUser();
    $record->setCompanyName($user->getCompany() ? $user->getCompany()->getTitle() : '');
    $record->setCompanyId($user->getCompany() ? $user->getCompany()->getId() : '');
    $record->setUserName($user->getFirstName() . " " . $user->getLastName());
    $record->setUserId($user->getId());

    $vehicle = $field->getVehicle();

    $cache = \SafeStartApi\Application::getCache();
    $cashKey = "getVehicleChecklist" . $vehicle->getId();
    $cashKey2 = "getVehicleForEditChecklist" . $vehicle->getId();
    $cashKey3 = "getVehicleChecklistFieldsStructure" . $vehicle->getId();
    if ($cache->hasItem($cashKey)) $cache->removeItem($cashKey);
    if ($cache->hasItem($cashKey2)) $cache->removeItem($cashKey2);
    if ($cache->hasItem($cashKey3)) $cache->removeItem($cashKey3);

    $this->em->persist($record);
    $this->em->flush();

    $this->answer = array(
      'done' => true
    );

    return $this->AnswerPlugin()->format($this->answer);
  }

  /**
   * @return mixed
   */
  public function getVehicleAlertsAction()
  {
    $alerts = null;
    $this->answer = array();
    // filters
    $filters = array();
    $filters['status'] = (string)$this->getRequest()->getQuery('status');
    $page = (int)$this->getRequest()->getQuery('page');
    $limit = (int)$this->getRequest()->getQuery('limit');

    $vehicleId = (int)$this->getRequest()->getQuery('vehicleId');
    if (!empty($vehicleId)) {
      $vehicle = $this->em->find('SafeStartApi\Entity\Vehicle', $vehicleId);
      if (!$vehicle) {
        $this->answer = array(
          "errorMessage" => "Vehicle not found."
        );
        return $this->AnswerPlugin()->format($this->answer, 404);
      }
      if (!$vehicle->haveAccess($this->authService->getStorage()->read())) return $this->_showUnauthorisedRequest();
      $alerts = $this->getAlertsByVehicle($vehicle, $filters);
    }


    $companyId = (int)$this->getRequest()->getQuery('companyId');
    if (!empty($companyId)) {
      $company = $this->em->find('SafeStartApi\Entity\Company', $companyId);
      if (!$company) {
        $this->answer = array(
          "errorMessage" => "Vehicle not found."
        );
        return $this->AnswerPlugin()->format($this->answer, 404);
      }
      $alerts = $this->getAlertsByCompany($company, $filters);
    }

    $alerts = array_reverse($alerts);

    if ($alerts) {
      if (count($alerts) < ($page - 1) * $limit) {
        $this->answer = array();
        return $this->AnswerPlugin()->format($this->answer);
      }
      $iteratorAdapter = new \Zend\Paginator\Adapter\ArrayAdapter($alerts);
      $paginator = new \Zend\Paginator\Paginator($iteratorAdapter);
      $paginator->setCurrentPageNumber($page ? $page : 1);
      $paginator->setItemCountPerPage($limit ? $limit : 10);
      $items = $paginator->getCurrentItems() ? $paginator->getCurrentItems()->getArrayCopy() : array();
      $this->answer = $items;
      return $this->AnswerPlugin()->format($this->answer);
    }

    return $this->AnswerPlugin()->format($this->answer);
  }

  private function checkCurrentInfoAlerts(\SafeStartApi\Entity\Vehicle $vehicle, $desc)
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

  /**
   * @param \SafeStartApi\Entity\Vehicle $vehicle
   * @param array $filters
   * @return array|mixed
   */
  private function getAlertsByVehicle(\SafeStartApi\Entity\Vehicle $vehicle, $filters = array())
  {
    $cache = \SafeStartApi\Application::getCache();
    $cashKey = "getAlertsByVehicle" . $vehicle->getId();

    $alerts = array();

    /*   if ($cache->hasItem($cashKey)) {
           $data = $cache->getItem($cashKey);
       } else {*/
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
    /*      $cache->setItem($cashKey, $data);
      }*/

    return $alerts;
  }

  /**
   * @param \SafeStartApi\Entity\Company $company
   * @param array $filters
   * @return array|mixed
   */
  private function getAlertsByCompany(\SafeStartApi\Entity\Company $company, $filters = array())
  {
    $cache = \SafeStartApi\Application::getCache();
    $cashKey = "getAlertsByCompany" . $company->getId();

    $data = array();

    /*  if ($cache->hasItem($cashKey)) {
          $data = $cache->getItem($cashKey);
      } else {*/
    $query = $this->em->createQuery('SELECT v FROM SafeStartApi\Entity\Vehicle v WHERE v.deleted = 0 AND v.company = ?1');
    $query->setParameter(1, $company);
    $vehicles = $query->getResult();
    if (!empty($vehicles)) {
      foreach ($vehicles as $vehicle) {
        if ($vehicle->haveAccess($this->authService->getStorage()->read())) {
          $data = array_merge($data, $this->getAlertsByVehicle($vehicle, $filters));
        }
      }
    }

    /*       $cache->setItem($cashKey, $data);
       }*/

    return $data;
  }

  /**
   * @return mixed
   */
  public function getNewIncomingAction()
  {
    $companyId = (int)$this->params('id');
    $company = $this->em->find('SafeStartApi\Entity\Company', $companyId);

    if (!$company) {
      $this->answer = array(
        "errorMessage" => "Company not found."
      );
      return $this->AnswerPlugin()->format($this->answer, 404);
    }

    $query = $this->em->createQuery('SELECT v FROM SafeStartApi\Entity\Vehicle v WHERE v.deleted = 0 AND v.company = ?1');
    $query->setParameter(1, $company);
    $vehicles = $query->getResult();

    $alertsCount = 0;

    if (!empty($vehicles)) {
      foreach ($vehicles as $vehicle) {
        if ($vehicle->haveAccess($this->authService->getStorage()->read())) {
          $checkLists = $vehicle->getCheckLists();
          if (!empty($checkLists)) {
            foreach ($checkLists as $checkList) {
              $alertsCount += count($checkList->getAlertsArray(array('status' => 'new')));
            }
          }
        }
      }
    }

    $this->answer = array(
      'alerts' => $alertsCount
    );

    return $this->AnswerPlugin()->format($this->answer);
  }

    /**
     * @return mixed
     */
    public function getVehicleFieldAction()
    {
        $vehicleId = (int)$this->getRequest()->getQuery('vehicleId');
        $vehicle = $this->em->find('SafeStartApi\Entity\Vehicle', $vehicleId);
        $vehiclePlugin = $this->processAdditionalVehiclePlugin();
        $vehiclePlugin->checkDefaultCustomFields($vehicle);
        if (!$vehicle) {
            $this->answer = array(
                "errorMessage" => "Vehicle not found."
            );
            return $this->AnswerPlugin()->format($this->answer, 404);
        }

        if (!$vehicle->haveAccess($this->authService->getStorage()->read())) return $this->_showUnauthorisedRequest();

//        $cache = \SafeStartApi\Application::getCache();
//        $cashKey = "getVehicleForEditField" . $vehicleId;

//        if ($cache->hasItem($cashKey)) {
//            $this->answer = $cache->getItem($cashKey);
//        } else {
            $query = $this->em->createQuery('SELECT f FROM SafeStartApi\Entity\VehicleField f WHERE f.deleted = 0 AND f.vehicle = ?1');
            $query->setParameter(1, $vehicle);
            $items = $query->getResult();
            $this->answer = $this->GetDataPlugin()->buildChecklistTree($items);
//            $cache->setItem($cashKey, $this->answer);
//        }

        return $this->AnswerPlugin()->format($this->answer);
    }

    /**
     * @return mixed
     */
    public function updateVehicleFieldAction()
    {
        $vehicleId = (int)$this->data->vehicleId;
        $vehicle = $this->em->find('SafeStartApi\Entity\Vehicle', $vehicleId);
        if (!$vehicle) {
            $this->answer = array(
                "errorMessage" => "Vehicle not found."
            );
            return $this->AnswerPlugin()->format($this->answer, 404);
        }

        if (!$vehicle->haveAccess($this->authService->getStorage()->read())) return $this->_showUnauthorisedRequest();

        $action = 'create';
        $fieldId = (int)$this->params('id');
        if ($fieldId) {
            $field = $this->em->find('SafeStartApi\Entity\VehicleField', $fieldId);
            if (!$field) {
                $this->answer = array(
                    "errorMessage" => "Checklist Filed not found."
                );
                return $this->AnswerPlugin()->format($this->answer, 404);
            }
            $action = 'update';
        } else {
            $field = new \SafeStartApi\Entity\VehicleField();
        }

        if (!empty($this->data->parentId) && $this->data->parentId != "NaN") {
            $parentField = $this->em->find('SafeStartApi\Entity\VehicleField', (int)$this->data->parentId);
            if (!$parentField) {
                $this->answer = array(
                    "errorMessage" => "Wrong parent filed."
                );
                return $this->AnswerPlugin()->format($this->answer, 401);
            }
            $field->setParent($parentField);
        }

        if (!in_array($this->data->type, array('root', 'text', 'group', 'radio', 'checkbox', 'photo', 'datePicker', 'label'))) {
            $this->answer = array(
                "errorMessage" => "Wrong field type."
            );
            return $this->AnswerPlugin()->format($this->answer, 401);
        }

        $oldTitle = $field->getTitle();
        $oldType = $field->getType();
        if ($this->data->type == 'root' && $this->data->parentId) $this->data->type = 'text';

        $field->setTitle($this->data->title);
        $field->setDescription($this->data->description);
        $field->setType($this->data->type);
        $field->setOrder((int)$this->data->sort_order);
        $field->setAdditional($this->data->type == 'root' ? (int)$this->data->additional : 0);
        $field->setAlertTitle(isset($this->data->alert_title) ? $this->data->alert_title : '');
        $field->setAlertDescription(isset($this->data->alert_description) ? $this->data->alert_description : '');
        $field->setTriggerValue($this->data->trigger_value);
        $field->setEnabled((int)$this->data->enabled);
        $field->setDefaultValue($this->data->default_value);
        $field->setAlertCritical((int)$this->data->alert_critical);
        $field->setVehicle($vehicle);

        if (!$fieldId) $this->em->persist($field);
        $field->setAuthor($this->authService->getStorage()->read());

        $this->em->flush();

//        $cache = \SafeStartApi\Application::getCache();
//        $cashKey = "getVehicleFiled" . $vehicle->getId();
//        $cashKey2 = "getVehicleForEditChecklist" . $vehicle->getId();
//        $cashKey3 = "getVehicleChecklistFieldsStructure" . $vehicle->getId();
//        if ($cache->hasItem($cashKey)) $cache->removeItem($cashKey);
//        if ($cache->hasItem($cashKey2)) $cache->removeItem($cashKey2);
//        if ($cache->hasItem($cashKey3)) $cache->removeItem($cashKey3);

//        if ($oldTitle != $this->data->title || $oldType != $this->data->type) {
//            $record = new \SafeStartApi\Entity\InspectionChanges();
//            $record->setAction($action);
//            $record->setKey($field->getTitle());
//            $record->setPrevKey($oldTitle);
//            $record->setType($field->getType());
//            $record->setFieldId($field->getId());
//
//            $user = \SafeStartApi\Application::getCurrentUser();
//            $record->setCompanyName($user->getCompany() ? $user->getCompany()->getTitle() : '');
//            $record->setCompanyId($user->getCompany() ? $user->getCompany()->getId() : '');
//            $record->setUserName($user->getFirstName() . " " . $user->getLastName());
//            $record->setUserId($user->getId());
//
//            $this->em->persist($record);
//            $this->em->flush();
//        }

        $this->answer = array(
            'done' => true,
            'fieldId' => $field->getId(),
        );

        return $this->AnswerPlugin()->format($this->answer);

    }

    /**
     * @return mixed
     */
    public function deleteVehicleFieldAction()
    {
        $fieldId = (int)$this->params('id');

        $field = $this->em->find('SafeStartApi\Entity\VehicleField', $fieldId);
        if (!$field) {
            $this->answer = array(
                "errorMessage" => "Checklist Filed not found."
            );
            return $this->AnswerPlugin()->format($this->answer, 404);
        }

//        $field->setDeleted(1);
        $this->em->remove($field);
//        $this->em->flush();

//        $record = new \SafeStartApi\Entity\InspectionChanges();
//        $record->setAction('delete');
//        $record->setKey($field->getTitle());
//        $record->setType($field->getType());
//        $record->setFieldId($field->getId());
//
//        $user = \SafeStartApi\Application::getCurrentUser();
//        $record->setCompanyName($user->getCompany() ? $user->getCompany()->getTitle() : '');
//        $record->setCompanyId($user->getCompany() ? $user->getCompany()->getId() : '');
//        $record->setUserName($user->getFirstName() . " " . $user->getLastName());
//        $record->setUserId($user->getId());

        $vehicle = $field->getVehicle();

//        $cache = \SafeStartApi\Application::getCache();
//        $cashKey = "getVehicleChecklist" . $vehicle->getId();
//        $cashKey2 = "getVehicleForEditChecklist" . $vehicle->getId();
//        $cashKey3 = "getVehicleChecklistFieldsStructure" . $vehicle->getId();
//        if ($cache->hasItem($cashKey)) $cache->removeItem($cashKey);
//        if ($cache->hasItem($cashKey2)) $cache->removeItem($cashKey2);
//        if ($cache->hasItem($cashKey3)) $cache->removeItem($cashKey3);

//        $this->em->persist($record);
        $this->em->flush();

        $this->answer = array(
            'done' => true
        );

        return $this->AnswerPlugin()->format($this->answer);
    }

}
