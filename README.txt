CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Requirements
 * Installation
 * Configuration
 * Maintainers


INTRODUCTION
------------

The DCAT module provides the possibility to integrate external DCAT providers
and import the datasets those DCAT feeds offer.

 * For a full description of the module, visit the project page:
   https://www.drupal.org/project/dcat

 * To submit bug reports and feature suggestions, or to track changes:
   https://www.drupal.org/project/issues/dcat


REQUIREMENTS
------------

This module requires the following modules:

Inline entity form is used so a dataset can be created from a single form,
including the distributions, agent, vcards.

 * Inline Entity Form - https://www.drupal.org/project/inline_entity_form

For the dcat_import submodule the Migrate (part of core), Migrate plus and
Migrate tools modules are used to import the DCAT feeds.

 * Migrate Plus - https://www.drupal.org/project/migrate_plus
 * Migrate Tools - https://www.drupal.org/project/migrate_tools


INSTALLATION
------------

Install the DCAT module as you would normally install a contributed Drupal
module. Visit https://www.drupal.org/node/1897420 for further information.


CONFIGURATION
-------------

    1. Navigate to Administration > Extend and enable the module and its
       dependencies.
    2. Navigate to Administration > Structure > DCAT to administer DCAT

The main module provides the following entity types:
 * Dataset
 * Distribution
 * Agent
 * vCard

All these entities have the necessary fields to conform with DCAT-AP.


MAINTAINERS
-----------

 * Wesley De Vrient (wesleydv) - https://www.drupal.org/u/wesleydv

Supporting organization:

 * Digipolis Gent - https://www.drupal.org/digipolis-gent
