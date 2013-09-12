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
                    'route' => '/vehicle/:id/getinfobyplantid',
                    'constraints' => array(
                        'id' => '[0-9]*',
                    ),
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
            'get-vehicle-alerts' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/vehicle/getalerts',
                    'constraints' => array(
                        'id' => '[0-9]*',
                    ),
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
                    'constraints' => array(
                        'id' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => 'PublicVehicle',
                        'action' => 'checklisttoemail',
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
                    'route' => '/checklist/:id/generate-pdf',
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
                        'size' => '[0-9]+(x|X)[0-9]+', //todo: regexp for 200x200
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
        ),
    ),
);