<?php
namespace SafeStartApi\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class ProcessTrailerPlugin extends AbstractPlugin
{
    const TRAILER_CHECKLIST_NAME = 'Trailer';
    const TRAILER_PLANT_ID_FIELD_NAME = 'Plant ID';
    const TRAILER_TYPE_FIELD_NAME = 'Type of trailer';
    const TRAILER_MAKE_FIELD_NAME = 'Trailer Make';
    const TRAILER_MODEL_FIELD_NAME = 'Trailer Model';

    public function getTrailerPlantIdFromChecklist(\SafeStartApi\Entity\CheckList $checkList) {
        $vehicle = $checkList->getVehicle();
        $fieldsStructure = json_decode($checkList->getFieldsStructure());
        $fieldsData = json_decode($checkList->getFieldsData());

        if (! $trailerStructure = $this->_findTrailerFieldsStructure($fieldsStructure)) {
            return null;
        }
        $trailerFieldsData = $this->_getTrailerFieldsData($trailerStructure, $fieldsData);
        if (! $plantId = strtoupper($this->_findFieldValue($trailerStructure, $fieldsData, self::TRAILER_PLANT_ID_FIELD_NAME))) {
            return null;
        }

        return $plantId;
    }

    public function processTrailer($trailer, \SafeStartApi\Entity\CheckList $checkList, $alerts) {
        $vehicle = $checkList->getVehicle();
        $user = $checkList->getUser();
        $fieldsStructure = json_decode($checkList->getFieldsStructure());
        $fieldsData = json_decode($checkList->getFieldsData());

        if (! $plantId = $this->getTrailerPlantIdFromChecklist($checkList)) {
            return;
        }

        if (! $trailerStructure = $this->_findTrailerFieldsStructure($fieldsStructure)) {
            return;
        }
        $trailerFieldsData = $this->_getTrailerFieldsData($trailerStructure, $fieldsData);
        if (! $plantId = strtoupper($this->_findFieldValue($trailerStructure, $fieldsData, self::TRAILER_PLANT_ID_FIELD_NAME))) {
            return;
        }

        //$type = $this->_findFieldValue($trailerStructure, $fieldsData, self::TRAILER_TYPE_FIELD_NAME);
        $model = $this->_findFieldValue($trailerStructure, $fieldsData, self::TRAILER_MODEL_FIELD_NAME);
        if (!$model) {
            $model = $this->_findFieldValue($trailerStructure, $fieldsData, self::TRAILER_TYPE_FIELD_NAME);
        }
        $make = $this->_findFieldValue($trailerStructure, $fieldsData, self::TRAILER_MAKE_FIELD_NAME);
        //$registration = $this->_findFieldValue($trailerStructure, $fieldsData, self::TRAILER_TYPE_FIELD_NAME);
        
        $repository = $this->getController()->em->getRepository('SafeStartApi\Entity\Vehicle');

        $company = $vehicle->getCompany();

        if (! $trailer) {
            $trailer = new \SafeStartApi\Entity\Vehicle();

            $trailer->setCompany($vehicle->getCompany());
            $trailer->setPlantId($plantId);
            $trailer->setTitle($model);
            $trailer->setType($make);
            $trailer->setEnabled($vehicle->getEnabled());
            $trailer->setProjectName($vehicle->getProjectName());
            $trailer->setProjectNumber($vehicle->getProjectNumber());
            $trailer->setServiceDueKm($vehicle->getServiceDueKm());
            $trailer->setServiceDueHours($vehicle->getServiceDueHours());
            $trailer->setCurrentOdometerHours($vehicle->getCurrentOdometerHours());
            $trailer->setCurrentOdometerKms($vehicle->getCurrentOdometerKms());
            $trailer->setInspectionDueKms($vehicle->getInspectionDueKms());
            $trailer->setInspectionDueHours($vehicle->getInspectionDueHours());
            $trailer->setWarrantyStartOdometer($vehicle->getWarrantyStartOdometer());
            $trailer->setWarrantyStartDate($vehicle->getWarrantyStartDate());

            foreach ($vehicle->getUsers() as $user) {
                $trailer->addUser($user);
            }
            foreach ($vehicle->getResponsibleUsers() as $user) {
                $trailer->addResponsibleUser($user);
            }
            $this->getController()->em->persist($trailer);
        }


        $newCheckList = new \SafeStartApi\Entity\CheckList();
        $uniqId = uniqid();
        $newCheckList->setHash($uniqId);
        $newCheckList->setVehicle($trailer);
        $newCheckList->setUser($user);

        $newCheckList->setFieldsStructure(json_encode(array($trailerStructure)));
        $newCheckList->setFieldsData(json_encode($trailerFieldsData));
        $this->getController()->em->persist($newCheckList);

        $repField = $this->getController()->em->getRepository('SafeStartApi\Entity\DefaultField');
        $defFields = $repField->findBy(array('parent' => null, 'title' => self::TRAILER_CHECKLIST_NAME));

        foreach ($defFields as $defField) {
            $newField = new \SafeStartApi\Entity\Field();
            $newField->setParent(null);
            $newField->setVehicle($trailer);
            $newField->setTitle($defField->getTitle());
            $newField->setDescription($defField->getDescription());
            $newField->setType($defField->getType());
            $newField->setAdditional(false);
            $newField->setTriggerValue($defField->getTriggerValue());
            $newField->setAlertTitle($defField->getAlertTitle());
            $newField->setAlertDescription($defField->getAlertDescription());
            $newField->setAlertCritical($defField->getAlertCritical());
            $newField->setOrder($defField->getOrder());
            $newField->setEnabled($defField->getEnabled());
            $newField->setDeleted($defField->getDeleted());
            $newField->setAuthor($defField->getAuthor());
            $this->copyVehicleDefFields($trailer, $defField, $newField);

            $this->getController()->em->persist($newField);
            $trailer->addField($newField);
            $this->getController()->em->persist($trailer);
        }

        $trailerIds = array();

        foreach ($trailerStructure->fields as $field) {
            $trailerIds[] = $field->fieldId;
        }

        foreach ($alerts as $alert) {
            if (in_array($alert->getField()->getId(), $trailerIds)) {
                $newAlert = new \SafeStartApi\Entity\Alert();
                $newAlert->setField($alert->getField());
                $newAlert->setCheckList($newCheckList);
                $newAlert->setDescription($alert->getDescription());
                $newAlert->setImages($alert->getImages());
                if ($trailer) {
                    $newAlert->setVehicle($trailer);
                }
                $this->getController()->em->persist($newAlert);
            }
        }

        $this->getController()->em->flush();
    }

