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
        $vehicle = $vehRep->findBy(array('plant_id' => $plantId));
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

        // save checklist
        if(!empty($this->data->plantId)) {
            $plantId = !empty($this->data->plantId) ? $this->data->plantId : 0;
            $vehicle = $this->em->findBy('SafeStartApi\Entity\Vehicle', array('plantId' => $plantId));
        } else {
            $plantId = uniqid('vehicle');
            $vehicle = null;
        }

        if (!$vehicle) {
            $vehicle = new Vehicle();
            $vehicle->setCompany($this->data->company);
            $vehicle->setEnabled(1);
            $vehicle->setPlantId($plantId);
            $vehicle->setProjectName($this->data->projectName);
            $vehicle->setProjectNumber($this->data->projectNumber);
            $vehicle->setRegistrationNumber($this->data->registrationNumber);
            $vehicle->setServiceDueHours($this->data->serviceDueHours);
            $vehicle->setServiceDueKm($this->data->serviceDueKm);
            $vehicle->setTitle($this->data->title);
            $vehicle->setType($this->data->type);
            $this->em->persist($vehicle);
        } else {
            $vehicle->setCompany($this->data->company);
            $vehicle->setPlantId($plantId);
            $vehicle->setProjectName($this->data->projectName);
            $vehicle->setProjectNumber($this->data->projectNumber);
            $vehicle->setRegistrationNumber($this->data->registrationNumber);
            $vehicle->setServiceDueHours($this->data->serviceDueHours);
            $vehicle->setServiceDueKm($this->data->serviceDueKm);
            $vehicle->setTitle($this->data->title);
            $vehicle->setType($this->data->type);
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
        $checkList->setHash(null);
        $checkList->setGpsCoords((isset($this->data->gps) && !empty($this->data->gps)) ? $this->data->gps : null);
        $checkList->setCurrentOdometer((isset($this->data->odometer) && !empty($this->data->odometer)) ? $this->data->odometer : null);

        $this->em->persist($checkList);
        $this->em->flush();

        $uniqId = uniqid();

        $checkList->setHash($uniqId);
        $this->em->persist($checkList);
        $this->em->flush();

        $this->answer = array(
            'checklist' => $checkList->getHash(),
        );

        $pdf = $this->PdfPlugin($checkList->getId(), false);

        foreach($emails as $email) {
            $this->MailPlugin()->send(
                'Checklist',
                $email,
                'checklist.phtml',
                array(
                ),
                $pdf
            );
        }

        return $this->AnswerPlugin()->format($this->answer);
    }
}
