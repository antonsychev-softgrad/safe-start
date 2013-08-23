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

           /** /
           $repGroupField = $this->em->getRepository('SafeStartApi\Entity\DefaultField');
           $defFields = $repGroupField->findAll();
           foreach($defFields as $defField) {

           $newField = new \SafeStartApi\Entity\Field();
           $defFieldVars = array_keys($defField->toArray());
           foreach($defFieldVars as $defFieldVar) {

           if(strtolower($defFieldVar) == 'id') {
           continue;
           }

           $setFuncName = 'set';
           $setFuncName .= ucfirst($defFieldVar);

           $getDefFuncName = 'get';
           $getDefFuncName .= ucfirst($defFieldVar);

           if(!method_exists($newField, $setFuncName)) {
           continue;
           }

           $fieldVal = $defField->$getDefFuncName();

           if($fieldVal instanceof \Doctrine\Common\Collections\ArrayCollection) {
           continue;
           }

           $newField->$setFuncName($fieldVal);
           }
           $this->em->persist($newField);
           $vehicle->addField($newField);
           }
           /**/
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
