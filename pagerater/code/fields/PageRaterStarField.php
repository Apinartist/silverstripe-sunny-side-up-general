<?php

class PageRaterStarField extends FormField {

	private $rating = 0;
	private $starOptions = 0;
	
	/**
	 * Returns an input field, class="start field" and type="hidden" with an optional maxlength
	 */
	function __construct($name, $title = null, $value = "", $starOptions, $form = null){
		
		$this->starOptions = $starOptions;
		
		parent::__construct($name, $title, $value, $form);
	}
	
	function Field() {
		Requirements::javascript(THIRDPARTY_DIR .'/jquery/jquery.js');
		Requirements::javascript(THIRDPARTY_DIR .'/jquery-form/jquery.form.js');
		Requirements::javascript('pagerater/javascript/formSubmit.js');
		Requirements::javascript('pagerater/javascript/jquery.rating.pack.js');
		Requirements::themedCSS('jquery.rating');
		
		$html = "";
		
		$name = $this->Name();
		$id = $this->id();
		for($i = 1; $i < $this->starOptions + 1; $i++){
			if($i == $this->Value()){
				$html .= "<input name='$id' class='$id' type='radio' checked='checked' value='$i' />";
			}else{
				$html .= "<input name='$id' class='$id' type='radio' value='$i' />";
			}
		}
		$html .= "<input name='$name' type='hidden' id='$id' />";
		
		Requirements::customScript(<<<JS
			jQuery('.$id').rating({
				required: true,
				callback: function(value, link){ 
					jQuery('#$id').val(value); 
				}
			});
JS
);
		
		return $html;
	}

}