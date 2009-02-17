<?php
/**
 * GoogleMapLocationsObject.php: Sub-class of DataObject
 * DataObject holding all GeoPoints
 * @created 14/10/2008
 */

class GoogleMapLocationsObject extends DataObject {

	static $db = array (
				'PointType' =>'Enum("none, point, polyline, polygon", "point")',
				'Accuracy' => 'Int',
				'Latitude' => 'Double(12,7)',
				'Longitude' => 'Double(12,7)',
				'PointString' => 'Text',
				'Address' => 'Text',
				'FullAddress' => 'Text',
				'CountryNameCode' => 'Varchar(3)',
				'AdministrativeAreaName' => 'Varchar(255)',
				'SubAdministrativeAreaName' => 'Varchar(255)',
				'LocalityName' => 'Varchar(255)',
				'ThoroughfareName' => 'Varchar(255)',
				'PostalCodeNumber' => 'Varchar(30)',
				'Manual' => 'Boolean',
				//'GeoPointField' => 'GeoPoint',
				//'GeoPolygonField' => 'GeoPolygon',
				//'GeoLineString' => 'GeoLineString'
	);

	static $has_one = array (
		'Parent' => 'SiteTree'
	);

	static $has_many = array (
		//'' => ''
	);

	/*static $many_many = array (

	);

	static $belongs_many_many = array (

	);*/

	static $defaults = array (
		// '' => ''
	);

	var $ParentData = null;

	static function radiusDefinition($lon, $lat) {
		return '(6378.137 * ACOS( ( SIN( PI( ) * '.$lat.' /180 ) * SIN( PI( ) * `GoogleMapLocationsObject`.`Latitude` /180 ) ) + ( COS( PI( ) * '.$lat.' /180 ) * cos( PI( ) * `GoogleMapLocationsObject`.`Latitude` /180 ) * COS( (PI( ) * `GoogleMapLocationsObject`.`Longitude` /180 ) - ( PI( ) *'.$lon.' /180 ) ) ) ) ) ';
	}

	static function radiusDefinitionOtherTable($lon, $lat, $table, $latitudeField, $longitudeField) {
		return '(6378.137 * ACOS( ( SIN( PI( ) * '.$lat.' /180 ) * SIN( PI( ) * `'.$table.'`.`'.$latitudeField.'` /180 ) ) + ( COS( PI( ) * '.$lat.' /180 ) * cos( PI( ) * `'.$table.'`.`'.$latitudeField.'` /180 ) * COS( (PI( ) * `'.$table.'`.`'.$longitudeField.'` /180 ) - ( PI( ) *'.$lon.' /180 ) ) ) ) ) ';
	}

	static function pointExists($addressArray) {
		return DataObject::get_one("GoogleMapLocationsObject", 'Longitude = "'.$addressArray[0].'" AND Latitude = "'.$addressArray[1].'"');
	}

	function  getCMSFields_forPopup($parentPageID) {
		$fieldset = new FieldSet(
			new TextField('Address', 'Enter Full Address (e.g. 123 Main Street, Newtown, Wellington, New Zealand ) - all other fields will be auto-completed (looked up at Google Maps)'),
			//new CheckboxField('Manual', 'Edit Manually (save and reload to change)'),
			new HiddenField('ParentID', 'ParentID', $parentPageID)
		);
		$fieldset->push(new HeaderField('Auto-completed (not required)', 2));
		$fieldset->push(new NumericField('Latitude', 'Latitude'));
		$fieldset->push(new NumericField('Longitude', 'Longitude'));
		$fieldset->push(new TextField('PointString', 'PointString'));
		$fieldset->push(new TextField('FullAddress', 'Found Address'));
		$fieldset->push(new NumericField('Accuracy', 'Accuracy'));
		$fieldset->push(new TextField('CountryNameCode', 'Country Name Code'));
		$fieldset->push(new TextField('AdministrativeAreaName', 'Administrative Area Name'));
		$fieldset->push(new TextField('SubAdministrativeAreaName', 'SubAdministrative Area Name'));
		$fieldset->push(new TextField('LocalityName', 'Locality Name'));
		$fieldset->push(new TextField('ThoroughfareName', 'Thoroughfare Name'));
		$fieldset->push(new TextField('PostalCodeNumber', 'Postal Code Number'));
				//'GeoPointField' => 'GeoPoint',
					//'GeoPolygonField' => 'GeoPolygon',
					//'GeoLineString' => 'GeoLineString'
		return $fieldset;
	}

	function addParentData() {
		$parent = $this->Parent();
		$this->URLSegment = $parent->URLSegment;
		$this->Title = $parent->MenuTitle;
		$this->Name = $parent->MenuTitle;
		$this->AjaxInfoWindowLink = $parent->AjaxInfoWindowLink();
		$this->ParentClassName = $parent->ClassName;
		$this->ParentData = $parent;
	}

	function complexTableFields() {
		$fields = array(
			'FullAddress' => 'FullAddress',
			'Longitude' => 'Longitude',
			'Latitude' => 'Latitude',
		);
		return $fields;
	}
	function onBeforeWrite() {
		parent::onBeforeWrite();
		/*
		$this->GeoPointField->setX($this->Latitude);
		$this->GeoPointField->setX($this->Longitude);
		parent::onBeforeWrite();
		*/
		$this->findGooglePoints();
	}

	function completePoints() {
		$uncompletedPoints = DataObject::get("GoogleMapLocationsObject", '`GoogleMapLocationsObject`.`Address` <> `GoogleMapLocationsObject`.`FullAddress` AND `GoogleMapLocationsObject`.`Manual` <> 1 AND `GoogleMapLocationsObject`.`Address` <> IsNull AND `GoogleMapLocationsObject`.`Address` <');
		if($uncompletedPoints) {
			foreach($uncompletedPoints as $point) {
				$point->findGooglePoints();
			}
		}
	}
	function findGooglePoints() {
		if($this) {
			if(!$this->Manual && (!$this->Latitude || !$this->Longitude)) {
				if($this->Address) {
					$newData = GetLatLngFromGoogleUsingAddress::get_placemark_as_array($this->Address);
					$this->addDataFromArray($newData);
				}
			}
		}
	}

	function addDataFromArray($newData) {
		if(isset($newData["address"])) {$this->FullAddress = $newData["address"]; $this->Address = $newData["address"];}
		if(isset($newData[0])) {$this->Longitude = $newData[0];}
		if(isset($newData[1])) {$this->Latitude = $newData[1];}
		if(isset($newData["CountryNameCode"])) {$this->CountryNameCode = $newData["CountryNameCode"];}
		if(isset($newData["AdministrativeAreaName"])) {$this->AdministrativeAreaName = $newData["AdministrativeAreaName"];}
		if(isset($newData["SubAdministrativeAreaName"])) {$this->SubAdministrativeAreaName = $newData["SubAdministrativeAreaName"];}
		if(isset($newData["LocalityName"])) {$this->LocalityName = $newData["LocalityName"];}
		if(isset($newData["ThoroughfareName"])) {$this->ThoroughfareName = $newData["ThoroughfareName"];}
		if(isset($newData["PostalCodeNumber"])) {$this->PostalCodeNumber = $newData["PostalCodeNumber"];}
		$this->Write();
	}
}