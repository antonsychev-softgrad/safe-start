#!/bin/bash

project_dir=/var/www/safe-start.dev
queues=default,new_checklist_uploaded

basedir=`dirname $0`
cd $basedir

QUEUE=$queues VVERBOSE=1 APP_INCLUDE=$project_dir/module/SafeStartApi/jobs_classes_autoloader.php php $project_dir/vendor/chrisboulton/php-resque/resque.php 
