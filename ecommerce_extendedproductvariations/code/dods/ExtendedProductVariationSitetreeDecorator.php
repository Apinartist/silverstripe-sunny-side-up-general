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
		if($this->owner instanceOf ProductGroup || $this->owner->DoNotAddVariationsAutomatically) {
			$tab = new tab(
				"ProductVariations",
				new HeaderField("DefaultVariationGroupsHeader",'Choose Applicable Variation Lists', 3),
				new LiteralField(
					"DefaultVariationGroupsExplanation",
					'<p>
						Selecting lists below will automatically add its options as product variations to all products listed under the current page.
						If the current page does not have any entries selected (ticked) then the entries from the parent (Product Group) page will be used.
						Make sure you have entered all the correct <a href="admin/productvariations/">groups and group options</a>.
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