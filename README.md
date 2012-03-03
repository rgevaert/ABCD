# ABCD Debian package

## Introduction

In this repository you can find 'a' version of ABCD.

- This version has some modifications so ABCD can run on Debian Squeeze.
- It also has been modified so a Debian package can be created of it with FPM


## Creating the Debian package

When you cloned the git repository you can

    cd installation
    ./create_deb

It will then create the Debian package in the installation directory.

## Installing ABCD Debian package

For now you will need to manually install the dependancies.  After that you can install the abcd package.
Finally can have to configure apache.   Have a look at installation/apache/ABCD_80 for an example config file.

## Links

* ABCD: http://bvsmodelo.bvsalud.org/php/level.php?lang=en&component=27&item=13
* FPM: https://github.com/jordansissel/fpm
* Primary contact: De Smet Egbert <egbert.desmet@ua.ac.be>
