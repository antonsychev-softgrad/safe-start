<?php

namespace SafeStartApi\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use SafeStartApi\Entity\Group;
use SafeStartApi\Entity\Field;

class GetDataPlugin extends AbstractPlugin
{

    public function getGroupFields(Group $group)
    {

        $fieldsArray = array();
        $fields = $group->getFields()->toArray();
        foreach($fields as $field) {

            $fieldsArray[] = $this->getField($field);
        }

        return $fieldsArray;
    }

    public function getAdditionalFields(Field $field)
    {

        $fieldsArray = array();
        $fields = $field->getAdditionalFields()->toArray();
        foreach($fields as $field) {

            $fieldsArray[] = array(
                'field' => $this->getFields($field),
                'triggerValue' => $field->getTriggerValue(),
            );
        }

        return $fieldsArray;
    }

    public function getAlerts(Field $field)
    {
        $alertsArray = array();
        $alerts = $field->getAlerts()->toArray();
        foreach($alerts as $alert) {
            $alertsArray[] = array(
                'alertMessage' => $alert->getTitle(),
                'triggerValue' => $alert->getTriggerValue(),
            );
        }
        return $alertsArray;
    }

    public function getSubgroupFields(Field $field)
    {
        $subgroup = $field->getSubgroup();

        $fieldsArray = array();
        $fields = $subgroup->getFields()->toArray();
        foreach($fields as $field) {
            $fields[] = $this->getField($field);
        }
        return $fields;
    }

    public function getField(Field $field)
    {
        return array(
            'fieldId' => $field->getId(),
            'fieldOrder' => $field->getOrder(),
            'fieldName' => $field->getTitle(),
            'fieldType' => $this->moduleConfig['fieldTypes'][$field->getType()]['id'],
            'fieldValue' =>  $this->moduleConfig['fieldTypes'][$field->getType()]['default'],
            'options' => $this->moduleConfig['fieldTypes'][$field->getType()]['options'],
            'additionalFields' => $this->getAdditionalFields($field),
            'alerts' => $this->getAlerts($field),
            'items' => $this->getSubgroupFields($field),
        );
    }
}