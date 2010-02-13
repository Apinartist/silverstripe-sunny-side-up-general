<?php

/**
 *@author nicolaas[at]sunnysideup.co.nz
 *@description: go
 *
 *
 **/

class OrderReportWithLog_Popup extends OrderReport_Popup {


	function init(){
		parent::init();
		Requirements::block("ecommerce/css/OrderReport.css");
		Requirements::themedCSS("OrderReportWithLog");
	}
	/**
	 * This is the default action of this
	 * controller without calling any
	 * explicit action, such as "show".
	 *
	 * This default "action" will show
	 * order information in a printable view.
	 */
	function index() {
		return $this->renderWith('OrderInformationWithLog_Print');
	}

	/**
	 * This action shows an invoice template
	 * for the current order we're looking at.
	 */
	function invoice() {
		return $this->renderWith('OrderInformationWithLog_Print');
	}

	function Link($action = null) {
		return "OrderReportWithLog_Popup/$action";
	}


	/**
	 * Return a {@link Form} allowing a user to change the status
	 * of an order using {@link OrderStatusLog} records.
	 *
	 * @TODO Tidy up JS, and switch it over to jQuery instead of prototype.
	 *
	 * @return Form
	 */
	function StatusForm() {
		Requirements::css('cms/css/layout.css');
		Requirements::css('cms/css/cms_right.css');
		Requirements::css('ecommerce/css/OrderReport.css');

		Requirements::javascript('jsparty/loader.js');
		Requirements::javascript('jsparty/behaviour.js');
		Requirements::javascript('jsparty/prototype.js');
		Requirements::javascript('jsparty/prototype_improvements.js');

		$OrderID = (isset($_REQUEST['OrderID'])) ? $_REQUEST['OrderID'] : $this->urlParams['ID'];

		if(is_numeric($OrderID)) {
			$order = DataObject::get_by_id('Order', $OrderID);
			if($order) {
				$member = $order->Member();
				$fields = new FieldSet(
					new HeaderField(_t('OrderReport.CHANGESTATUS', 'Change Order Status'), 3),
					$order->obj('Status')->formField('Status', null, null, $order->Status),
					new TextField('DispatchedBy', "Dispatched By"),
					new CalendarDateField('DispatchedOn', "Dispatched On"),
					new TextField('DispatchTicket', "Dispatch Ticket Code"),
					new TextField('PaymentCode', "Payment Code / Reference"),
					new CheckboxField('PaymentOK', "Payment has cleared"),
					new TextareaField('Note', "Note", $rows = 4, $cols = 30),
					new CheckboxField('SentToCustomer', sprintf(_t('OrderReport.SENDNOTETO', "Send status update details to %s (%s)"), $member->Title, $member->Email), false),
					new HiddenField('OrderID', 'OrderID', $order->ID)
				);

				$actions = new FieldSet(
					new FormAction('doStatusForm', 'Add New Status Update')
				);

				$form = new Form(
					$this,
					'StatusForm',
					$fields,
					$actions
				);
				$OrderStatusLogWithDetails = DataObject::get_one("OrderStatusLogWithDetails", "OrderStatusLog.OrderID = ".$order->ID, "Created DESC");
				if($OrderStatusLogWithDetails) {
					$form->loadDataFrom($OrderStatusLogWithDetails);
				}
				return $form;
			}
		}
	}

	/**
	 * Return a form with a table in it showing
	 * all the statuses for the current Order
	 * instance that we're viewing.
	 *
	 * @TODO Rename this to StatusLogForm, and check templates.
	 *
	 * @return Form
	 */
	function StatusLog() {
		$table = new TableListField(
			'StatusTable',
			'OrderStatusLogWithDetails',
			array(
				'Created' => 'Created',
				'Status' => 'Status',
				'Note' => 'Note',
				'DispatchedBy' => 'Dispatched By',
				'DispatchedOn' => 'Dispatched On',
				'DispatchTicket' => 'Dispatch Ticket',
				'PaymentCode' => 'Payment Code',
				'PaymentOK' => 'Payment Cleared',
				'SentToCustomer' => 'Email update to Customer'
			),
			"OrderStatusLog.OrderID = {$this->urlParams['ID']}"
		);

		$table->setFieldCasting(array(
			'Created' => 'Date',
			'PaymentOK' => 'Boolean->Nice',
			'SentToCustomer' => 'Boolean->Nice',
		));
		$table->setPermissions(array('show'));
		$table->IsReadOnly = true;

		return new Form(
			$this,
			'OrderStatusLogWithDetailsForm',
			new FieldSet(
				new HeaderField('Order Status History',3),
				new HiddenField('ID'),
				$table
			),
			new FieldSet()
		);
	}

	/**
	 * Form submit handler for StatusForm()
	 *
	 * @param array $data Request data from form
	 * @param Form $form The form object submitted on
	 */
	function doStatusForm($data, $form) {
		if(!is_numeric($data['OrderID'])) {
			return false;
		}

		$order = DataObject::get_by_id("Order", $data['OrderID']);

	// if the status was changed or a note was added, create a new log-object
		$orderlog = new OrderStatusLogWithDetails();
		$orderlog->OrderID = $order->ID;
		$form->saveInto($orderlog);
		$orderlog->write();

		// save the order
		if($order) {
			$order->Status = $data['Status'];
			$order->write();
		}

		if(isset($_REQUEST['SentToCustomer'])) {
			if(($_REQUEST['SentToCustomer'])) {
				$order->sendStatusChange();
			}
		}

		return FormResponse::respond();
	}

}
