<?php
/**
 * developed by www.sunnysideup.co.nz
 * authors:
 * Nicolaas modules [at] sunnysideup.co.nz
 **/

define('SS_METATAGS_DIR', 'metatags');

//copy the lines between the START AND END line to your /mysite/_config.php file and choose the right settings
//===================---------------- START metatags MODULE ----------------===================
// dont forget to add $this->addBasicMetatagRequirements() to Page_Controller->init();
// and add this to your Page.ss template file: $ExtendedMetatags
//MUST SET ...
//Object::add_extension('SiteConfig', 'MetaTagsSiteConfigDE');
//Object::add_extension('SiteTree', 'MetaTagsSTE');
//Object::add_extension('ContentController', 'MetaTagsContentControllerEXT');
//MAY SET ...
/* pop-ups and form interaction */
//MetaTagsSTE::$disable_update_popup = false;
/* meta descriptions */
//MetaTagsSTE::$meta_desc_length = 24;
/* meta keywords */
//MetaTagsSTE::$hide_keywords_altogether = true;
//FONTS - see google fonts for options, include within CSS file as: body {font-family: Inconsolata;}
//MetaTagsSTE::add_google_font('Inconsolata');
/* combined files */
//MetaTagsContentControllerEXT::set_folder_for_combined_files('cache');
//MetaTagsContentControllerEXT::set_combine_css_files_into_one(true);
//MetaTagsContentControllerEXT::set_combine_js_files_into_one(true);
/* favicons */
//MetaTagsSTE::$use_themed_favicon = true;
//===================---------------- END metatags MODULE ----------------===================

