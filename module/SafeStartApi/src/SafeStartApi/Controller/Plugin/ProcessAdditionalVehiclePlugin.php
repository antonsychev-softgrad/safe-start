<?php
namespace SafeStartApi\Controller\Plugin;

use SafeStartApi\Entity\CheckList;
use SafeStartApi\Entity\Field;
use SafeStartApi\Entity\Vehicle;
use SafeStartApi\Entity\VehicleField;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class ProcessAdditionalVehiclePlugin extends AbstractPlugin
{
    const TRAILER_CHECKLIST_NAME = 'Trailer';
//    const TRAILER_PLANT_ID_FIELD_NAME = 'Plant ID';
    const TRAILER_TYPE_FIELD_NAME = 'Type of trailer';
    const TRAILER_MAKE_FIELD_NAME = 'Trailer Make';
    const TRAILER_MODEL_FIELD_NAME = 'Trailer Model';
    const TRAILER = 'TRAILER';
    const AUXILIARY_MOTOR = 'AUXILIARY MOTOR';
    const AUXILIARY_MOTOR_CHECKLIST_NAME = 'Auxiliary motor';
    const VEHICLE_PLANT_ID_FIELD_NAME = 'Plant ID';
    const VEHICLE_REGISTRATION_EXPIRY = 'Registration expiry';

    const DEFAULT_VEHICLE_FIELDS = 'Default fields';
    const DEFAULT_VEHICLE_FIELD_MODEL = 'Model';
    const DEFAULT_VEHICLE_FIELD_MAKE = 'Make';
    const DEFAULT_VEHICLE_FIELD_PROJECT_NAME = 'Project Name';
    const DEFAULT_VEHICLE_FIELD_PROJECT_NUMBER = 'Project Number';

    public static $defaultFields = array(
        array(
            'type'  => 'text',
            'title' => self::DEFAULT_VEHICLE_FIELD_MODEL
        ),
        array(
            'type'  => 'text',
            'title' => self::DEFAULT_VEHICLE_FIELD_MAKE
        ),
        array(
            'type'  => 'text',
            'title' => self::DEFAULT_VEHICLE_FIELD_PROJECT_NAME
        ),
        array(
            'type'  => 'text',
            'title' => self::DEFAULT_VEHICLE_FIELD_PROJECT_NUMBER
        )
    );

    private $ignoreChecklistFields = array('Plant ID','Type of trailer','Trailer Make','Registration expiry','Hours');

    public function getVehiclePlantIdFromChecklist(CheckList $checkList, $vehicleType) {
         $vehicle = $checkList->getVehicle();
        $fieldsStructure = json_decode($checkList->getFieldsStructure());
        $fieldsData = json_decode($checkList->getFieldsData());

//        if ((! $trailerStructure = $this->_findTrailerFieldsStructure($fieldsStructure)) || (! $auxStructure = $this->_findAuxFieldsStructure($fieldsStructure))) {
//            return null;
//        }
        if (! $vehicleStructure = $this->_findVehicleFieldsStructure($fieldsStructure, $vehicleType)) {
            return null;
        }
//        if($vehicleType == self::TRAILER){
//            $trailerFieldsData = $this->_getTrailerFieldsData($trailerStructure, $fieldsData);
//            if (! $plantId = strtoupper($this->_findFieldValue($trailerStructure, $fieldsData, self::TRAILER_PLANT_ID_FIELD_NAME))) {
//                return null;
//            }
//        } elseif($vehicleType == self::AUXILIARY_MOTOR){
//            $auxFieldsData = $this->_getAuxFieldsData($auxStructure, $fieldsData);
//            if (! $plantId = strtoupper($this->_findFieldValue($auxStructure, $fieldsData, self::AUXILIARY_MOTOR_PLANT_ID_FIELD_NAME))) {
//                return null;
//            }
//        }

            $vehicleFieldsData = $this->_getVehicleFieldsData($vehicleStructure, $fieldsData);
            if (! $plantId = strtoupper($this->_findFieldValue($vehicleStructure, $fieldsData, self::VEHICLE_PLANT_ID_FIELD_NAME))) {
                return null;
            }

        return $plantId;
    }

    public function processVehicle($trailer, CheckList $checkList, $alerts, $vehicleType) {
        $vehicle = $checkList->getVehicle();
        $user = $checkList->getUser();
        $fieldsStructure = json_decode($checkList->getFieldsStructure());
        $fieldsData = json_decode($checkList->getFieldsData());

        if (! $plantId = $this->getVehiclePlantIdFromChecklist($checkList, $vehicleType)) {
            return;
        }

//        if (! $trailerStructure = $this->_findTrailerFieldsStructure($fieldsStructure)) {
//            return;
//        }
        if (! $vehicleStructure = $this->_findVehicleFieldsStructure($fieldsStructure, $vehicleType)) {
            return;
        }
//        $trailerFieldsData = $this->_getTrailerFieldsData($trailerStructure, $fieldsData);
//        if (! $plantId = strtoupper($this->_findFieldValue($trailerStructure, $fieldsData, self::TRAILER_PLANT_ID_FIELD_NAME))) {
//            return;
//        }
        $vehicleFieldsData = $this->_getVehicleFieldsData($vehicleStructure, $fieldsData);
        if (! $plantId = strtoupper($this->_findFieldValue($vehicleStructure, $fieldsData, self::VEHICLE_PLANT_ID_FIELD_NAME))) {
            return;
        }
        if($vehicleType == self::TRAILER){
            $model = $this->_findFieldValue($vehicleStructure, $fieldsData, self::TRAILER_MODEL_FIELD_NAME);
            if (!$model) {
                $model = $this->_findFieldValue($vehicleStructure, $fieldsData, self::TRAILER_TYPE_FIELD_NAME);
            }
            $make = $this->_findFieldValue($vehicleStructure, $fieldsData, self::TRAILER_MAKE_FIELD_NAME);
        }

        $registrationExpiry = $this->_findFieldValue($vehicleStructure, $fieldsData, self::VEHICLE_REGISTRATION_EXPIRY);
        //$type = $this->_findFieldValue($trailerStructure, $fieldsData, self::TRAILER_TYPE_FIELD_NAME);
//        $model = $this->_findFieldValue($trailerStructure, $fieldsData, self::TRAILER_MODEL_FIELD_NAME);
//        if (!$model) {
//            $model = $this->_findFieldValue($trailerStructure, $fieldsData, self::TRAILER_TYPE_FIELD_NAME);
//        }
//        $make = $this->_findFieldValue($trailerStructure, $fieldsData, self::TRAILER_MAKE_FIELD_NAME);
        //$registration = $this->_findFieldValue($trailerStructure, $fieldsData, self::TRAILER_TYPE_FIELD_NAME);
        
        $repository = $this->getController()->em->getRepository('SafeStartApi\Entity\Vehicle');

        $company = $vehicle->getCompany();

        if (! $trailer) {
            $trailer = new Vehicle();

            $trailer->setCompany($vehicle->getCompany());
            $trailer->setPlantId($plantId);
            if(isset($make)){
                $trailer->setType($make);
            }
            if(isset($model)){
                $trailer->setTitle($model);
            }
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
            if($registrationExpiry){
                $expiryDate = new \DateTime();
                $expiryDate->setTimestamp((int)$registrationExpiry);
                $trailer->setExpiryDate($expiryDate);
            }

            foreach ($vehicle->getUsers() as $user) {
                $trailer->addUser($user);
            }
            foreach ($vehicle->getResponsibleUsers() as $user) {
                $trailer->addResponsibleUser($user);
            }
            $this->getController()->em->persist($trailer);


            $repField = $this->getController()->em->getRepository('SafeStartApi\Entity\DefaultField');
            if($vehicleType == self::TRAILER){
                $defFields = $repField->findBy(array('parent' => null, 'title' => self::TRAILER_CHECKLIST_NAME));
            } elseif($vehicleType == self::AUXILIARY_MOTOR){
                $defFields = $repField->findBy(array('parent' => null, 'title' => self::AUXILIARY_MOTOR_CHECKLIST_NAME));
            }
//        $defFields = $repField->findBy(array('parent' => null, 'title' => self::TRAILER_CHECKLIST_NAME));

            foreach ($defFields as $defField) {
                $newField = new Field();
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

            $this->getController()->em->flush();
        }

        $newCheckList = new CheckList();
        $uniqId = uniqid();
        $newCheckList->setHash($uniqId);
        $newCheckList->setVehicle($trailer);
        $newCheckList->setUser($user);
        $newCheckList->setGpsCoords($checkList->getGpsCoords());


//        $newCheckList->setFieldsStructure(json_encode(array($trailerStructure)));
        $newCheckListStructure = $this->_findVehicleFieldsStructure($fieldsStructure, $vehicleType, true);
        $newCheckListData = $this->_getVehicleFieldsData($newCheckListStructure, $fieldsData);

        $newTrailerFields = $this->getController()->em->getRepository('SafeStartApi\Entity\Field')->findBy(array('vehicle' => $trailer));
        $newCheckListFields = $this->_getVehicleFields($newCheckListStructure, $newCheckListData, $newTrailerFields);

        $newCheckList->setFieldsStructure(json_encode(array($newCheckListFields['fieldStructure'])));
        $newCheckList->setFieldsData(json_encode($newCheckListFields['fieldData']));

//        $newCheckList->setFieldsStructure(json_encode(array($newCheckListStructure)));
//        $newCheckList->setFieldsData(json_encode($trailerFieldsData));
//        $newCheckList->setFieldsData(json_encode($newCheckListData));

        $this->getController()->em->persist($newCheckList);

        $trailerIds = array();

//        foreach ($trailerStructure->fields as $field) {
//            $trailerIds[] = $field->fieldId;
//        } $vehicleStructure

        foreach ($vehicleStructure->fields as $field) {
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

        $this->checkDefaultCustomFields($trailer);
    }

    public function processHiddenVehicle(CheckList $checkList, $alerts, $vehicleType) {
        $vehicle = $checkList->getVehicle();
        $user = $checkList->getUser();
        $fieldsStructure = json_decode($checkList->getFieldsStructure());
        $fieldsData = json_decode($checkList->getFieldsData());

//        if (! $trailerStructure = $this->_findTrailerFieldsStructure($fieldsStructure)) {
//            return;
//        }
        if (! $vehicleStructure = $this->_findVehicleFieldsStructure($fieldsStructure, $vehicleType)) {
            return null;
        }

//        $trailerFieldsData = $this->_getTrailerFieldsData($trailerStructure, $fieldsData);
//        if (! $plantId = strtoupper($this->_findFieldValue($trailerStructure, $fieldsData, self::TRAILER_PLANT_ID_FIELD_NAME))) {
//            return;
//        }
        $vehicleFieldsData = $this->_getVehicleFieldsData($vehicleStructure, $fieldsData);
        if (! $plantId = strtoupper($this->_findFieldValue($vehicleStructure, $fieldsData, self::VEHICLE_PLANT_ID_FIELD_NAME))) {
            return null;
        }

        //$type = $this->_findFieldValue($trailerStructure, $fieldsData, self::TRAILER_TYPE_FIELD_NAME);
//        $model = $this->_findFieldValue($trailerStructure, $fieldsData, self::TRAILER_MODEL_FIELD_NAME);
//        if (!$model) {
//            $model = $this->_findFieldValue($trailerStructure, $fieldsData, self::TRAILER_TYPE_FIELD_NAME);
//        }
//        $make = $this->_findFieldValue($trailerStructure, $fieldsData, self::TRAILER_MAKE_FIELD_NAME);
        $model = $this->_findFieldValue($vehicleStructure, $fieldsData, self::TRAILER_MODEL_FIELD_NAME);
        if (!$model) {
            $model = $this->_findFieldValue($vehicleStructure, $fieldsData, self::TRAILER_TYPE_FIELD_NAME);
        }
        $make = $this->_findFieldValue($vehicleStructure, $fieldsData, self::TRAILER_MAKE_FIELD_NAME);

        $company = $vehicle->getCompany();

        $newCheckList = new CheckList();
        $uniqId = uniqid();
        $newCheckList->setHash($uniqId);
        $newCheckList->setVehicle($vehicle);
        $newCheckList->setUser($user);
//        $newCheckList->setFieldsStructure(json_encode(array($trailerStructure)));
//        $newCheckList->setFieldsData(json_encode($trailerFieldsData));
        $newCheckList->setFieldsStructure(json_encode(array($vehicleFieldsData)));
        $newCheckList->setFieldsData(json_encode($vehicleFieldsData));
        $this->getController()->em->persist($newCheckList);

        $repField = $this->getController()->em->getRepository('SafeStartApi\Entity\DefaultField');
        $defFields = $repField->findBy(array('parent' => null, 'title' => self::TRAILER_CHECKLIST_NAME));

        $trailerIds = array();

//        foreach ($trailerStructure->fields as $field) {
//            $trailerIds[] = $field->fieldId;
//        }
        foreach ($vehicleStructure->fields as $field) {
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
        if (file_exists($pdf) && $admin) {
            $this->getController()->MailPlugin()->send(
                $this->getController()->moduleConfig['params']['emailSubjects']['new_vehicle_inspection'],
                $admin->getEmail(),
                'checklist.phtml',
                array(
                    'name' => $admin->getFirstName() . ' ' . $admin->getLastName(),
                    'plantId' => $newCheckList->getVehicle() ? $newCheckList->getVehicle()->getPlantId() : '-',
                    'uploadedByName' => $newCheckList->getOperatorName(),
                    'siteUrl' => $this->getController()->moduleConfig['params']['site_url'],
                    'emailStaticContentUrl' => $this->getController()->moduleConfig['params']['email_static_content_url'],
                    'showLoginUrl' => true,
                ),
                $pdf
            );
        }
        
        return true;
    }
    //deprecated
    protected function _findTrailerFieldsStructure($fieldsStructure) {
        foreach($fieldsStructure as $fieldGroup) {
            if ($fieldGroup->groupName == self::TRAILER_CHECKLIST_NAME) {
                return $fieldGroup;
            }
        }
    }

    protected function _findAuxFieldsStructure($fieldsStructure) {
        foreach($fieldsStructure as $fieldGroup) {
            if ($fieldGroup->groupName == self::AUXILIARY_MOTOR_CHECKLIST_NAME) {
                return $fieldGroup;
            }
        }
    }
    //end of deprecated
    protected function _findVehicleFieldsStructure($fieldsStructure, $vehicleType, $forChecklist=false){
        if($vehicleType == self::TRAILER){
            $groupName = self::TRAILER_CHECKLIST_NAME;
        } elseif($vehicleType == self::AUXILIARY_MOTOR){
            $groupName = self::AUXILIARY_MOTOR_CHECKLIST_NAME;
        }
        foreach($fieldsStructure as $fieldGroup) {
            if ($fieldGroup->groupName == $groupName) {
                if($forChecklist){
                    $fields = $fieldGroup->fields;
                    foreach($fields as $key=>$field){
                        if(in_array($field->fieldName,$this->ignoreChecklistFields)){
                            unset($fields[$key]);
                        }
                    }
                    $fieldGroup->fields = array_values($fields);
                }
                return $fieldGroup;
            }
        }
    }
    //deprecated
    protected function _getTrailerFieldsData($fieldsStructure, $fieldsData) {
        $trailerFieldsData = array();
        
        $this->_collectFieldsData($fieldsStructure, $fieldsData, $trailerFieldsData);
        return $trailerFieldsData;
    }

    protected function _getAuxFieldsData($fieldsStructure, $fieldsData) {
        $auxFieldsData = array();

        $this->_collectFieldsData($fieldsStructure, $fieldsData, $auxFieldsData);
        return $auxFieldsData;
    }
    //end of deprecated
    protected function _getVehicleFieldsData($fieldsStructure, $fieldsData) {
        $vehicleFieldsData = array();

        $this->_collectFieldsData($fieldsStructure, $fieldsData, $vehicleFieldsData);
        return $vehicleFieldsData;
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

    protected function _getVehicleFields($fieldStructure, $fieldData, $newVehicleFields) {
        $vehicleFields = array();
        $fields = $fieldStructure->fields;
        foreach ($fields as $field) {
            $vehicleFields[$field->fieldName]['oldId'] = $field->id;
        }

        foreach ($newVehicleFields as $field) {
            if (isset($vehicleFields[$field->getTitle()])) {
                $vehicleFields[$field->getTitle()]['newId'] = $field->getId();
            }
        }

        foreach ($fields as $field) {
            $field->id = isset($vehicleFields[$field->fieldName]['newId']) ? $vehicleFields[$field->fieldName]['newId'] : $field->id;
            $field->fieldId = isset($vehicleFields[$field->fieldName]['newId']) ? $vehicleFields[$field->fieldName]['newId'] : $field->id;
        }
        $fieldStructure->fields = $fields;

        $oldFieldsIds = array_map(function($field) {
            return $field['oldId'];
        }, $vehicleFields);

        foreach ($fieldData as $field) {
            if (in_array($field->id, $oldFieldsIds)) {
                $field->id = $vehicleFields[array_search($field->id, $oldFieldsIds)]['newId'];
            }
        }

        $result = array();
        $result['fieldStructure'] = $fieldStructure;
        $result['fieldData'] = $fieldData;

        return $result;
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
            if(in_array($defField->title,$this->ignoreChecklistFields)){
                continue;
            }
            $newField = new Field();

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


    public function checkDefaultCustomFields($vehicle, $fields = array())
    {
        $defaultsFieldsTrigger = false;
        if ($vehicle) {
            $vehicleField = $this->getController()->em->getRepository('SafeStartApi\Entity\VehicleField')->findBy(array(
                'vehicle' => $vehicle,
                'title'   => self::DEFAULT_VEHICLE_FIELDS
            ));
            if ($vehicleField) {
                $defaultsFieldsTrigger = true;
            }
            if (!$defaultsFieldsTrigger) {
                $parentField = new VehicleField();
                $parentField->setTitle(self::DEFAULT_VEHICLE_FIELDS);
                $parentField->setType('root');
                $parentField->setVehicle($vehicle);
                $this->getController()->em->persist($parentField);

                if ($parentField) {

                    $createField = function($defaultField = array(), $value = null) use ($parentField, $vehicle) {
                        $field = new VehicleField();
                        $field->setParent($parentField);

                        $field->setTitle($defaultField['title']);
                        $field->setType($defaultField['type']);
                        $field->setOrder(0);
                        $field->setAdditional((isset($this->data->type) && $this->data->type == 'root') ? (int)$this->data->additional : 0);
                        $field->setAlertTitle(isset($this->data->alert_title) ? $this->data->alert_title : '');
                        $field->setAlertDescription(isset($this->data->alert_description) ? $this->data->alert_description : '');
                        $field->setEnabled(1);
                        $field->setAlertCritical(isset($this->data->alert_critical) ? (int)$this->data->alert_critical : 0);
                        $field->setVehicle($vehicle);
                        $field->setDefaultValue($value);

                        $this->getController()->em->persist($field);
                        $user = $this->getController()->em->getRepository('SafeStartApi\Entity\User')->findById(1);
                        $field->setAuthor($user[0]);
                        $parentField->setAuthor($user[0]);
                    };

                    if(!empty($fields) && sizeof(self::$defaultFields) == sizeof($fields)) {
                        foreach (array_values($fields) as $key => $value) {
                            $defaultField = self::$defaultFields[$key];
                            $createField($defaultField, $value);
                        }
                    } else {
                        foreach (self::$defaultFields as $defaultField) {
                            $createField($defaultField);
                        }
                    }
                }
                $this->getController()->em->flush();
            }
        }

    }
}
