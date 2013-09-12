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

        // save checklist
        $vehicleId = $this->params('id');
        $vehicle = $this->em->find('SafeStartApi\Entity\Vehicle', $vehicleId);

        if (!$vehicle) {
            $vehicle = new Vehicle();
            $vehicle->setCompany($this->data->company);
            $vehicle->setEnabled(1);
            $vehicle->setPlantId($this->data->plant_id);
            $vehicle->setProjectName($this->data->project_name);
            $vehicle->setProjectNumber($this->data->project_number);
            $vehicle->setRegistrationNumber($this->data->registration_number);
            $vehicle->setServiceDueHours($this->data->service_due_hours);
            $vehicle->setServiceDueKm($this->data->service_due_km);
            $vehicle->setTitle($this->data->title);
            $vehicle->setType($this->data->type);
            $this->em->persist($vehicle);
        } else {
            $vehicle->setCompany($this->data->company);
            $vehicle->setPlantId($this->data->plant_id);
            $vehicle->setProjectName($this->data->project_name);
            $vehicle->setProjectNumber($this->data->project_number);
            $vehicle->setRegistrationNumber($this->data->registration_number);
            $vehicle->setServiceDueHours($this->data->service_due_hours);
            $vehicle->setServiceDueKm($this->data->service_due_km);
            $vehicle->setTitle($this->data->title);
            $vehicle->setType($this->data->type);
        }

        $query = $this->em->createQuery('SELECT f FROM SafeStartApi\Entity\Field f WHERE f.deleted = 0 AND f.enabled = 1 AND f.vehicle = ?1');
        $query->setParameter(1, $vehicle);
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
                $newAlert->setVehicle($vehicle);
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
}
