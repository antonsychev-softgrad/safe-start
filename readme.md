#1. Test server access:
##
    http://safe-start.dev2.ocsico.com/
    http://safe-start.dev2.ocsico.local/
    ftp://safe-start.dev2.ocsico.local/
    http://phpmyadmin.ocsico.com

    login: safe-start
    pass: Newpassw0rd

#2. GIT access
##
    git clone https://ocsico@bitbucket.org/ocsico/safe-start.git

    login: ocsico
    pass: Pass!@

#3. PHP vendors install:
##
   php composer.phar update

#4. MySQL\Doctrine 2:

##a) Update\Create:
        ./vendor/bin/doctrine-module orm:schema-tool:create
        ./vendor/bin/doctrine-module orm:schema-tool:update
        ./vendor/bin/doctrine-module orm:validate-schema


##b) Generate setters:
        ./vendor/bin/doctrine-module orm:generate-entities ./module/SafeStartApi/src/ --filter Company --generate-annotations=true


##c) own console command
        php ./public/index.php doctrine set-def-data

#5. Sencha
##
   a) Build JS
   cd ./module/SafeStartApp/public/
   sencha app build production

#6. Production
##a) Console:
        https://console.aws.amazon.com
        Username: paul@safestartinspections.com
        Password: ssi2705
        54.200.117.161 OR ec2-54-200-117-161.us-west-2.compute.amazonaws.com

        ssh -i safe-start-root.pem ubuntu@54.200.117.161

##b) MySql:
        user: root
        Password: SafeStart!@

        user: safe-start
        Password: J27k187lq1tJ80K

##c) Email
        user: admin@safestartinspections.com
        Password: GHHxEG1Tcr+s


#7 PHP RESQUE
##
    a) Run:
    php ./public/index.php resque start --verbose

    b) Check system process:
    ps u | grep resque.php

    c)Soft stopping workers:
    kill -QUIT YOUR-WORKER-PID


#8. nginx ZF2 conf
##
    server {
        listen 80 default_server;
        listen [::]:80 default_server ipv6only=on;

        #root /usr/share/nginx/html;
        root        /var/www/safe-start/public;
        index index.php index.html index.htm;

        # Make site accessible from http://localhost/
        server_name localhost;

        location / {
            # First attempt to serve request as file, then
            # as directory, then fall back to displaying a 404.
            try_files $uri $uri/ /index.php$is_args$args;
            # Uncomment to enable naxsi on this location
            # include /etc/nginx/naxsi.rules
        }

        location /doc/ {
            alias /usr/share/doc/;
            autoindex on;
            allow 127.0.0.1;
            allow ::1;
            deny all;
        }

        # Only for nginx-naxsi used with nginx-naxsi-ui : process denied requests
        #location /RequestDenied {
        #	proxy_pass http://127.0.0.1:8080;
        #}

        #error_page 404 /404.html;

        # redirect server error pages to the static page /50x.html
        #
        error_page 500 502 503 504 /50x.html;
        #location = /50x.html {
        #	root /usr/share/nginx/html;
        #}

        # pass the PHP scripts to FastCGI server listening on 127.0.0.1:9000

        location ~ \.php$ {
                  #try_files $uri =404;
                  fastcgi_split_path_info ^(.+\.php)(/.+)$;
                  # NOTE: You should have "cgi.fix_pathinfo = 0;" in php.ini

                  # With php5-cgi alone:
                  # fastcgi_pass 127.0.0.1:9000;
                  # With php5-fpm:
                  fastcgi_pass unix:/var/run/php5-fpm.sock;
                  fastcgi_param  SCRIPT_FILENAME $document_root$fastcgi_script_name;
                  include fastcgi_params;
            }
        # deny access to .htaccess files, if Apache's document root
        # concurs with nginx's one

        location ~ /\.ht {
            deny all;
        }
    }