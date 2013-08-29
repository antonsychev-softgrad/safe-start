<?php

namespace SafeStartApi\Controller;

use SafeStartApi\Base\RestrictedAccessRestController;

class CompanyController extends RestrictedAccessRestController
{
    public function getVehiclesAction()
    {
        //todo: check access to company
        /*
            todo: add json schema
            if (!$this->_requestIsValid('admin/getcompanies')) return $this->_showBadRequest();
        */
        $companyId = (int)$this->getRequest()->getQuery('companyId');
        $company = $this->em->find('SafeStartApi\Entity\Company', $companyId);

        if (!$company) {
            $this->answer = array(
                "errorMessage" => "Company not found."
            );
            return $this->AnswerPlugin()->format($this->answer, 404);
        }

        $this->answer = array(

        );

        $query = $this->em->createQuery('SELECT v FROM SafeStartApi\Entity\Vehicle v WHERE v.deleted = 0 AND v.company = ?1');
        $query->setParameter(1, $company);
        $items = $query->getResult();

        foreach ($items as $item) {
            $this->answer[] = $item->toMenuArray();
        }

        return $this->AnswerPlugin()->format($this->answer);
    }

    protected function copyVehicleDefFields($vehicle, $defParent = null, $parent = null) {
        $repField = $this->em->getRepository('SafeStartApi\Entity\DefaultField');

        $defFields = new \Doctrine\Common\Collections\ArrayCollection();
        $newFields = new \Doctrine\Common\Collections\ArrayCollection();
        if ($defParent === null) {
            $defFields = $repField->findBy(array('parent' => null));
        } else {
            $defFields = $repField->findBy(array('parent' => $defParent->getId()));
        }

        foreach ($defFields as $defField) {
            $newFild = new \SafeStartApi\Entity\Field();

            $newFild->setParent($parent);
            $newFild->setVehicle($vehicle);
            $newFild->setTitle($defField->getTitle());
            $newFild->setType($defField->getType());
            $newFild->setAdditional($defField->getAdditional());
            $newFild->setTriggerValue($defField->getTriggerValue());
            $newFild->setAlertTitle($defField->getAlertTitle());
            $alertsList = $defField->getAlerts();
            foreach ($alertsList as $alert) {
                $newFild->addAlert($alert);
            }
            $newFild->setOrder($defField->getOrder());
            $newFild->setEnabled($defField->getEnabled());
            $newFild->setDeleted($defField->getDeleted());
            $newFild->setAuthor($defField->getAuthor());

            // $newFild->setCreation_date(date_create());
            if ($parent !== null) {
                $parent->addChildred($newFild);
                $this->em->persist($parent);
            }

            $this->copyVehicleDefFields($vehicle, $defField, $newFild);
            $this->em->persist($newFild);
            $vehicle->addField($newFild);
            $this->em->persist($vehicle);
        }
    }

    public function updateVehicleAction()
    {
        $vehicleId = (int)$this->params('id');
        if ($vehicleId) {
            $vehicle = $this->em->find('SafeStartApi\Entity\Vehicle', $vehicleId);
            if (!$vehicle) {
                $this->answer = array(
                    "errorMessage" => "Vehicle not found."
                );
                return $this->AnswerPlugin()->format($this->answer, 404);
            }
        } else {
            $vehicle = new \SafeStartApi\Entity\Vehicle();
            $this->copyVehicleDefFields($vehicle);
        }

        //todo: check access to company

        if (isset($this->data->companyId)) {
            $company = $this->em->find('SafeStartApi\Entity\Company', $this->data->companyId);
            if (!$company) {
                $this->answer = array(
                    "errorMessage" => "Company not found."
                );
                return $this->AnswerPlugin()->format($this->answer, 404);
            }

            $vehicle->setCompany($company);
        }

        $vehicle->setPlantId($this->data->plantId);
        $vehicle->setTitle($this->data->title);
        $vehicle->setType($this->data->type);
        $vehicle->setEnabled((int)$this->data->enabled);
        $vehicle->setRegistrationNumber($this->data->registration);
        $vehicle->setProjectName($this->data->projectName);
        $vehicle->setProjectNumber($this->data->projectNumber);
        $vehicle->setServiceDueKm($this->data->serviceDueKm);
        $vehicle->setServiceDueHours($this->data->serviceDueHours);
        $this->em->persist($vehicle);

        $this->em->flush();

        $this->answer = array(
            'done' => true,
            'vehicleId' => $vehicle->getId(),
        );

        return $this->AnswerPlugin()->format($this->answer);

    }

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

