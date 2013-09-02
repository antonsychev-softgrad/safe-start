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

    public function completeChecklistAction()
    {
        if (!$this->authService->hasIdentity()) return $this->_showUnauthorisedRequest();
        if (!$this->_requestIsValid('vehicle/completechecklist')) return $this->_showBadRequest();

        // save checklist
        $vehicleId = $this->params('id');
        $vehicle = $this->em->find('SafeStartApi\Entity\Vehicle', $vehicleId);
        if (!$vehicle) return $this->_showNotFound("Vehicle not found.");

        $user = $this->authService->getStorage()->read();

        $query = $this->em->createQuery('SELECT f FROM SafeStartApi\Entity\Field f WHERE f.deleted = 0 AND f.enabled = 1 AND f.vehicle = ?1');
        $query->setParameter(1, $vehicle);
        $items = $query->getResult();

        $fieldsStructure = $this->GetDataPlugin()->buildChecklist($items);
        foreach($fieldsStructure as $structKey => $struct) {
            if(isset($struct['fields']) && is_array($struct['fields'])) {
                foreach($struct['fields'] as $fieldKey => $field) {
                    $id = (int) $field['id'];
                    $query = $this->em->createQuery('SELECT f.alert_title FROM SafeStartApi\Entity\Field f WHERE f.id = ?1');
                    $query->setParameter(1, $id);
                    $alertTitle = $query->getSingleScalarResult();
                    if(!empty($alertTitle)) {
                        $fieldsStructure[$structKey]['fields'][$fieldKey]['alertTitle'] = $alertTitle;
                    }
                }
            }
        }

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
        if(isset($this->data->alerts) && !empty($this->data->alerts)) {
            $alerts = $this->data->alerts;
            foreach($alerts as $alert) {

                $field = $this->em->find('SafeStartApi\Entity\Field', $alert->fieldId);
                if($field === null) {
                    continue;
                }

                $newAlert = new \SafeStartApi\Entity\Alert();
                $newAlert->setField($field);
                $newAlert->setComment(isset($alert->comment) ? $alert->comment : null);
                $newAlert->setImages(isset($alert->images) ? json_encode($alert->images) : null);
                $this->em->persist($newAlert);
            }
            $this->em->flush();
        }

        $this->answer = array(
            'checklist' => $checkList->getHash(),
        );

        return $this->AnswerPlugin()->format($this->answer);

    }
}
