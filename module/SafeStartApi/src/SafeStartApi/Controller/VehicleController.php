<?php

namespace SafeStartApi\Controller;

use SafeStartApi\Base\RestController;

class VehicleController extends RestController
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

        $id = $this->params('id');
        $vehRep = $this->em->getRepository('SafeStartApi\Entity\Vehicle');
        $veh = $vehRep->findOneById($id);
        if(empty($veh)) return $this->_showNotFound();

        $groups = $veh->getGroups();

        $checklist = array();
        foreach($groups as $group) {

            $checklist[] = array(
                'groupName' => $group->getTitle(),
                'groupId' => $group->getId(),
                'groupOrder' => $group->getOrder(),
                'additional' => $group->getAdditional(),
                'fields' => $this->GetDataPlugin()->getGroupFields($group),
            );
        }

        $this->answer = array(
            'checklist' => $checklist,
        );

        return $this->AnswerPlugin()->format($this->answer);
    }

    public function completeChecklistAction()
    {
        if (!$this->authService->hasIdentity()) return $this->_showUnauthorisedRequest();
        if (!$this->_requestIsValid('vehicle/completechecklist')) return $this->_showBadRequest();

        $id = $this->params('id');

        $this->answer = array(
            'checklist' => '',
        );

        return $this->AnswerPlugin()->format($this->answer);

    }
}
