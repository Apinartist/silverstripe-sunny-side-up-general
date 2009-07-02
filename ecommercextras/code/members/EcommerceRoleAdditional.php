<?php
/**
 * EcommerceRole provides customisations to the {@link Member}
 * class specifically for this ecommerce module.
 *
 * @package ecommerce
 */
class EcommerceRoleAdditional extends DataObjectDecorator {

	function extraStatics() {
		return array(
			'db' => array(
				'ENews' => 'Boolean',
				'ProNewsletter' => 'Boolean'
			)
		);
	}

}
