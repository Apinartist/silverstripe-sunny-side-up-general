<?php



class EcommerceAlsoRecommendedDOD extends DataObjectDecorator {

	public function extraStatics() {
		return array (
			'many_many' => array(
				'RecommendedProducts' => 'Product'
			)
		);
	}

	function RecommendedProducts() {
		return $this->owner->RecommendedProducts();
	}

	function updateCMSFields(FieldSet &$fields) {
		if($this->owner instanceOf Product) {
			$field->addFieldToTab("Root.Content.RecommendedProducts",

		}
	}


}