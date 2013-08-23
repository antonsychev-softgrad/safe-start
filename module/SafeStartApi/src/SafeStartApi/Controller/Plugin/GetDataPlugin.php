<?php

namespace SafeStartApi\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use SafeStartApi\Entity\Field;

class GetDataPlugin extends AbstractPlugin
{

    public function buildChecklist($fields)
    {
        $checklist = array();
        foreach ($fields as $field) {
            if ($field->getParent()) continue;
            $checklist[] = array(
                'groupName' => $field->getTitle(),
                'groupId' => $field->getId(),
                'id' => $field->getId(),
                'groupOrder' => $field->getOrder(),
                'additional' => $field->getAdditional(),
                'fields' => $this->_buildChecklist($fields, $field->getId()),
            );
        }
        return $checklist;
    }

    private function _buildChecklist($fields, $parentId = null)
    {
        $checklist = array();
        $fieldsConfig = $this->getController()->moduleConfig['fieldTypes'];
        foreach ($fields as $field) {
            if (!$field->getParent()) continue;
            if ($parentId == $field->getParent()->getId()) {
                $listField = array(
                    'fieldId' => $field->getId(),
                    'id' => $field->getId(),
                    'fieldOrder' => $field->getOrder(),
                    'fieldName' => $field->getTitle(),
                    'fieldType' => $fieldsConfig[$field->getType()]['id'],
                    'type' => $field->getType(),
                    'additional' => $field->getAdditional(),
                    'triggerValue' => $field->getTriggerValue(),
                );
                if ($field->getAdditional() || $field->getType() == 'group') $listField['items'] = $this->_buildChecklist($fields, $field->getId());
                if (isset($fieldsConfig[$field->getType()]['default'])) $listField['fieldValue'] = $fieldsConfig[$field->getType()]['default'];
                if (isset($fieldsConfig[$field->getType()]['options'])) $listField['options'] = $fieldsConfig[$field->getType()]['options'];
                if ($field->getTriggerValue() && !$field->getAdditional()) {
                    $listField['alerts'] = array(
                        array(
                            'alertMessage' => $field->getAlertTitle(),
                            'triggerValue' => $field->getTriggerValue(),
                        )
                    );
                }

                $checklist[] = $listField;
            }
        }

        return $checklist;
    }

}