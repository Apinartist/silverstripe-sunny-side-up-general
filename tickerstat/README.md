###############################################
Ticker Stat Module
Pre 0.1 proof of concept
###############################################

Developer
-----------------------------------------------
Nicolaas Francken [at] sunnysideup.co.nz

Requirements
-----------------------------------------------
SilverStripe 2.3.0 or greater.

Documentation
-----------------------------------------------

Installation Instructions
-----------------------------------------------
1. Find out how to add modules to SS and add module as per usual.

2. copy configurations from this module's _config.php file
into mysite/_config.php file and edit settings as required.
NB. the idea is not to edit the module at all, but instead customise
it from your mysite folder, so that you can upgrade the module without redoing the settings.

3. add the following code to any page type / controller that wants to have ticker stat:


	/**
	 * inserts a piece of JS into your template
	 * #{code} - e.g. <div id="myCode"></div>
	 * use in template like this <div id="myStat">$TickerStat(myStat)</div>
	 * 
	 * @param String $code - id of the div AND name of the statistic
	 * @param Int $width - number of digits for the stat
	 * 
	 */
	public function TickerStat($code, $width = 8){
		$stat = DataObject::get_one("TickerStat", "\"Code\" = ''".Convert::raw2sql($code)."'");
		if($stat) {
			return Requirements::customScript(
				"TickerStat.createTicker(\"$code\", ".$stat->Number.", $width, ".$stat->SecondsBetweenChange.", ".$stat->Direction.");",
				"TickerStat_".$stat->Code
			);
		}
		else {
			user_error("Could not find ticker stat: <i>".$code"</i>", E_USER_NOTICE);
		}
	}



4. in your template for that page type, add:
 <div id="myStat">$TickerStat(myStat)</div>

