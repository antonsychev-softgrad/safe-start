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

        $rootFields = $veh->getFields();

        $checklist = array();
        foreach($rootFields as $field) {

            $checklist[] = array(
                'groupName' => $field->getTitle(),
                'groupId' => $field->getId(),
                'groupOrder' => $field->getOrder(),
                'additional' => $field->getAdditional(),
                'fields' => $this->GetDataPlugin()->getGroupFields($field),
            );
        }

        /*
        $checklist = array(
                array(
                    'groupName' => 'Daily inspection checklist structural',
                    'groupId' => 1,
                    'groupOrder' => 1,
                    'additional' => false,
                    'fields' => array(
                        array(
                            'fieldId' => 1,
                            'fieldOrder' => 1,
                            'fieldName' => 'Is the vehicle free of damage?',
                            'fieldType' => $this->moduleConfig['fieldTypes']['radio']['id'],
                            'fieldValue' =>  $this->moduleConfig['fieldTypes']['radio']['default'],
                            'options' => $this->moduleConfig['fieldTypes']['radio']['options'],
                        ),
                        array(
                            'fieldId' => 2,
                            'fieldOrder' => 2,
                            'fieldName' => 'Are all safety guards in place?',
                            'fieldType' => $this->moduleConfig['fieldTypes']['radio']['id'],
                            'fieldValue' =>  $this->moduleConfig['fieldTypes']['radio']['default'],
                            'options' => $this->moduleConfig['fieldTypes']['radio']['options'],
                        ),
                        array(
                            'fieldId' => 3,
                            'fieldOrder' => 3,
                            'fieldName' => 'Are the tyres correctly inflated, with good tread and wheel nuts tight?',
                            'fieldType' => $this->moduleConfig['fieldTypes']['radio']['id'],
                            'fieldValue' =>  $this->moduleConfig['fieldTypes']['radio']['default'],
                            'options' => $this->moduleConfig['fieldTypes']['radio']['options'],
                            'additionalFields' => array(
                                array(
                                    'field' => array(
                                        'fieldId' => 4,
                                        'fieldOrder' => 4,
                                        'fieldName' => 'Are you authorised to inflate or change tyres?',
                                        'fieldType' => $this->moduleConfig['fieldTypes']['radio']['id'],
                                        'fieldValue' =>  $this->moduleConfig['fieldTypes']['radio']['default'],
                                        'options' => $this->moduleConfig['fieldTypes']['radio']['options'],
                                        'alerts' => array(
                                            array(
                                                'alertMessage' => 'Do not work on tyres unless authorised',
                                                'triggerValue' => 'No',
                                            )
                                        ),
                                    ),
                                    'triggerValue' => 'No'
                                ),
                            ),
                        ),
                        array(
                            'fieldId' => 5,
                            'fieldOrder' => 5,
                            'fieldName' => 'Is the windscreen and mirrors clean and free of damage?',
                            'fieldType' => $this->moduleConfig['fieldTypes']['radio']['id'],
                            'fieldValue' =>  $this->moduleConfig['fieldTypes']['radio']['default'],
                            'options' => $this->moduleConfig['fieldTypes']['radio']['options'],
                        ),
                    )
                ),
                array(
                    'groupName' => 'Daily inspection checklist mechanical',
                    'groupId' => 2,
                    'groupOrder' => 2,
                    'additional' => false,
                    'fields' => array(
                        array(
                            'fieldId' => 6,
                            'fieldOrder' => 1,
                            'fieldName' => 'Have you isolated the vechicle?',
                            'fieldType' => $this->moduleConfig['fieldTypes']['radio']['id'],
                            'fieldValue' =>  $this->moduleConfig['fieldTypes']['radio']['default'],
                            'options' => $this->moduleConfig['fieldTypes']['radio']['options'],
                            'alerts' => array(
                                array(
                                    'alertMessage' => 'Isolate vehicle before continuing',
                                    'triggerValue' => 'No',
                                )
                            ),
                        ),
                        array(
                            'fieldId' => 7,
                            'fieldOrder' => 2,
                            'fieldName' => 'Are the fluid levels acceptable?',
                            'fieldType' => $this->moduleConfig['fieldTypes']['group']['id'],
                            'items' => array(
                                array(
                                    'fieldId' => 8,
                                    'fieldOrder' => 3,
                                    'fieldName' => 'Water',
                                    'fieldType' => $this->moduleConfig['fieldTypes']['checkbox']['id'],
                                    'fieldValue' => $this->moduleConfig['fieldTypes']['checkbox']['default'],
                                    'options' => $this->moduleConfig['fieldTypes']['checkbox']['options'],
                                ),
                                array(
                                    'fieldId' => 9,
                                    'fieldOrder' => 4,
                                    'fieldName' => 'Hydraulic',
                                    'fieldType' => $this->moduleConfig['fieldTypes']['checkbox']['id'],
                                    'fieldValue' => $this->moduleConfig['fieldTypes']['checkbox']['default'],
                                    'options' => $this->moduleConfig['fieldTypes']['checkbox']['options'],
                                ),
                                array(
                                    'fieldId' => 10,
                                    'fieldOrder' => 5,
                                    'fieldName' => 'Brake',
                                    'fieldType' => $this->moduleConfig['fieldTypes']['checkbox']['id'],
                                    'fieldValue' => $this->moduleConfig['fieldTypes']['checkbox']['default'],
                                    'options' => $this->moduleConfig['fieldTypes']['checkbox']['options'],
                                ),
                                array(
                                    'fieldId' => 11,
                                    'fieldOrder' => 6,
                                    'fieldName' => 'Coolant',
                                    'fieldType' => $this->moduleConfig['fieldTypes']['checkbox']['id'],
                                    'fieldValue' => $this->moduleConfig['fieldTypes']['checkbox']['default'],
                                    'options' => $this->moduleConfig['fieldTypes']['checkbox']['options'],
                                ),
                                array(
                                    'fieldId' => 12,
                                    'fieldOrder' => 7,
                                    'fieldName' => 'Transmission',
                                    'fieldType' => $this->moduleConfig['fieldTypes']['checkbox']['id'],
                                    'fieldValue' => $this->moduleConfig['fieldTypes']['checkbox']['default'],
                                    'options' => $this->moduleConfig['fieldTypes']['checkbox']['options'],
                                ),
                                array(
                                    'fieldId' => 13,
                                    'fieldOrder' => 8,
                                    'fieldName' => 'Battery',
                                    'fieldType' => $this->moduleConfig['fieldTypes']['checkbox']['id'],
                                    'fieldValue' => $this->moduleConfig['fieldTypes']['checkbox']['default'],
                                    'options' => $this->moduleConfig['fieldTypes']['checkbox']['options'],
                                ),
                            )
                        ),
                    )
                ),
                array(
                    'groupName' => 'Crane',
                    'groupId' => 3,
                    'groupOrder' => 3,
                    'additional' => true,
                    'fields' => array(
                        array(
                            'fieldId' => 14,
                            'fieldOrder' => 1,
                            'fieldName' => 'Add vechicle CPS coordinates.',
                            'fieldType' => $this->moduleConfig['fieldTypes']['coordinates']['id'],
                            'fieldValue' => $this->moduleConfig['fieldTypes']['coordinates']['default'],
                        ),
                        array(
                            'fieldId' => 15,
                            'fieldOrder' => 2,
                            'fieldName' => 'Add date.',
                            'fieldType' => $this->moduleConfig['fieldTypes']['datePicker']['id'],
                            'fieldValue' => $this->moduleConfig['fieldTypes']['datePicker']['default'],
                        ),
                    )
                )
        );
        */

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
