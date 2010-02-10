<?php
/**
 * An extension to {@link SSReport} that allows a user
 * to view all Order instances in the system that
 * are not printed. {@link UnprintedOrderReport->getReportField()}
 * outlines the logic for what orders are considered to be "unprinted".
 * 
 * @package ecommerce
 */
class DraftOrderReport extends SSReport {

	protected $title = 'Draft Orders';

	protected $description = 'This shows draft orders created by standing orders to be reviewed and published.';

	/**
	 * Return a {@link ComplexTableField} that shows
	 * all Order instances that are not printed. That is,
	 * Order instances with the property "Printed" value
	 * set to "0".
	 * 
	 * @return ComplexTableField
	 */
	function getReportField() {
		// Get the fields used for the table columns
		$fields = DraftOrder::$table_overview_fields;

		$table = new ComplexTableField(
			$this,
			'DraftOrders',
			'DraftOrder',
			$fields
		);
		
		$table->itemClass = 'DraftOrderReport_Item';
		
		$table->setFieldCasting(array(
			'Created' => 'Date',
			'Total' => 'Currency->Nice'
		));
		
		$query = singleton('DraftOrder')->buildSQL(null, 'Order.Created DESC');
		$query->groupby[] = 'Order.Created';
		$table->setCustomQuery($query);
		
		$table->setPermissions(array(
			'show',
			'export',
			'delete',
		));
		
		return $table;
	}

}

class DraftOrderReport_Item extends TableListField_Item {

	public function ShowLink() {
		return 'DraftOrderReport_Controller/index/'.$this->item->ID;
	}
	
}

class DraftOrderReport_Controller extends Controller {
	
	public function init() {
		parent::init();
		
		if(!Permission::check('ADMIN') || !Permission::check('CMS_ACCESS_ReportAdmin')) {
			return Security::permissionFailure($this, _t('OrderReport.PERMISSIONFAILURE', 'Sorry you do not have permission to view this report. Please login as an Adminstrator'));
		}
	}

	public function index() {
		return $this->renderWith('OrderInformation_Draft');
	}
	
	public function publish() {
		$draftOrder = DataObject::get_by_id('DraftOrder', $this->urlParams['ID']);
		
		if($draftOrder) { 
			$published = $this->renderWith('OrderInformation_Draft', array('Published' => true));
			$draftOrder->publishOrder();
			return $published;
		}
	}

	public function PublishLink($action = null) {
		return 'DraftOrderReport_Controller/publish/'.$this->urlParams['ID'];
	}

	public function Order() {
		$id = $this->urlParams['ID'];

		if(is_numeric($id)) {
			$order = DataObject::get_by_id("Order", $id);
			if(isset($_REQUEST['print'])) {
				$order->updatePrinted(true);
			}
			
			return $order;
		}

		return false;
	}
	
	public function StandingOrder() {
		$order = $this->Order();

		if($order) {
			return $order->StandingOrder();
		}

		return false;
	}

}