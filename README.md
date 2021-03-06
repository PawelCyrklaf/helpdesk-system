
  
# HelpDesk System API  
The Help Desk system is a system created in the Symfony 4 RESTful API, whose task is to support the operation of support. The system is to help the customer report a problem, e.g. problems with the order or the operation of services.  
  
![swagger images](https://www.cyrklaf.eu/swagger.png)  
  
## Backend technology  
1. Symfony 4.4  
2. PHP 7.4 on Nginx server  
3. MariaDB 10.4  
4. Doctrine ORM  
5. RabbitMQ  
6. Elasticsearch  
  
## Composer packages used  
1. FOSRestBundle - REST API  
2. FOSElasticaBundle - Elasticsearch  
3. Lexik JWT Authentication Bundle - JWT Authentication  
4. JWT Refresh Token Bundle  
5. Nelmio Cors Bundle - CORS  
6. Symfony/test-pack - PHP Unit  
7. NelmioApiDocBundle - Swagger   
8. php-amqplib/php-amqplib - RabbitMQ  
9. KnpLabs/KnpPaginatorBundle  
  
## Environment  
1. Linux Debian 9 on local CentOS 7 on online server  
2. Apache with PHP 7.4  
3. PHPStorm  
4. MariaDB + MySQL Workbench  
  
## Installation  
 $ git clone https://github.com/PawelCyrklaf/helpdesk-system.git 
 $ cd helpdesk-system  
After downloading the repository, create an .env file in the application's root directory and add the following code and add your data such as data to the database, data to mail and the elasticsearch server.:  
  
 

        ###> doctrine/doctrine-bundle ### 
        DATABASE_URL=mysql://DB_USER:DB_PASS@mysql:3306/DB_NAME?serverVersion=5.7 
        ###< doctrine/doctrine-bundle ###     
        
        ###> nelmio/cors-bundle ###  
        CORS_ALLOW_ORIGIN=^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$ 
        ###< nelmio/cors-bundle ###     
        
        ###> symfony/google-mailer ###  
        MAILER_DSN=gmail://GMAIL_LOGIN:GMAIL_PASS@default 
        ###< symfony/google-mailer ###     
        
        ###> friendsofsymfony/elastica-bundle ###  
        ELASTICSEARCH_URL=http://localhost:9200/ 
        ###< friendsofsymfony/elastica-bundle ###  
        
        ### <variables for docker compose> ###
        MYSQL_ROOT_PASSWORD=root_password  
	    MYSQL_DATABASE=database_name  
	    NGINX_PORT=80  
        LOCAL_USER=1000:1000

  Add permissions to execute script:

     $ sudo chmod +x /docker/configure.sh

 then execute bash script:
 

    $ sudo /docker/configure.sh
configure.sh code:

    #!/usr/bin/env bash  
    docker-compose exec php php bin/console doctrine:schema:update --force  
    docker-compose exec php php bin/console doctrine:fixtures:load --no-interaction  
    docker-compose exec php php bin/console fos:elastica:populate

  last step of installation is run docker-compose:

      $ docker-compose up -d

Default admin credential is:    
 username: admin@example.com password: admin123   

Default user credential is:    
 username: user@example.com    password: user123  
 ## Planned features
1. Adding support packages and integration with payment systems, eg PayPal
2. SMS notification
3. General chat for employees
4. Adding attachments to tickets

  ## API documentation  
The API includes documented all available routings. After starting, just go to the address e.g. http://localhost:8000/api/doc where a detailed description of each API routing will be available.  
  
## Contact  
If you have any questions, please send me email for pawel.cyrklaf@gmail.com
