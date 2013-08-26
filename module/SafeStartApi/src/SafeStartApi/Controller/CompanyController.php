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
                $parent->addChildren($newFild);
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
}
