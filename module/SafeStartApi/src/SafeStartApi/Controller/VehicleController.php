<?php

namespace SafeStartApi\Controller;

use SafeStartApi\Base\RestController;

class VehicleController extends RestController
{
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

        $id = $this->params('id');

        $objDateTime = new \DateTime('NOW');
        $expiryDate = $objDateTime->format(\DateTime::RFC850);

        $vehicleData = array(
            'id' => $id,
            'vehicleName' => 'Name',
            'type' => 'Test vehicle type',
            'projectName' => 'Test project name',
            'projectType' => 'Test project type',
            'expiryDate' => $expiryDate,
            'kmsUntilNext' => 150,
            'hoursUntilNext' => 200,
        );

        $this->answer = array(
            'vehicleData' => $vehicleData,
        );

        return $this->AnswerPlugin()->format($this->answer);
    }

    public function getChecklistByVehicleIdAction()
    {
        if (!$this->authService->hasIdentity()) return $this->_showUnauthorisedRequest();
        if (!$this->_requestIsValid('vehicle/getchecklist')) return $this->_showBadRequest();

        $id = $this->params('id');

        $checklist = array(
            'groups' => array(
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
                            'fieldName' => 'Test field 2',
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
                ),
            )
        );

        $this->answer = array(
            'checklist' => $checklist,
        );

        return $this->AnswerPlugin()->format($this->answer);
    }
}
