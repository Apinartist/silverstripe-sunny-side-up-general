<?php
class LatestPagesVisited extends DataObjectDecorator {


}

class LatestPagesVisited_Controller extends DataObjectDecorator {

	/***
	* call this function to get the lasted pages visited
	***/

	function LatestPagesVisitedWidget() {
		$widget = new LatestPagesVisitedWidget();
		return $widget->renderWith("LatestPagesVisitedWidget");
	}


}
