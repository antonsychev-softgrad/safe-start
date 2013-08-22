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
        $fields = $group->getFields();
        foreach($fields as $field) {

            $fieldsArray[] = $this->getField($field);
        }

        return $fieldsArray;
    }

    public function getAdditionalFields(Field $field)
    {

        $fieldsArray = array();
        $fields = $field->getAdditionalFields();
        foreach($fields as $field) {

            $fieldsArray[] = array(
                'field' => $this->getField($field),
                'triggerValue' => $field->getTriggerValue(),
            );
        }

        return $fieldsArray;
    }

    public function getAlerts(Field $field)
    {
        $alertsArray = array();
        $alerts = $field->getAlerts();
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
        if(!empty($subgroup)) {
            $fields = $subgroup->getFields();
            foreach($fields as $field) {
                $fieldsArray[] = $this->getField($field);
            }
        }
        return $fieldsArray;
    }

    public function getField(Field $field)
    {
        $fieldArray = array(
            'fieldId' => $field->getId(),
            'fieldOrder' => $field->getOrder(),
            'fieldName' => $field->getTitle(),
            'fieldType' => $this->getController()->moduleConfig['fieldTypes'][$field->getType()]['id'],
            'additionalFields' => $this->getAdditionalFields($field),
            'alerts' => $this->getAlerts($field),
            'items' => $this->getSubgroupFields($field),
        );

        if(array_key_exists('default', $this->getController()->moduleConfig['fieldTypes'][$field->getType()])) {
            $fieldArray['fieldValue'] = $this->getController()->moduleConfig['fieldTypes'][$field->getType()]['default'];
        }
        if(array_key_exists('options', $this->getController()->moduleConfig['fieldTypes'][$field->getType()])) {
            $fieldArray['options'] = $this->getController()->moduleConfig['fieldTypes'][$field->getType()]['options'];
        }

        return $fieldArray;
    }
}