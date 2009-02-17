<?php

/**
* This module allows you to create social networking icons on your site
* to include:
* a. tweak settings below
* b. add <% include ShareThis %> to your template
* c. run db/build/?flush
*/

DataObject::add_extension('SiteTree', 'ShareThis');
ShareThis::$EnabledIcons = Array("email", "print", "digg", "reddit", "delicious", "furl", "ma.gnolia", "newsvine", "live", "myweb", "google", "stumbleupon", "simpy", "facebook", "favourites");
ShareThis::$ShowTitle = false;
ShareThis::$IconTransparent = true;
ShareThis::set_include_by_default(true);
ShareThis::set_always_include (true);
