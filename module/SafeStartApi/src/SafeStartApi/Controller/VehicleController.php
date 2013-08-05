<?php

namespace SafeStartApi\Controller;

use SafeStartApi\Base\RestController;

class VehicleController extends RestController
{
    public function getListAction()
    {

        $vehiclesList = array(
            array(
                'id' => '1',
                'type' => 'Type 1',
            ),
            array(
                'id' => '2',
                'type' => 'Type 2',
            ),
            array(
                'id' => '3',
                'type' => 'Type 3',
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
                    array(
                        'groupName' => 'Auxiliary Motor',
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
