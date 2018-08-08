# snowtricks

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/42432bbacfee461a9fa1931869691e75)](https://www.codacy.com/project/zohac/snowtricks/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=zohac/snowtricks&amp;utm_campaign=Badge_Grade_Dashboard)

P6 - SnowTricks Community Site Development - OC - Application Developer - PHP / Symfony.

SnowTricks community site development.
Project 6 of the OpenClassrooms "Application Developer - PHP / Symfony" course.

## Requirements

* PHP: SnowTricks requires PHP version 7.0 or greater. [![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.0-8892BF.svg?style=flat-square)](https://php.net/)
* MySQL: for the database. [![Minimum MySQL Version](https://img.shields.io/badge/MySQL-%3E%3D5.7-blue.svg?style=flat-square)](https://www.mysql.com/fr/downloads/)
* Composer: to install the dependencies. [![Minimum Composer Version](https://img.shields.io/badge/Composer-%3E%3D1.6-red.svg?style=flat-square)](https://getcomposer.org/download/)

## Installation

### Git Clone

You can also download the SnowTricks source directly from the Git clone:

    git clone https://github.com/zohac/SnowTricks.git
    cd SnowTricks

Give write access to the /var directory
and the /web/upload directory

    chmod 777 -R var
    chmod 777 -R web/uploads

Then

    composer update

Configure the application by completing the file /app/config/parameters.yml

    php bin/console doctrine:schema:update --dump-sql
    php bin/console doctrine:schema:update --force

If you want to use a data set

    php bin/console doctrine:fixtures:load

## Dependency

* Doctrine: <https://www.doctrine-project.org/>
* Swiftmailer: <https://swiftmailer.symfony.com/>
* Twig: <https://twig.symfony.com/>

## Issues

Bug reports and feature requests can be submitted on the [Github Issue Tracker](https://github.com/zohac/SnowTricks/issues)

## Author

Simon JOUAN
[https://jouan.ovh](https://jouan.ovh)
