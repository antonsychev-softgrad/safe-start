<?php

namespace SafeStartApi\Controller;

use SafeStartApi\Base\RestController;

class VehicleController extends RestController
{
    public function getListAction()
    {

        $vehiclesList = array(
            array(
                'vehicleId' => 1,
                'typeId' => 1,
            ),
            array(
                'vehicleId' => 1,
                'typeId' => 1,
            ),
            array(
                'vehicleId' => 1,
                'typeId' => 1,
            ),
        );

        $this->answer = array(
            'vehicles' => $vehiclesList,
        );

        return $this->AnswerPlugin()->format($this->answer);
    }

    public function getDataByIdAction()
    {

        $objDateTime = new \DateTime('NOW');
        $expiryDate = $objDateTime->format(\DateTime::RFC850);

        $vehicleData = array(
                'id' => '1',
                'type' => 'Test vehicle type',
                'projectName' => 'Test project name',
                'projectType' => 'Test project type',
                'expiryDate' => $expiryDate,
                'kmsUntilNext' => 150,
                'hoursUntilNext' => 200,
                'groups' => array(
                    array(
                        'groupName' => 'Trailer',
                        'fields' => array(
                            array(
                                'fieldId' => 0,
                                'fieldName' => 'Test field 1',
                                'fieldType' => 'radioButton',
                                'fieldValue' => 'Yes',
                            ),
                            array(
                                'fieldId' => 1,
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
                        'fields' => array(
                            array(
                                'fieldId' => 2,
                                'fieldName' => 'Test field 1',
                                'fieldType' => 'radioButton',
                                'fieldValue' => 'Yes',
                            ),
                            array(
                                'fieldId' => 3,
                                'fieldName' => 'Test field 2',
                                'fieldType' => 'textField',
                                'fieldValue' => 'Test text of the field',
                            ),
                        )
                    ),
                    array(
                        'groupName' => 'Crane',
                        'fields' => array(
                            array(
                                'fieldName' => 'Test field 1',
                                'fieldType' => 'radioButton',
                                'fieldValue' => 'Yes',
                            ),
                            array(
                                'fieldName' => 'Test field 2',
                                'fieldType' => 'textField',
                                'fieldValue' => 'Test text of the field',
                            ),
                        )
                    ),
                )
        );

        $this->answer = array(
            'vehicleData' => $vehicleData,
        );

        return $this->AnswerPlugin()->format($this->answer);
    }
}
