# CircleFinder

This small application helps to build co-creation circles.

[@circlefinder](https://twitter.com/circlefinder)

## Requirements

* PHP 7
* MySQL

## Installation

1) Make sure you have PHP 7 and Composer installed on the host machine
2) Clone this repository and "cd circlefinder"
3) Run "composer install --no-dev" for production or remove "--no-dev" for development
4) Run "php artisan migrate"
5) Run "php artisan setup"
6) Run "php artisan admin:create 'Name' 'e-mail' 'passwort'" to create the admin user
7) The the host root to 'circlefinder/public'

## Build status

[![Build Status](https://travis-ci.org/leonidlezner/circlefinder.svg?branch=master)](https://travis-ci.org/leonidlezner/circlefinder)

## OSS Scan

[![FOSSA Status](https://app.fossa.io/api/projects/git%2Bgithub.com%2Fleonidlezner%2Fcirclefinder.svg?type=large)](https://app.fossa.io/projects/git%2Bgithub.com%2Fleonidlezner%2Fcirclefinder?ref=badge_large)
