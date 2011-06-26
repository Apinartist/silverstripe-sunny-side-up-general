<?php

/**
 * based on the Silverstripe Module
 * developed by www.sunnysideup.co.nz
 * author: Nicolaas - modules [at] sunnysideup.co.nz
 *
 **/

define('SHARETHIS_DIR', 'sharethis');

//copy the lines between the START AND END line to your /mysite/_config.php file and choose the right settings
//===================---------------- START sharethis MODULE ----------------===================
DataObject::add_extension('SiteConfig', 'SocialNetworkingConfig');
// SHARE THIS LINK -> links to your visitors page on facebook, linkedin, etc... so that they can share your website
//DataObject::add_extension('SiteTree', 'ShareThis');
//ShareThis::set_always_include (false);
//ShareThis::set_include_by_default(true);
//ShareThis::set_share_this_all_in_one(true); // all-in-one button - see http://sharethis.com/developers/api_examples/
//ShareThis::set_show_title_with_icon(false);
//ShareThis::set_alternate_icons(array("live" => "mysite/images/madgif.gif"));
//ShareThis::set_use_bw_effect(true);
//hide / add completely
//ShareThis::set_icons_to_include(array("facebook", "google", "linkedin"));   //OR
//ShareThis::set_icons_to_exclude(array("myspace"));

// SOCIAL NETWORKING LINK -> links to your page on facebook, linkedin, etc...
//DataObject::add_extension('SiteTree', 'SocialNetworkingLinks');
//SocialNetworkingLinks::set_always_include (false);
//SocialNetworkingLinks::set_include_by_default(true);
//SocialNetworkingLinks::set_show_title_with_icon(false);

//optional//requires: http://sunny.svnrepository.com/svn/sunny-side-up-general/dataobjectsorter
//Object::add_extension('ShareThisDataObject', 'DataObjectSorterDOD');
//Object::add_extension('SocialNetworkingLinksDataObject', 'DataObjectSorterDOD');
//DataObjectSorterDOD::set_do_not_add_alternative_sort_field(true);
//===================---------------- END sharethis MODULE ----------------===================
