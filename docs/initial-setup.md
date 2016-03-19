# Initial Setup

## Source code

All source code is available on the [GitHub project page](https://github.com/cultuurnet/uitpas-beheer-silex).


## Setup with Vagrant

CultuurNet Vlaanderen vzw offers a ready-to-go setup with 
[Vagrant](https://www.vagrantup.com/) which resembles the test and production
environments the most. We recommend using this Vagrant box.
Get in touch with CultuurNet Vlaanderen vzw to get access to the Vagrant box.

## Setup on your own hosting stack

You will need a web server (for example Apache or Nginx) with at least PHP 5.5.

Steps:

* Git clone the source code.
* Install the dependencies with ``composer install``.
* Set the web directory as the document root in your web server 
  configuration.
* Configure your web server to rewrite all requests that do not match with an existing file,
  to index.php. If you are using Apache, the .htaccess file already takes care 
  of this.
* Copy config.dist.yml to config.yml and adapt to your needs.