    public function processHiddenTrailer(\SafeStartApi\Entity\CheckList $checkList, $alerts) {
        $vehicle = $checkList->getVehicle();
        $user = $checkList->getUser();
        $fieldsStructure = json_decode($checkList->getFieldsStructure());
        $fieldsData = json_decode($checkList->getFieldsData());

        if (! $trailerStructure = $this->_findTrailerFieldsStructure($fieldsStructure)) {
            return;
        }

        $trailerFieldsData = $this->_getTrailerFieldsData($trailerStructure, $fieldsData);
        if (! $plantId = strtoupper($this->_findFieldValue($trailerStructure, $fieldsData, self::TRAILER_PLANT_ID_FIELD_NAME))) {
            return;
        }

        //$type = $this->_findFieldValue($trailerStructure, $fieldsData, self::TRAILER_TYPE_FIELD_NAME);
        $model = $this->_findFieldValue($trailerStructure, $fieldsData, self::TRAILER_MODEL_FIELD_NAME);
        if (!$model) {
            $model = $this->_findFieldValue($trailerStructure, $fieldsData, self::TRAILER_TYPE_FIELD_NAME);
        }
        $make = $this->_findFieldValue($trailerStructure, $fieldsData, self::TRAILER_MAKE_FIELD_NAME);

        $company = $vehicle->getCompany();

        $newCheckList = new \SafeStartApi\Entity\CheckList();
        $uniqId = uniqid();
        $newCheckList->setHash($uniqId);
        $newCheckList->setVehicle($vehicle);
        $newCheckList->setUser($user);
        $newCheckList->setFieldsStructure(json_encode(array($trailerStructure)));
        $newCheckList->setFieldsData(json_encode($trailerFieldsData));
        $this->getController()->em->persist($newCheckList);

        $repField = $this->getController()->em->getRepository('SafeStartApi\Entity\DefaultField');
        $defFields = $repField->findBy(array('parent' => null, 'title' => self::TRAILER_CHECKLIST_NAME));

        $trailerIds = array();

        foreach ($trailerStructure->fields as $field) {
            $trailerIds[] = $field->fieldId;
        }

        foreach ($alerts as $alert) {
            if (in_array($alert->getField()->getId(), $trailerIds)) {
                $newAlert = new \SafeStartApi\Entity\Alert();
                $newAlert->setField($alert->getField());
                $newAlert->setCheckList($newCheckList);
                $newAlert->setDescription($alert->getDescription());
                $newAlert->setImages($alert->getImages());
                if ($trailer) {
                    $newAlert->setVehicle($trailer);
                }
            }
        }

        $pdf = $this->getController()->inspectionPdf()->create($newCheckList);

        $admin = $company->getAdmin();
        if (file_exists($pdf)) {
            $this->getController()->MailPlugin()->send(
                $this->getController()->moduleConfig['params']['emailSubjects']['new_vehicle_inspection'],
                $admin->getEmail(),
                'checklist.phtml',
                array(
                    'name' => $admin->getFirstName() . ' ' . $admin->getLastName(),
                    'plantId' => $newCheckList->getVehicle() ? $newCheckList->getVehicle()->getPlantId() : '-',
                    'uploadedByName' => $newCheckList->getOperatorName(),
                    'siteUrl' => $this->getController()->moduleConfig['params']['site_url'],
                    'emailStaticContentUrl' => $this->getController()->moduleConfig['params']['email_static_content_url']
                ),
                $pdf
            );
        }
        
        return true;
    }

