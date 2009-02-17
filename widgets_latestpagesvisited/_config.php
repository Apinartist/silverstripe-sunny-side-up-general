<?php
/* config stuff goes here */

LatestPagesVisitedWidget::setNumberOfPagesBack(7);

DataObject::add_extension('SiteTree', 'LatestPagesVisited');
DataObject::add_extension('ContentController', 'LatestPagesVisited_Controller');
//array_push(Page::$extensions, 'LatestPagesVisited');


