<?php

namespace SafeStartApi\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use SafeStartApi\Entity\Field;

class GetDataPlugin extends AbstractPlugin
{

    public function buildChecklist($fields, \SafeStartApi\Entity\CheckList $inspection = null)
    {
        $checklist = array();
        foreach ($fields as $field) {
            if ($field->getParent()) continue;
            $checklist[] = array(
                'groupName' => $field->getTitle(),
                'fieldDescription' => $field->getDescription(),
                'groupId' => $field->getId(),
                'id' => $field->getId(),
                'groupOrder' => $field->getOrder(),
                'additional' => $field->getAdditional(),
                'fields' => $this->_buildChecklist($fields, $field->getId(), $inspection),
            );
        }
        return $checklist;
    }

    public function getChecklistStructure($fields)
    {
        $checklist = array();
        foreach ($fields as $field) {
            if ($field->getParent()) continue;
            $checklist[] = array(
                'groupName' => $field->getTitle(),
                'fieldDescription' => $field->getDescription(),
                'groupId' => $field->getId(),
                'id' => $field->getId(),
                'groupOrder' => $field->getOrder(),
                'additional' => $field->getAdditional(),
                'fields' => $this->_buildChecklistStructure($fields, $field->getId()),
            );
        }
        return $checklist;
    }

    public function buildChecklistTree($items, $parentId = null)
    {
        $tree = array();
        foreach ($items as $item) {
            $itemParentId = $item->getParent() ? $item->getParent()->getId() : 0;
            if ($parentId == $itemParentId) {
                $treeItem = $item->toArray();
                $treeItem['data'] = $this->buildChecklistTree($items, $item->getId());
                $tree[] = $treeItem;
            }
        }
        return $tree;
    }

    private function _buildChecklist($fields, $parentId = null, $inspection = null)
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
                    'fieldDescription' => $field->getDescription(),
                    'fieldType' => isset($fieldsConfig[$field->getType()]) ? $fieldsConfig[$field->getType()]['id'] : 0,
                    'alertMessage' => $field->getAlertTitle(),
                    'alertDescription' => $field->getAlertDescription(),
                    'alertCritical' => $field->getAlertCritical(),
                    'type' => $field->getType(),
                    'additional' => $field->getAdditional(),
                    'triggerValue' => $field->getTriggerValue(),
                );
                $listField['defaultValue'] = $field->getDefaultValue();
                if ($inspection) {
                    $listField['fieldValue'] = $inspection->getFieldValue($field);
                } else if ($field->getDefaultValue()) {
                    $listField['fieldValue'] = $field->getDefaultValue();
                } else {
                    if (isset($fieldsConfig[$field->getType()]['default'])) $listField['fieldValue'] = $fieldsConfig[$field->getType()]['default'];
                }
                $listField['items'] = $this->_buildChecklist($fields, $field->getId(), $inspection);
                if (isset($fieldsConfig[$field->getType()]['options'])) $listField['options'] = $fieldsConfig[$field->getType()]['options'];
                $alertMassage = $field->getAlertTitle();
                $alertDescription = $field->getAlertDescription();

                $tempDescription = "";
                switch(true) {
                    case !empty($alertDescription):
                        $tempDescription = $alertDescription;
                        break;
                    case !empty($alertMassage):
                        $tempDescription = $alertMassage;
                        break;
                    case ("" !== $field->getDescription()):
                        $tempDescription = $field->getDescription();
                        break;
                    case ("" !== $field->getTitle()):
                        $tempDescription = $field->getTitle();
                        break;
                    default:
                        $tempDescription = "";
                        break;
                }

                if ((!empty($alertMassage) || !empty($tempDescription)) && empty($listField['items'])) {
                    $listField['alerts'] = array(
                        array(
                            'alertMessage' => $field->getAlertTitle(),
                            'alertDescription' => $tempDescription,
                            'critical' => $field->getAlertCritical(),
                            'triggerValue' => $field->getTriggerValue() ? $field->getTriggerValue() : '',
                        )
                    );
                } else if (!empty($listField['items']) && $field->getType() != 'group') {
                    $listField['additional'] = true;
                } else {
                    $listField['additional'] = false;
                }

                $checklist[] = $listField;
            }
        }

        return $checklist;
    }

    private function _buildChecklistStructure($fields, $parentId = null)
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
                    'fieldDescription' => $field->getDescription(),
                    'alertMessage' => $field->getAlertTitle(),
                    'alertDescription' => $field->getAlertDescription(),
                    'alertCritical' => $field->getAlertCritical(),
                    'fieldType' => $fieldsConfig[$field->getType()]['id'],
                    'type' => $field->getType(),
                    'additional' => $field->getAdditional(),
                    'triggerValue' => $field->getTriggerValue(),
                    'alertTitle' => $field->getAlertDescription(),
                    'alertMessage' => $field->getAlertTitle(),
                );
                $listField['defaultValue'] = $field->getDefaultValue();
                if ($field->getDefaultValue()) {
                    $listField['fieldValue'] = $field->getDefaultValue();
                } else {
                    if (isset($fieldsConfig[$field->getType()]['default'])) $listField['fieldValue'] = $fieldsConfig[$field->getType()]['default'];
                }
                $listField['items'] = $this->_buildChecklistStructure($fields, $field->getId());
                if (isset($fieldsConfig[$field->getType()]['default'])) $listField['fieldValue'] = $fieldsConfig[$field->getType()]['default'];
                if (isset($fieldsConfig[$field->getType()]['options'])) $listField['options'] = $fieldsConfig[$field->getType()]['options'];
                $alertMassage = $field->getAlertTitle();
                $alertDescription = $field->getAlertDescription();
                if ((!empty($alertMassage) || !empty($alertDescription)) && empty($listField['items'])) {
                    $listField['alerts'] = array(
                        array(
                            'alertMessage' => $field->getAlertTitle(),
                            'alertDescription' => $field->getAlertDescription(),
                            'critical' => $field->getAlertCritical(),
                            'triggerValue' => $field->getTriggerValue() ? $field->getTriggerValue() : '',
                        )
                    );
                } else if (!empty($listField['items']) && $field->getType() != 'group') {
                    $listField['additional'] = true;
                } else {
                    $listField['additional'] = false;
                }

                $checklist[] = $listField;
            }
        }

        return $checklist;
    }


}