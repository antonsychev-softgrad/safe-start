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

        print_r($veh);

        $this->answer = array(
            'foundInDatabase' => 1,
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
        $expiryDate = $objDateTime->format(\DateTime::RFC850);

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
                            'fieldId' => 0,
                            'fieldOrder' => 1,
                            'fieldName' => 'Test field 1',
                            'fieldType' => 'radioButton',
                            'fieldValue' => 'Yes',
                        ),
                        array(
                            'fieldId' => 1,
                            'fieldOrder' => 2,
                            'fieldName' => 'Test checkbox field 2',
                            'fieldType' => 'checkbox',
                            'fieldValue' => '',
                            'variants' => array(
                                array(
                                    'variantId' => 0,
                                    'variantLabel' => 'First variant'
                                ),
                                array(
                                    'variantId' => 1,
                                    'variantLabel' => 'Second variant'
                                ),
                                array(
                                    'variantId' => 2,
                                    'variantLabel' => 'Third variant'
                                ),
                            ),
                        ),
                        array(
                            'fieldId' => 2,
                            'fieldOrder' => 3,
                            'fieldName' => 'Test field 3',
                            'fieldType' => 'textField',
                            'fieldValue' => 'Test text of the field',
                            'alerts' => array(
                                array(
                                    'alertMessage' => 'Alert! You choose NO',
                                    'fieldCondition' => 'No'
                                )
                            )
                        ),
                    )
                ),
                array(
                    'groupName' => 'Auxiliary Motor',
                    'groupId' => 2,
                    'groupOrder' => 2,
                    'fields' => array(
                        array(
                            'fieldId' => 2,
                            'fieldOrder' => 1,
                            'fieldName' => 'Test field 1',
                            'fieldType' => 'radioButton',
                            'fieldValue' => 'Yes',
                        ),
                        array(
                            'fieldId' => 3,
                            'fieldOrder' => 2,
                            'fieldName' => 'Test field 2',
                            'fieldType' => 'textField',
                            'fieldValue' => 'Test text of the field',
                        ),
                    )
                ),
                array(
                    'groupName' => 'Crane',
                    'groupId' => 3,
                    'groupOrder' => 3,
                    'fields' => array(
                        array(
                            'fieldId' => 4,
                            'fieldOrder' => 1,
                            'fieldName' => 'Test field 1',
                            'fieldType' => 'radioButton',
                            'fieldValue' => 'Yes',
                        ),
                        array(
                            'fieldId' => 5,
                            'fieldOrder' => 2,
                            'fieldName' => 'Test field 2',
                            'fieldType' => 'textField',
                            'fieldValue' => 'Test text of the field',
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
