/**
 *
 *
 *
 *
 * usage: TickerStat.createTicker("homefooter", 123, 8, 10000, 1);
 * creates a ticker in the homefooter, like this:
 * <div id="homefooter">
 * 	<span>&nbsp;</span>
 * 	<span>&nbsp;</span>
 * 	<span>&nbsp;</span>
 * 	<span>&nbsp;</span>
 * 	<span>1</span>
 * 	<span>2</span>
 * 	<span>3</span>
 * </div>
 * and updates the number every 10,000 milli-seconds.
 */

TickerStat = {

	/**
	 * @number of digits
	 */
	width: new Array(),

	/**
	 * storage for all values
	 * @var Array
	 */
	values: new Array(),

	/**
	 * storage for all repeat functions
	 * @var Array
	 */
	repeatFunctions: new Array(),

	/**
	 * @param String code - code the stat and ID for element in which to show it
	 * @param String | Integer number - the number as a string (or as an integer)
	 * @param Int width - number of digits
	 * @param Int milliSecondsBetweenIncrements - number of milli seconds before the increment is added
	 * @param Int increment (usuall 1 or -1)
	 */
	createTicker: function(code, number, width, milliSecondsBetweenIncrements, increment) {
		TickerStat.values[code] = number;
		TickerStat.width[code] = width;
		if(milliSecondsBetweenIncrements) {
			TickerStat.repeatFunctions[code] = function(){
				var myCode = code;
				TickerStat.repeatFunctions[myCode+"_interval"] = window.setInterval(
					function() {
						TickerStat.values[myCode] = TickerStat.values[myCode] + increment;
						jQuery("#"+myCode).html(TickerStat.turnNumberIntroStringWithSpans(TickerStat.values[myCode], TickerStat.width[myCode]));
					},
					milliSecondsBetweenIncrements
				);
			}
			TickerStat.repeatFunctions[code]();
		}
		jQuery("#"+code).html(TickerStat.turnNumberIntroStringWithSpans(number, width));

	},

	/**
	 * returns a padding string with a span around each digit
	 * and a &nbsp; for each digit that has nothing in it
	 * up to a set width
	 * @param Int number
	 * @param Int width
	 */
	turnNumberIntroStringWithSpans: function(number, width) {
		string = number + "";
		var padding = width - string.length;
		outcome = "";
		for(i = 0; i < width; i++){
			if(i < padding) {
				outcome += "<span>&nbsp;</span>";
			}
			else {
				outcome += "<span>"+string[(-1 * padding) + i]+"</span>";
			}
		}
		return outcome;
	}

}



