<?php

Member::add_extension('Member', 'BusinessMember');

//BrowseBusinessDecorator_Controller::setDefaultFilterArray(array(1));

DataObject::add_extension('SiteTree', 'BrowseBusinessDecorator');
Object::add_extension('ContentController', 'BrowseBusinessDecorator_Controller');

Director::addRules(100, array('admin/business/$Action/$ID/$OtherID' => 'BusinessAdmin',));

BrowseBusinessDecorator::$max_radius = 250;
