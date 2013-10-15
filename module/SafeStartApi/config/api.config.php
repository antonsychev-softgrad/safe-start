<?php

$api = array(
    'params' => array(
        'version' => '1.0-beta',
        'date_format' => 'm/d/Y',
        'time_format' => 'H:i',
        'output' => 'json',
        'href' => '/api/',
        'emailForContacts' => 'ponomarenko.t@gmail.com',
    ),
    'fieldTypes' => array(
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
    ),
    'mail' => array(
        'from' => 'admin@safe-start.dev',
        'transport' => array(
            'options' => array(
                'host' => 'smtp.gmail.com',
                'connection_class' => 'plain',
                'connection_config' => array(
                    'username' => 'webdev.group.ua@gmail.com',
                    'password' => 'Ocsico!@',
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
            'title' => 'safe start inspection',
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
        'inspection_fault' => array(
            'ext' => 'pdf',
            'output_name_title' => 'inspection_fault',
            'output_name_format' => '{name}_{user}_{vehicle}_{checkList}_at_{date}',
            'title' => 'safe start inspection',
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
        )

    ),
    'externalApi' => array(
        'google' => array(
            'key' => 'AIzaSyDE7B2A5PvGmkFTgdVX21Al-mabPx1uB0E'
        ),
        'apple' => array(
            'key' => __DIR__ . '/apple_push_key.pem',
            'password' => '',
        )
    ),
    'requestsLimit' => array(
        'limitForLoggedInUsers' => 50,
        'limitForUnloggedUsers' => 5,
        'limitTime' => 60,
    )
);