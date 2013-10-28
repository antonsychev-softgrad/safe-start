#!/bin/bash

queues=default,new_checklist_uploaded

basedir=`dirname $0`
cd $basedir

project_dir=$basedir

QUEUE=$queues VVERBOSE=1 APP_INCLUDE=$project_dir/module/SafeStartApi/jobs_classes_autoloader.php php $project_dir/vendor/chrisboulton/php-resque/resque.php 
