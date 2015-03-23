<?php

$routes = array(
    'api' => array(
        'type' => 'Literal',
        'options' => array(
            'route' => '/api',
            'defaults' => array(
                '__NAMESPACE__' => 'SafeStartApi\Controller',
                'controller' => 'Index',
                'action' => 'index',
            ),
        ),
        'may_terminate' => true,
        'child_routes' => array(
            'default' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/[:controller[/:action]]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(),
                ),
            ),
            'get-vehicle-info' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/vehicle/:id/getinfo',
                    'constraints' => array(
                        'id' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Vehicle',
                        'action' => 'getdatabyid',
                    ),
                ),
            ),
            'get-vehicle-info-by-plant-id' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/vehicle/getinfobyplantid',
                    'defaults' => array(
                        'controller' => 'PublicVehicle',
                        'action' => 'getinfobyplantid',
                    ),
                ),
            ),
            'get-vehicle-checklist' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/vehicle/:id/getchecklist',
                    'constraints' => array(
                        'id' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Vehicle',
                        'action' => 'getchecklist',
                    ),
                ),
            ),
            'get-checklist-by-hash' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/vehicle/getchecklistbyhash',
                    'defaults' => array(
                        'controller' => 'PublicVehicle',
                        'action' => 'getchecklistbyhash',
                    ),
                ),
            ),
            'get-vehicle-alerts' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/vehicle/getalerts',
                    'defaults' => array(
                        'controller' => 'Vehicle',
                        'action' => 'getalerts',
                    ),
                ),
            ),
            'get-vehicle-checklist-data' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/vehicle/:id/getchecklistdata',
                    'constraints' => array(
                        'id' => '[A-Za-z0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Vehicle',
                        'action' => 'getChecklistData',
                    ),
                ),
            ),
            'get-vehicle-inspections' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/vehicle/:id/getinspections',
                    'constraints' => array(
                        'id' => '[A-Za-z0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'Vehicle',
                        'action' => 'getInspections'
                    ),
                ),
            ),
            'get-vehicle-statistic' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/vehicle/:id/statistic',
                    'constraints' => array(
                        'id' => '[0-9]*'
                    ),
                    'defaults' => array(
                        'controller' => 'Vehicle',
                        'action' => 'getStatistic'
                    ),
                ),
            ),
            'get-vehicle-inspections-statistic' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/vehicle/:id/inspections-statistic',
                    'constraints' => array(
                        'id' => '[0-9]*'
                    ),
                    'defaults' => array(
                        'controller' => 'Vehicle',
                        'action' => 'getInspectionBreakdownsStatistic'
                    ),
                ),
            ),
            'get-vehicle-alerts-statistic' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/vehicle/:id/alerts-statistic',
                    'constraints' => array(
                        'id' => '[0-9]*'
                    ),
                    'defaults' => array(
                        'controller' => 'Vehicle',
                        'action' => 'getAlertsStatistic'
                    ),
                ),
            ),
            'print-vehicle-statistic' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/vehicle/:id/print-statistic/:from/:to[/:name][/]',
                    'constraints' => array(
                        'id' => '[0-9]*',
                        'from' => '[0-9]*',
                        'to' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Vehicle',
                        'action' => 'printStatistic'
                    ),
                ),
            ),
            'print-vehicle-action-list' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/vehicle/:id/print-action-list[/:name][/]',
                    'constraints' => array(
                        'id' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Vehicle',
                        'action' => 'printActionList'
                    ),
                ),
            ),
            'verify-print-vehicle-action-list' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/vehicle/:id/verify-print-action-list',
                    'constraints' => array(
                        'id' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Vehicle',
                        'action' => 'verifyPrintActionList'
                    ),
                ),
            ),
            'send-vehicle-action-list' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/vehicle/:id/send-action-list',
                    'constraints' => array(
                        'id' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Vehicle',
                        'action' => 'sendActionList'
                    ),
                ),
            ),
            'complete-vehicle-checklist' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/vehicle/:id/completechecklist',
                    'constraints' => array(
                        'id' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Vehicle',
                        'action' => 'completechecklist',
                    ),
                ),
            ),
            'send-checklist-to-email' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/vehicle/checklisttoemail',
                    'defaults' => array(
                        'controller' => 'PublicVehicle',
                        'action' => 'checklistToEmail',
                    ),
                ),
            ),
            'update-user-profile' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/user/:id/profile/update',
                    'constraints' => array(
                        'id' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'UserProfile',
                        'action' => 'update',
                    ),
                ),
            ),
            'update-user-signature' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/user/profile/updatesignature',
                    'constraints' => array(
                        'id' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'UserProfile',
                        'action' => 'updatesignature',
                    ),
                ),
            ),
            'update-user' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/user/:id/update',
                    'constraints' => array(
                        'id' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'User',
                        'action' => 'update',
                    ),
                ),
            ),
            'delete-user' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/user/:id/delete',
                    'constraints' => array(
                        'id' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'User',
                        'action' => 'delete',
                    ),
                ),
            ),
            'user-sent-credentials' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/user/:id/send-credentials',
                    'constraints' => array(
                        'id' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'User',
                        'action' => 'sendCredentials',
                    ),
                ),
            ),
            'user-forgot-password' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/user/forgotpassword/:email',
                    'constraints' => array(
                        'email' => '[A-Za-z0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'User',
                        'action' => 'forgotPassword',
                    ),
                ),
            ),
            'user-reset-password' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/user/resetpassword/:token',
                    'constraints' => array(
                        'token' => '[A-Za-z0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'User',
                        'action' => 'resetPassword',
                    ),
                ),
            ),
            'update-company-subscription' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/admin/company/:id/update',
                    'constraints' => array(
                        'id' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Admin',
                        'action' => 'updateCompany',
                    ),
                ),
            ),
            'delete-company-subscription' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/admin/company/:id/delete',
                    'constraints' => array(
                        'id' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Admin',
                        'action' => 'deleteCompany',
                    ),
                ),
            ),
            'credentials-company-subscription' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/admin/company/:id/send-credentials',
                    'constraints' => array(
                        'id' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Admin',
                        'action' => 'sendCredentials',
                    ),
                ),
            ),
            'update-company-vehicle' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/vehicle/:id/update',
                    'constraints' => array(
                        'id' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Company',
                        'action' => 'updateVehicle',
                    ),
                ),
            ),
            'get-company-vehicle-users' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/vehicle/:id/users',
                    'constraints' => array(
                        'id' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Company',
                        'action' => 'getVehicleUsers',
                    ),
                ),
            ),
            'update-company-vehicle-users' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/vehicle/:id/update-users',
                    'constraints' => array(
                        'id' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Company',
                        'action' => 'updateVehicleUsers',
                    ),
                ),
            ),
            'delete-company-vehicle' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/vehicle/:id/delete',
                    'constraints' => array(
                        'id' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Company',
                        'action' => 'deleteVehicle',
                    ),
                ),
            ),
            'upload-images' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/upload-images',
                    'defaults' => array(
                        'controller' => 'ProcessData',
                        'action' => 'uploadImages',
                    ),
                ),
            ),
            'generate-pdf' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/checklist/:id/generate-pdf[/:name][/]',
                    'constraints' => array(
                        'id' => '[A-Za-z0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'ProcessData',
                        'action' => 'generatePdf',
                    ),
                ),
            ),
            'get-public-image' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/image/:hash[/:size][/]',
                    'constraints' => array(
                        'hash' => '[a-zA-Z0-9]+',
                        'size' => '[0-9]+(x|X)[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Info',
                        'action' => 'getImage',
                    ),
                ),
            ),
            'default-checklist-update' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/admin/checklist/:id/update',
                    'constraints' => array(
                        'id' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Admin',
                        'action' => 'updateDefaultChecklistFiled',
                    ),
                ),
            ),
            'default-checklist-delete' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/admin/checklist/:id/delete',
                    'constraints' => array(
                        'id' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Admin',
                        'action' => 'deleteDefaultChecklistFiled',
                    ),
                ),
            ),
            'checklist-field-update' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/checklist/:id/update',
                    'constraints' => array(
                        'id' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Company',
                        'action' => 'updateVehicleChecklistFiled',
                    ),
                ),
            ),
            'checklist-field-delete' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/checklist/:id/delete',
                    'constraints' => array(
                        'id' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Company',
                        'action' => 'deleteVehicleChecklistFiled',
                    ),
                ),
            ),
            'vehicle-field-update' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/vehiclefield/:id/update',
                    'constraints' => array(
                        'id' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Company',
                        'action' => 'updateVehicleField',
                    ),
                ),
            ),
            'vehicle-field-delete' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/vehiclefield/:id/delete',
                    'constraints' => array(
                        'id' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Company',
                        'action' => 'deleteVehicleField',
                    ),
                ),
            ),
            'update-vehicle-alert' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/vehicle/:vehicleId/alert/:alertId/update',
                    'constraints' => array(
                        'id' => '[0-9]*',
                        'alertId' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Vehicle',
                        'action' => 'updateAlert',
                    ),
                ),
            ),
            'delete-vehicle-alert' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/vehicle/alert/:alertId/delete',
                    'constraints' => array(
                        'alertId' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Vehicle',
                        'action' => 'deleteAlert',
                    ),
                ),
            ),
            'delete-vehicle-inspection' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/vehicle/inspection/delete',
                    'defaults' => array(
                        'controller' => 'Vehicle',
                        'action' => 'deleteInspection',
                    ),
                ),
            ),
            'delete-vehicle-inspection-by-id' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/vehicle/inspection/:inspectionId/delete',
                    'constraints' => array(
                        'inspectionId' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Vehicle',
                        'action' => 'deleteInspection',
                    ),
                ),
            ),
            'get-company-new-incoming' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/company/:id/get-new-incoming',
                    'constraints' => array(
                        'id' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Company',
                        'action' => 'getNewIncoming',
                    ),
                ),
            ),
            'get-inspection-alerts' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/vehicle/inspection/:inspectionId/alerts',
                    'constraints' => array(
                        'id' => '[0-9]*',
                        'alertId' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Vehicle',
                        'action' => 'getInspectionAlerts',
                    ),
                ),
            ),
            'company-update-info' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/company/:id/update',
                    'constraints' => array(
                        'id' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Company',
                        'action' => 'update'
                    ),
                ),
            ),
            'test-push-notification' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/test-push/:device/:deviceId',
                    'constraints' => array(
                        'device' => '[a-zA-Z0-9_-]*',
                        'deviceId' => '[a-zA-Z0-9_-]*'
                    ),
                    'defaults' => array(
                        'controller' => 'Index',
                        'action' => 'sendPush',
                    ),
                ),
            ),
            'sync-user' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/user/sync',
                    'defaults' => array(
                        'controller' => 'User',
                        'action' => 'sync',
                    ),
                ),
            ),
            'version' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/version',
                    'defaults' => array(
                        'controller' => 'Index',
                        'action' => 'version',
                    ),
                ),
            ),
        ),
    ),
);