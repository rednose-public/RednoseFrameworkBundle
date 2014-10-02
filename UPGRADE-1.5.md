UPGRADE FROM 1.3 or 1.4 to 1.5
==============================

Configuration
-------------

* Configuration for editor controls was removed

Features
--------

* Users and Groups are now identified by a GUID. User ids no longer collide with group ids so they can be combined in ACL configurations etc.
* Default views are upgraded to use Bootstrap 3
* Array utils added
* Added Organization model
* Added DataDictionary

Deprecations
------------

* The grid pager was removed
* All form components, models and interfaces are removed from the framework
* Extrinsic object was removed
* Experimental Node-graph functionality was removed

Bundles
-------

* FOSRestBundle was upgraded to version 1.4
* RednoseApiDocBundle was removed from the default dependencies
