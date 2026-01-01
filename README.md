# GymMel_Alumni

GymMel_Alumni is a web application that can be used for building an alumni network in which earlier students are able to register themselves and give information about their professional background. This data can be used by school administrators for carieer orientation and finding new cooperation partners.

This application is specifically adapted for the use at [Gymnasium Melle](https://www.melle-gymnasium.de) (high school in Germany) but can be forked and adapted for the use at other schools.

It includes a professional user management with twofactor authentication-functionality as well as a possibility to create database backups.

## Requirements
GymMel_Alumni needs PHP 8.1 and up. As a database, MySQL or MariaDB is required.

## Installation
### Development purposes 
This application can be installed for development purposes with the following commands. You should have [npm](https://www.npmjs.com/), [composer](https://getcomposer.org/download/) and [git](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git) installed.
```
git clone https://github.com/modelrailroader/GymMel_Alumni.git
cd GymMel_Alumni
composer install
pnpm install
pnpm build
```
The application supports PHPUnit-tests which can be triggered with running `pnpm test`. Automatical building with webpack while developing can be triggered with running `pnpm build:watch`. The debug mode can be set in the file constants.php.
### Production purposes
The last release is available for download [here](https://github.com/modelrailroader/GymMel_Alumni/releases).

### Setup
You have to configure your database server in the file constants.original.php which is placed in the root directory GymMel_Alumni. After entering the database credentials, you have to rename the file to constants.php. The configured database user needs read and write access.

Additionally you have to create an own database for this application and install the basic table infrastructure. This can be done by loading the database.sql file in the root directory of this repositority into phpMyAdmin.

## Documentation
An own documentation is not ready.

If you have any questions or need help at adapting the application for your school, don't hesitate to write me an email to model_railroader@gmx-topmail.de.

Created by Jan Harms

Copyright [Gymnasium Melle](https://www.melle-gymnasium.de) © 2026
This project is licensed under the Mozilla Public License MPL-2.0. Further information are available in the LICENSE-file.