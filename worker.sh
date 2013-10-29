#!/bin/bash

queues=default,new_checklist_uploaded,new_email_checklist_uploaded

basedir=`dirname $0`
cd $basedir

project_dir=$basedir

nohup QUEUE=$queues VVERBOSE=1 COUNT=5 APP_INCLUDE=$project_dir/module/SafeStartApi/jobs_classes_autoloader.php php $project_dir/vendor/chrisboulton/php-resque/resque.php &

exit
