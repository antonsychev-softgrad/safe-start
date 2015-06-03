<?php

$api = array(
    'params' => array(
        'version' => '2.0-beta',
        'site_url' => 'http://database.safestartinspections.com:81',
        'email_static_content_url' => 'https://s3-us-west-2.amazonaws.com/safe-start/emails/',
        'date_format' => 'd/m/Y',
        'time_format' => 'H:i',
        'output' => 'json',
        'href' => '/api/',
        'emailForContacts' => 'info@safestartinspections.com',
        'emailSubjects' => array(
            'vehicle_fail_notification' => 'New Critical Alert(s)',
            'vehicle_action_list' => 'New Action List',
            'new_vehicle_inspection' => 'New Inspection Report',
            'welcome' => 'Welcome To Safe Start Inspections!',
        )
    ),
    'fieldTypes' => array(
        'root' => array(
           'id' => 0,
            'default' => ''
        ),
        'radio' => array(
            'id' => 1,
            'options' => array(
                array(
                    'value' => 'yes',
                    'label' => 'Yes'
                ),
                array(
                    'value' => 'no',
                    'label' => 'No'
                ),
                array(
                    'value' => 'n/a',
                    'label' => 'N/A'
                ),
            ),
            'default' => 'n/a'
        ),
        'checkbox' => array(
            'id' => 3,
            'options' => array(
                array(
                    'value' => 'Yes',
                    'label' => 'Yes'
                ),
                array(
                    'value' => 'No',
                    'label' => 'No'
                ),
            ),
            'default' => ''
        ),
        'text' => array(
            'id' => 2,
            'default' => ''
        ),
        'photo' => array(
            'id' => 4,
            'default' => ''
        ),
        'coordinates' => array(
            'id' => 5,
            'default' => ''
        ),
        'datePicker' => array(
            'id' => 7,
            'default' => ''
        ),
        'group' => array(
            'id' => 6,
        ),
        'label' => array(
            'id' => 8,
            'default' => ''
        ),
    ),
    'mail' => array(
        'from' => 'info@safestartinspections.com',
        'transport' => array(
            'options' => array(
                'host' => 'gator3046.hostgator.com',
                'connection_class' => 'plain',
                'connection_config' => array(
                    'username' => 'admin@safestartinspections.com',
                    'password' => 'GHHxEG1Tcr+s',
                    'ssl' => 'tls'
                ),
            ),
        ),
    ),
    'defUsersPath' => '/data/users/',
    'pdf' => array(
        'inspection' => array(
            'ext' => 'pdf',
            'output_name_title' => 'inspection',
            'output_name_format' => '{name}_{user}_{vehicle}_{checkList}_at_{date}',
            'title' => 'daily inspection',
            'style' => array(
                'footer_text_size' => 10,
                'footer_text_color' => '#333333',
                'signature_height' => 60,
                'signature_width' => 120,
                'content_width' => 1,
                'content_height' => 1/3,
                'email_content_height' => 1/4,
                'content_columns' => 5,
                'content_column_padding' => 20,
                'column_padding_left' => 0,
                'page_padding_top' => 24,
                'page_padding_right' => 10,
                'page_padding_bottom' => 50,
                'page_padding_left' => 10,
                'field_size' => 10,
                'field_line_spacing' => 3,
                'field_color' => '#333333',
                'field_group_color' => '#0F5B8D',
                'field_ok_color' => '#0f5b8d',
                'field_alert_text' => 'alert',
                'field_alert_color' => '#ff0000',
                'category_field_size' => 12,
                'category_field_line_spacing' => 4,
                'category_field_color' => '#0F5B8D',
                'critical_alerts_header' => 'Critical Alerts',
                'alerts_header' => 'Non-Critical Alerts',
                'alerts_comments_header' => 'Additional comments:',
                'alert_description_size' => '10',
                'alert_description_color' => '#ff0000',
                'alert_comment_size' => '10',
                'alert_comment_color' => '#333333',
                'warning_size' => '10',
                'warning_color' => '#ff0000',
                'warning_line_spacing' => '2',
                'custom_checklist_warning' => 'Date of next inspection in %s days',
                'next_service_due' => 'Next service in %d days',
                'subscription_ending' => 'Subscription expires in %d days',
                'date_discrepancy_kms' => 'Discrepancy of current kms',
                'date_discrepancy_hours' => 'Discrepancy of current hours',
                'date_incorrect' => 'Inaccurate current hours or kms',
            )
        ),
        'inspection_fault' => array(
            'ext' => 'pdf',
            'output_name_title' => 'inspection_fault',
            'output_name_format' => '{name}_{user}_{vehicle}_{checkList}_at_{date}',
            'title' => 'fault notification',
            'style' => array(
                'footer_text_size' => 10,
                'footer_text_color' => '#333333',
                'signature_height' => 60,
                'signature_width' => 120,
                'content_width' => 1,
                'content_height' => 1/3,
                'content_columns' => 5,
                'content_column_padding' => 20,
                'column_padding_left' => 0,
                'page_padding_top' => 24,
                'page_padding_right' => 10,
                'page_padding_bottom' => 50,
                'page_padding_left' => 10,
                'field_size' => 10,
                'field_line_spacing' => 3,
                'field_color' => '#333333',
                'field_group_color' => '#0F5B8D',
                'field_ok_color' => '#0f5b8d',
                'field_alert_text' => 'alert',
                'field_alert_color' => '#ff0000',
                'category_field_size' => 12,
                'category_field_line_spacing' => 4,
                'category_field_color' => '#0F5B8D',
                'alerts_header' => 'Faults',
                'alerts_comments_header' => 'Additional Comments',
                'alert_description_size' => '10',
                'alert_description_color' => '#ff0000',
                'alert_comment_size' => '10',
                'alert_comment_color' => '#333333',
            )
        ),
        'vehicleReport' => array(
            'ext' => 'pdf',
            'output_name_title' => 'vehicle_report',
            'output_name_format' => '{name}_{user}_{vehicle}_at_{date}',
            'title' => 'vehicle report',
            'style' => array(
                'footer_text_size' => 10,
                'footer_text_color' => '#333333',
                'signature_height' => 60,
                'signature_width' => 120,
                'page_padding_top' => 24,
                'page_padding_right' => 10,
                'page_padding_bottom' => 50,
                'page_padding_left' => 10,
                'field_size' => 12,
                'field_line_spacing' => 4,
                'field_color' => '#333333',
                'field_value_color' => '#0F5B8D',
            )
        ),
        'vehicleActionList' => array(
            'ext' => 'pdf',
            'output_name_title' => 'action_list',
            'output_name_format' => '{name}_{user}_at_{date}',
            'title' => 'vehicle action list',
            'style' => array(
                'footer_text_size' => 10,
                'footer_text_color' => '#333333',
                'signature_height' => 60,
                'signature_width' => 120,
                'content_width' => 1,
                'content_height' => 1/3,
                'content_columns' => 5,
                'content_column_padding' => 20,
                'column_padding_left' => 0,
                'page_padding_top' => 24,
                'page_padding_right' => 10,
                'page_padding_bottom' => 50,
                'page_padding_left' => 10,
                'field_size' => 10,
                'field_line_spacing' => 3,
                'field_color' => '#333333',
                'field_group_color' => '#0F5B8D',
                'field_ok_color' => '#0f5b8d',
                'field_alert_text' => 'alert',
                'field_alert_color' => '#ff0000',
                'category_field_size' => 12,
                'category_field_line_spacing' => 4,
                'category_field_color' => '#0F5B8D',
                'alerts_header' => 'Faults',
                'alerts_comments_header' => 'Additional Comments',
                'alert_description_size' => '10',
                'alert_description_color' => '#ff0000',
                'alert_comment_size' => '10',
                'alert_comment_color' => '#333333',
                'warnings_header' => 'Upcoming Items',
                'warning_size' => '10',
                'warning_color' => '#ff0000',
                'warning_line_spacing' => '2',
                'next_service_due' => 'Next service in %d days',
                'custom_checklist_warning' => 'Date of next inspection in %s days',
                'subscription_ending' => 'Vehicle registration expires in %d days',
                'date_discrepancy_kms' => 'Discrepancy of current kms',
                'date_discrepancy_hours' => 'Discrepancy of current hours',
                'date_incorrect' => 'Inaccurate current hours or kms',
            )
        )

    ),
    'externalApi' => array(
        'google' => array(
            'key' => 'AIzaSyDE7B2A5PvGmkFTgdVX21Al-mabPx1uB0E'
        ),
        'apple' => array(
            'key' => __DIR__ . '/CertApsProd.pem',
            'password' => '',
        )
    ),
    'requestsLimit' => array(
        'limitForLoggedInUsers' => 50,
        'limitForUnloggedUsers' => 30,
        'limitTime' => 60,
    ),
    "3rdParty" => array(
        'connectionParams' => array(
            'dbname'   => 'paulmc_wrdp1',
            'user'     => 'paulmc_wrdpuser',
            'password' => 'K&p)X6h$OIMM',
            'host'     => '192.232.217.142',
            'port'     => '3306',
            'driver'   => 'pdo_mysql',
            'charset'  => 'UTF8',
        ),
        'dbPrefix' => 'wp_',
        'availableTypes' => array(
            'Annual'  => 1,
            'Monthly' => 2,
            'Free'    => 7,
        ),
    ),
);
