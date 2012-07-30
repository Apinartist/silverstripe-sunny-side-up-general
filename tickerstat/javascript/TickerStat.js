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

	width: new Array(),

	values: new Array(),

	repeatFunctions: new Array(),
	/**
	 * @param Int
	 * @param String (HTML)
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



