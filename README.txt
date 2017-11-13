The DCAT module provides the possibility to integrate external DCAT providers
and import the datasets those DCAT feeds offer.

The main module provides the following entity types:
- Dataset
- Distribution
- Agent
- vCard
- All these entities have the neccerary fields to conform with DCAT-AP.

Import

The import module allows to add a DCAT source, once added the feed can be
imported in the UI or via the 'dcat-import' drush command.
You can add a source here: admin/structure/dcat/settings/dcat_source

The import is based on the migrate modules in combination with the EasyRDF
library (both already part of core).

Currently the following features are under development:
- Export a combined DCAT feed
