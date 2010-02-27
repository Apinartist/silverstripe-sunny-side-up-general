<?php
/**
 * @author Nicolaas [at] sunnysideup.co.nz
 */
class SalesAdmin extends ReportAdmin {

	static $url_segment = 'sales';

	static $url_rule = '/$Action/$ID';

	static $menu_title = 'Sales';

	static $template_path = null; // defaults to (project)/templates/email

	public function init() {
		parent::init();
	}

	/**
	 * Does the parent permission checks, but also
	 * makes sure that instantiatable subclasses of
	 * {@link Report} exist. By default, the CMS doesn't
	 * include any Reports, so there's no point in showing
	 *
	 * @param Member $member
	 * @return boolean
	 */


	/**
	 * Return a DataObjectSet of SSReport subclasses
	 * that are available for use.
	 *
	 * @return DataObjectSet
	 */
	public function Reports() {
		$processedReports = array();
		$subClasses = ClassInfo::subclassesFor('SalesReport');

		if($subClasses) {
			foreach($subClasses as $subClass) {
				if($subClass != 'SalesReport') {
					$processedReports[] = new $subClass();
				}
			}
		}
		$processedReports[] = new PaymentsReport();
		$processedReports[] = new DraftOrderReport();
		$reports = new DataObjectSet($processedReports);

		return $reports;
	}

}