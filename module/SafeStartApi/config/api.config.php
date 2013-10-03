<?php

$api = array(
    'params' => array(
        'version' => '1.0-beta',
        'date_format' => 'd/m/Y',
        'time_format' => 'H:i',
        'output' => 'json',
        'href' => '/api/',
        'emailForContacts' => 'test21141@gmail.com',
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
                    'username' => 'test21141@gmail.com',
                    'password' => 'test211411',
                    'ssl' => 'tls'
                ),
            ),
        ),
    ),
    'defUsersPath' => '/data/users/',
    'pdf' => array(
        'name' => 'checklist_review',
        'ext' => '.pdf', // automatic add to name
        'template_for_name' => array(
            'format' => "{%s}", // only
            'template' => "{name}_{user}_{vehicle}_{checkList}_at_{date}", // available: {name}, {user}, {vehicle}, {checkList}, {date}
        ),
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