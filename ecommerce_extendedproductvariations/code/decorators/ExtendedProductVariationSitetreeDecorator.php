<?php

class ExtendedProductVariationSitetreeDecorator extends SiteTreeDecorator {

	function extraStatics() {
		return array(
			'many_many' => array(
				'ExtendedProductVariationGroups' => 'ExtendedProductVariationGroup'
			)
		);
	}


	function getParentExtendedProductVariationGroups() {
		$dos = $this->owner->ExtendedProductVariationGroups();
		$array = $dos->getIdList();
		if(count($array)) {
			return $dos;
		}
		elseif($this->owner->ParentID) {
			return $this->owner->Parent()->getParentExtendedProductVariationGroups();
		}
	}


	function updateCMSFields(FieldSet &$fields) {

		if($this->owner instanceOf ProductGroup || $this->owner instanceOf Product) {
			$tab = new tab(
				"ProductVariationDefaults",
				new LiteralField(
					"DefaultVariationGroupsExplanation",
					'<p>
						Selecting groups below will automatically add its options as product variations to this product.
						You can also <a href="admin/productvariations/">manage the groups and group options</a> directly.
					</p>'
				),
				$this->getExtendedProductVariationGroupsTable()
			);
			$fields->addFieldsToTab(
				"Root.Content",
				$tab
			);
		}
		return $fields;
	}

	function getExtendedProductVariationGroupsTable() {
		/*
		$ExtendedProductVariationGroupArray = DataObject::get("ExtendedProductVariationGroup")->toDropdownMap('ID','Name');
		new MultiSelectField(
			'ExtendedProductVariationGroups',
			'automatically add options from the following option groups ...',
			$ExtendedProductVariationGroupArray
		)
		*/
		$field = new ManyManyComplexTableField(
			$this->owner,
			'ExtendedProductVariationGroups',
			'ExtendedProductVariationGroup',
			array('Name' => 'Name'),
			null,
			null,
			"`Checked` DESC, `Name` ASC"
		);
		$field->setPermissions(array());
		$field->pageSize = 1000;
		return $field;

	}



}