    protected function _findTrailerFieldsStructure($fieldsStructure) {
        foreach($fieldsStructure as $fieldGroup) {
            if ($fieldGroup->groupName == self::TRAILER_CHECKLIST_NAME) {
                return $fieldGroup;
            }
        }
    }

    protected function _getTrailerFieldsData($fieldsStructure, $fieldsData) {
        $trailerFieldsData = array();
        
        $this->_collectFieldsData($fieldsStructure, $fieldsData, $trailerFieldsData);
        return $trailerFieldsData;
    }

    protected function _collectFieldsData($field, $fieldsData, &$returnData) {
        foreach ($fieldsData as $fieldData) {
            if ($fieldData->id === $field->id) {
                $returnData[] = $fieldData;
                break;
            }
        }
        if (isset($field->fields) && is_array($field->fields)) {
            foreach ($field->fields as $childField) {
                $this->_collectFieldsData($childField, $fieldsData, $returnData);
            }
        }
    }

    protected function _findFieldValue($fieldsGroup, $fieldsData, $fieldName) {
        foreach ($fieldsGroup->fields as $field) {
            if ($field->fieldName == $fieldName) {
                foreach ($fieldsData as $fieldData) {
                    if ($fieldData->id == $field->fieldId) {
                        return $fieldData->value;
                    }
                }
            }
        }
    }

    protected function copyVehicleDefFields($vehicle, $defParent = null, $parent = null)
    {
        $repField = $this->getController()->em->getRepository('SafeStartApi\Entity\DefaultField');

        $defFields = new \Doctrine\Common\Collections\ArrayCollection();
        $newFields = new \Doctrine\Common\Collections\ArrayCollection();
        if ($defParent === null) {
            $defFields = $repField->findBy(array('parent' => null));
        } else {
            $defFields = $repField->findBy(array('parent' => $defParent->getId()));
        }

        foreach ($defFields as $defField) {
            $newField = new \SafeStartApi\Entity\Field();

            $newField->setParent($parent);
            $newField->setVehicle($vehicle);
            $newField->setTitle($defField->getTitle());
            $newField->setDescription($defField->getDescription());
            $newField->setType($defField->getType());
            $newField->setAdditional($defField->getAdditional());
            $newField->setTriggerValue($defField->getTriggerValue());
            $newField->setAlertTitle($defField->getAlertTitle());
            $newField->setAlertDescription($defField->getAlertDescription());
            $newField->setAlertCritical($defField->getAlertCritical());
            $newField->setOrder($defField->getOrder());
            $newField->setEnabled($defField->getEnabled());
            $newField->setDeleted($defField->getDeleted());
            $newField->setAuthor($defField->getAuthor());

            if ($parent !== null) {
                $parent->addChildred($newField);
                $this->getController()->em->persist($parent);
            }

            $this->copyVehicleDefFields($vehicle, $defField, $newField);
            $this->getController()->em->persist($newField);
            $vehicle->addField($newField);
            $this->getController()->em->persist($vehicle);
        }
    }
}