        $vehicle->setDeleted(1);

        $this->em->flush();

        $this->answer = array(
            'done' => true
        );

        return $this->AnswerPlugin()->format($this->answer);
    }

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
            $this->answer[] = $item->toArray();
        }

        return $this->AnswerPlugin()->format($this->answer);

    }

    public function getVehicleChecklistAction()
    {
        // todo: check access

        $vehicleId = (int)$this->getRequest()->getQuery('vehicleId');
        $vehicle = $this->em->find('SafeStartApi\Entity\Vehicle', $vehicleId);
        if (!$vehicle) {
            $this->answer = array(
                "errorMessage" => "Vehicle not found."
            );
            return $this->AnswerPlugin()->format($this->answer, 404);
        }

        $query = $this->em->createQuery('SELECT f FROM SafeStartApi\Entity\Field f WHERE f.deleted = 0 AND f.vehicle = ?1');
        $query->setParameter(1, $vehicle);
        $items = $query->getResult();
        $this->answer = $this->GetDataPlugin()->buildChecklistTree($items);
        return $this->AnswerPlugin()->format($this->answer);
    }

    public function updateVehicleChecklistFiledAction()
    {
        //  todo: check request format;
        $vehicleId = (int) $this->data->vehicleId;
        $vehicle = $this->em->find('SafeStartApi\Entity\Vehicle', $vehicleId);
        if (!$vehicle) {
            $this->answer = array(
                "errorMessage" => "Vehicle not found."
            );
            return $this->AnswerPlugin()->format($this->answer, 404);
        }

        $fieldId = (int)$this->params('id');
        if ($fieldId) {
            $field = $this->em->find('SafeStartApi\Entity\Field', $fieldId);
            if (!$field) {
                $this->answer = array(
                    "errorMessage" => "Checklist Filed not found."
                );
                return $this->AnswerPlugin()->format($this->answer, 404);
            }
        } else {
            $field = new \SafeStartApi\Entity\DefaultField();
        }

        $field->setTitle($this->data->title);

        if (!empty($this->data->parentId) && $this->data->parentId != "NaN") {
            $parentField = $this->em->find('SafeStartApi\Entity\Field', (int) $this->data->parentId);
            if (!$parentField) {
                $this->answer = array(
                    "errorMessage" => "Wrong parent filed."
                );
                return $this->AnswerPlugin()->format($this->answer, 401);
            }
            $field->setParent($parentField);
        }

        if (!in_array($this->data->type, array('root', 'text', 'group', 'radio', 'checkbox', 'photo', 'datePicker'))) {
            $this->answer = array(
                "errorMessage" => "Wrong field type."
            );
            return $this->AnswerPlugin()->format($this->answer, 401);
        }

        $field->setType($this->data->type);
        $field->setOrder((int)$this->data->sort_order);
        $field->setAdditional($this->data->type == 'root' ? (int)$this->data->additional : 0);
        $field->setAlertTitle(($this->data->type == 'radio' || $this->data->type == 'checkbox') ? $this->data->alert_title : '');
        $field->setTriggerValue($this->data->trigger_value);
        $field->setEnabled((int)$this->data->enabled);
        $field->setVehicle($vehicle);

        $this->em->persist($field);
        $field->setAuthor($this->authService->getStorage()->read());


        $this->em->flush();

        $this->answer = array(
            'done' => true,
            'fieldId' => $field->getId(),
        );

        return $this->AnswerPlugin()->format($this->answer);

    }

    public function deleteVehicleChecklistFiledAction()
    {
        // todo: check access

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

        $this->answer = array(
            'done' => true
        );

        return $this->AnswerPlugin()->format($this->answer);
    }
}
