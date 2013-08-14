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
            'foundInDatabase' => (int)$inDb,
        );

        return $this->AnswerPlugin()->format($this->answer);
    }

    public function getListAction()
    {
        if (!$this->authService->hasIdentity()) return $this->_showUnauthorisedRequest();
        if (!$this->_requestIsValid('vehicle/getlist')) return $this->_showBadRequest();

        $vehiclesList = array(
            array(
                'vehicleId' => 1,
                'typeId' => 1,
                'vehicleName' => 'Vehicle name 1',
            ),
            array(
                'vehicleId' => 2,
                'typeId' => 2,
                'vehicleName' => 'Vehicle name 2',
            ),
            array(
                'vehicleId' => 3,
                'typeId' => 3,
                'vehicleName' => 'Vehicle name 3',
            ),
        );

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

        $objDateTime = new \DateTime('NOW');
        $expiryDate = $objDateTime->getTimestamp();

        $vehicleData = array(
            'vehicleId' => $id,
            'plantId' => 'PLANTIDTEST',
            'registration' => 'REGISTRATION',
            'vehicleName' => 'Name',
            'type' => 'Test vehicle type',
            'projectName' => 'Test project name',
            'projectNumber' => 1123,
            'expiryDate' => $expiryDate,
            'kmsUntilNext' => 150,
            'hoursUntilNext' => 200,
        );

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

        $checklist = array(
                array(
                    'groupName' => 'Trailer',
                    'groupId' => 1,
                    'groupOrder' => 1,
                    'fields' => array(
                        array(
                            'fieldId' => 1,
                            'fieldOrder' => 1,
                            'fieldName' => 'Is the vechicle stable?',
                            'fieldType' => $this->moduleConfig['fieldTypes']['radio']['id'],
                            'fieldValue' =>  $this->moduleConfig['fieldTypes']['radio']['default'],
                            'options' => $this->moduleConfig['fieldTypes']['radio']['options'],
                            'alerts' => array(
                                array(
                                    'alertMessage' => 'Alert! You choose NO',
                                    'triggerValue' => 'NO',
                                )
                            ),
                        ),
                        array(
                            'fieldId' => 2,
                            'fieldOrder' => 2,
                            'fieldName' => 'Is the vechicle clean and all items secure?',
                            'fieldType' => $this->moduleConfig['fieldTypes']['radio']['id'],
                            'fieldValue' => $this->moduleConfig['fieldTypes']['radio']['default'],
                            'options' => $this->moduleConfig['fieldTypes']['radio']['options'],
                            'alerts' => array(
                                array(
                                    'alertMessage' => 'Alert! You choose NO',
                                    'triggerValue' => 'NO',
                                )
                            ),
                        ),
                        array(
                            'fieldId' => 3,
                            'fieldOrder' => 3,
                            'fieldName' => 'Describe the vechicle status.',
                            'fieldType' => $this->moduleConfig['fieldTypes']['text']['id'],
                            'fieldValue' => $this->moduleConfig['fieldTypes']['radio']['default'],
                        ),
                    )
                ),
                array(
                    'groupName' => 'Auxiliary Motor',
                    'groupId' => 2,
                    'groupOrder' => 2,
                    'fields' => array(
                        array(
                            'fieldId' => 4,
                            'fieldOrder' => 1,
                            'fieldName' => '2.1 Some question aobut the vechicle?',
                            'fieldType' => $this->moduleConfig['fieldTypes']['radio']['id'],
                            'fieldValue' =>  $this->moduleConfig['fieldTypes']['radio']['default'],
                            'options' => $this->moduleConfig['fieldTypes']['radio']['options'],
                            'alerts' => array(
                                array(
                                    'alertMessage' => 'Alert! You choose NO',
                                    'triggerValue' => 'NO',
                                )
                            ),
                        ),
                        array(
                            'fieldId' => 5,
                            'fieldOrder' => 2,
                            'fieldName' => '2.2 Some question aobut the vechicle?',
                            'fieldType' => $this->moduleConfig['fieldTypes']['checkbox']['id'],
                            'fieldValue' => $this->moduleConfig['fieldTypes']['checkbox']['default'],
                            'options' => $this->moduleConfig['fieldTypes']['checkbox']['options'],
                            'alerts' => array(
                                array(
                                    'alertMessage' => 'Alert! You choose NO',
                                    'triggerValue' => 'NO',
                                )
                            ),
                        ),
                        array(
                            'fieldId' => 6,
                            'fieldOrder' => 3,
                            'fieldName' => 'Add vechicle CPS coordinates.',
                            'fieldType' => $this->moduleConfig['fieldTypes']['coordinates']['id'],
                            'fieldValue' => $this->moduleConfig['fieldTypes']['coordinates']['default'],
                        ),
                    )
                ),
                array(
                    'groupName' => 'Crane',
                    'groupId' => 3,
                    'groupOrder' => 3,
                    'fields' => array(
                        array(
                            'fieldId' => 7,
                            'fieldOrder' => 1,
                            'fieldName' => 'Are the fluid levels accepatble',
                            'fieldType' => $this->moduleConfig['fieldTypes']['group']['id'],
                            'items' => array(
                                array(
                                    'fieldId' => 8,
                                    'fieldOrder' => 1,
                                    'fieldName' => 'Water',
                                    'fieldType' => $this->moduleConfig['fieldTypes']['checkbox']['id'],
                                    'fieldValue' => $this->moduleConfig['fieldTypes']['checkbox']['default'],
                                    'options' => $this->moduleConfig['fieldTypes']['checkbox']['options'],
                                    'alerts' => array(
                                        array(
                                            'alertMessage' => 'Alert! You choose NO',
                                            'triggerValue' => 'NO',
                                        )
                                    ),
                                ),
                                array(
                                    'fieldId' => 9,
                                    'fieldOrder' => 2,
                                    'fieldName' => 'Hydraulic',
                                    'fieldType' => $this->moduleConfig['fieldTypes']['checkbox']['id'],
                                    'fieldValue' => $this->moduleConfig['fieldTypes']['checkbox']['default'],
                                    'options' => $this->moduleConfig['fieldTypes']['checkbox']['options'],
                                    'alerts' => array(
                                        array(
                                            'alertMessage' => 'Alert! You choose NO',
                                            'triggerValue' => 'NO',
                                        )
                                    ),
                                ),
                                array(
                                    'fieldId' => 9,
                                    'fieldOrder' => 2,
                                    'fieldName' => 'Battery',
                                    'fieldType' => $this->moduleConfig['fieldTypes']['checkbox']['id'],
                                    'fieldValue' => $this->moduleConfig['fieldTypes']['checkbox']['default'],
                                    'options' => $this->moduleConfig['fieldTypes']['checkbox']['options'],
                                    'alerts' => array(
                                        array(
                                            'alertMessage' => 'Alert! You choose NO',
                                            'triggerValue' => 'NO',
                                        )
                                    ),
                                ),
                            )
                        ),
                    )
                )
        );

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
