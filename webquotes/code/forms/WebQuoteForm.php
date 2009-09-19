<?php

/**
 * @author Nicolaas [at] sunnysideup.co.nz
 * @package Webquote
 * @sub-package Webquote
 */

class WebquoteForm extends Form {

	function __construct($controller, $name) {
		$fields =  = new FieldSet(
			new TabSet("Root",
				$tabContent1 = new TabSet('Requirements',
					new DropdownField("CurrencyConversion", "Currency", $this->CurrencyList()),
					new DropdownField("DesignOption", "Design Option"),
					new DropdownField("TimeframeOption", "Timeframe Option"),
					new DropdownField("WeDeliverYouDeliver", "We Deliver You Deliver")
				),
				$tabContent2 = new TabSet('Final Questions',
					new TextField("Name"),
					new TextareaField("NotesAndQuestions", "Notes And Questions"),
					new TextareaField("TemplatesRequired", "Templates Required"),
					new TextareaField("Goals","Goals"),
					new TextareaField("DeadLine","Goals"),
				)
			)
		);




		parent::__construct($controller, $name, $fields, $actions, $requiredFields);
	}


	function doWebQuote($form, $data) {


	}




}