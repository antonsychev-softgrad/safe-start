#!/bin/bash

queues=default,new_checklist_uploaded,new_email_checklist_uploaded

basedir=`dirname $0`
cd $basedir

project_dir=$basedir
DATE=`date +%Y-%m-%d`-worker-uotput.log

nohup sudo -u www-data QUEUE=$queues \
        COUNT=5 \
        VVERBOSE=1 \
        APP_INCLUDE=$project_dir/module/SafeStartApi/jobs_classes_autoloader.php \
        php $project_dir/vendor/chrisboulton/php-resque/resque.php \
        >> $project_dir/data/logs/resque/$DATE 2>&1 &

