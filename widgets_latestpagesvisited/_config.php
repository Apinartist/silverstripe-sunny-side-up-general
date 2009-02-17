<?php

 /**
 * based on the Silverstripe Module
 * developed by www.sunnysideup.co.nz
 * author: Nicolaas - modules [at] sunnysideup.co.nz
 **/

LatestPagesVisitedWidget::setNumberOfPagesBack(7);

DataObject::add_extension('SiteTree', 'LatestPagesVisited');
DataObject::add_extension('ContentController', 'LatestPagesVisited_Controller');
//array_push(Page::$extensions, 'LatestPagesVisited');


