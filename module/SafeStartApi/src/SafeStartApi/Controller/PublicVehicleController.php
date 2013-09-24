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
            $plantId = $this->data->plantId;
            $vehicle = $this->em->getRepository('SafeStartApi\Entity\Vehicle')->findBy(array('plantId' => $plantId));
        } else {
            $plantId = uniqid('vehicle');
            $vehicle = new Vehicle();
            $vehicle->setEnabled(1);
        }

        $projectName = !empty($this->data->projectName) ? $this->data->projectName : '';
        $projectNumber = !empty($this->data->projectNumber) ? $this->data->projectNumber : 0;
        $registrationNumber = !empty($this->data->registrationNumber) ? $this->data->registrationNumber : '';
        $serviceDueHours = !empty($this->data->serviceDueHours) ? $this->data->serviceDueHours : 0;
        $serviceDueKm = !empty($this->data->serviceDueKm) ? $this->data->serviceDueKm : 0;
        $title = !empty($this->data->title) ? $this->data->title : '';
        $type = !empty($this->data->type) ? $this->data->type : '';

        $vehicle->setPlantId($plantId);
        $vehicle->setProjectName($projectName);
        $vehicle->setProjectNumber($projectNumber);
        $vehicle->setRegistrationNumber($registrationNumber);
        $vehicle->setServiceDueHours($serviceDueHours);
        $vehicle->setServiceDueKm($serviceDueKm);
        $vehicle->setTitle($title);
        $vehicle->setType($type);

        if(empty($this->data->plantId)) {
            $this->em->persist($vehicle);
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
        $checkList->setGpsCoords((isset($this->data->gps) && !empty($this->data->gps)) ? $this->data->gps : null);
        $checkList->setCurrentOdometer((isset($this->data->odometer) && !empty($this->data->odometer)) ? $this->data->odometer : null);
        $checkList->setCurrentOdometerHours((isset($this->data->odometer_hours) && !empty($this->data->oodometer_hours)) ? $this->data->odometer_hours : null);
        $uniqId = uniqid();
        $checkList->setHash($uniqId);
        $this->em->persist($checkList);

        // save new alerts
        $alerts = array();
        if (!empty($this->data->alerts) && is_array($this->data->alerts)) {
            $alerts = $this->data->alerts;
            foreach ($alerts as $alert) {
                $field = $this->em->find('SafeStartApi\Entity\DefaultField', $alert->fieldId);
                if ($field === null) {
                    continue;
                }
                $newAlert = new \SafeStartApi\Entity\Alert();
                $newAlert->setDefaultField($field);
                $newAlert->setCheckList($checkList);
                $newAlert->setDescription(!empty($alert->comment) ? $alert->comment : null);
                $newAlert->setImages(!empty($alert->images) ? $alert->images : array());
                $newAlert->setVehicle($vehicle);

                $this->em->persist($newAlert);
            }
        }
        $this->em->flush();

        $this->answer = array(
            'checklist' => $checkList->getHash(),
        );

        $pdf = $this->PdfPlugin($checkList->getId(), true);

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
