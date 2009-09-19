
 /**
 * @author Nicolaas [at] sunnysideup.co.nz
 *
 *
 *
 *
 *
 *
 */


;(function($) {
	$(document).ready(
		function() {
			webquotes.init();
		}
	);

	var webquotes = {

		openGroupAgain : null,

		tabindex : 0,

		selectedQuickCurrency : 0,

		selectedQuickCode : 0,

		quickCurrencySelect: "#quickCurrencySelect",

		quickCodeSelect: "#quickCodeSelect",

		rateCalculatorTableBodySelector: "#rateCalculatorTable tbody",

		confirmLooseSelectionMsg: "Are you sure you want to loose your current selection?",

		init: function() {
			jQuery(webquotes.quickCurrencySelect).html(webquotes.getCurrencyShortList());
			jQuery(webquotes.quickCodeSelect).html(webquotes.getCodeShortList());
			/*
			webquotes.createCostSelector();
			webquotes.createCurrencySelector();

			webquotes.createTimeSelector();
			webquotes.createCodeSelector();
			webquotes.createDesignSelector();
			webquotes.createTemplateSelector();

			$("input[@name='costSelector']:nth(" + defaultCost+ ")").attr("checked","checked");
			$("input[@name='currencySelector']:nth(" + defaultCurrency + ")").attr("checked","checked");
			$("input[@name='timeSelector']:nth(" + defaultTime+ ")").attr("checked","checked");
			$("input[@name='codeSelector']:nth(" + defaultCode+ ")").attr("checked","checked");
			*/
			webquotes.createSelectTable();

			if(defaultItems != "") {
				var itemArray = defaultItems.split("_");
				for(var i = 0; i < itemArray.length; i++) {
					var itemValues = itemArray[i].split(",");
					var item = itemValues[0];
					var value = itemValues[1];
					$("#c" + item).val(value);
				}
			}

			webquotes.webquotes.calculate();
			webquotes.recalculateQuickSelect();
			if(defaultItems != "") {
				webquotes.showHideAllPrices();
			}
		},

//------------------ create main tables

		createSelectTable: function () {
			$(webquotes.rateCalculatorTableBodySelector).children().remove();
			var costSelectorValue = webquotes.getCostSelectorValue();
			var html = '';
			var lastGroup;
			var currentGroup;
			var nextGroup;
			var inputID = '';
			var currentGroupPlusOne;
			for(var i = 0; i < d.length; i++) {
				inputID = 'c' + i;
				currentGroup = d[i][2];
				if((i + 1) < d.length) {
					nextGroup = d[i + 1][2];
				}
				else {
					nextGroup = 0;
				}
				if(currentGroup != lastGroup) {
					currentGroupPlusOne = currentGroup;
					currentGroupPlusOne++;
					html += '<tr class="subHeadingRow" id="trG' + currentGroup + '">';
					html += '<th class="subHeading" colspan="2" rel="' + currentGroupPlusOne + '">';
					html += (currentGroup + 1) + ". " + groupPlan[currentGroup][0] + ' - <a href="/' + jsCurrentPageCode + '#trG' + currentGroup + '" onclick="return !webquotes.showGroup(' + currentGroup + ')" class="hideShowGroup">view and select items</a> ';
					if(groupPlan[currentGroup][1]) {
						html += ' <span class="groupExplanation" id="exp' + currentGroup + '" style="display: none;">|<a href="#" onclick="return !webquotes.showGroupExplanation(this);">explanation &raquo;</a>';
						html += ' <span style="display: none;"  class="groupExplanation"><i class="houseIntro">' + houseIntro + '</i>'+ groupPlan[currentGroup][1] + '</span>';
					}
					html += '</th>';
					html += '<th id="subTotal' + currentGroup + '" class="currencyCell">-</th>';
					html += '</tr>';
				}
				if(d[i][6] == 1) {
					perStatement = ' ::: price per month ';
				}
				else {
					perStatement = '';//once only price
				}
				lastGroup = currentGroup;
				if(d[i][5]) {
					moreInfo = ' <a href="#" class="moreLink" onclick="return !webquotes.showMore(this);">&raquo;</a><span style="display: none;">' + d[i][5] + '</span>';
				}
				else {
					moreInfo = '';
				}
				html += '<tr id="tr'+inputID+'" class="tr' + currentGroup + ' row">';
				html += '<td>'+ d[i][0] + perStatement + moreInfo + '</td>';
				html += '<td class="qtyCell">';
				html += '<input type="text" name="'+inputID+'" id="' + inputID + '" class="quantityBox" onclick="webquotes.toggleValue(this); " onchange="webquotes.calculate();" maxlength="3" value="0" autocomplete="off" tabindex='+(i+100)+'/>';
				html += '<input type="hidden" name="h'+i+'" value="'+i+'" />';
				html += '<input type="hidden" name="t'+i+'" value="0" />';
				html += '</td>';
				html += '<td id="sum'+inputID+'" class="currencyCell">-</td>';
				html += '</tr>';
			}
			$(webquotes.rateCalculatorTableBodySelector).append(html);
		},

//----------- calculate


		calculate: function () {
			var costSelectorValue = webquotes.getCostSelectorValue();
			var currencySelectorValue = webquotes.getCurrencySelectorValue();
			var timeSelectorValue = webquotes.getTimeSelectorValue();
			var codeSelectorValue = webquotes.getCodeSelectorValue();
			var designSelectorValue = webquotes.getDesignSelectorValue();
			var sumDev = 0;
			var sumMonthly = 0;
			var subTotal = 0;
			var currentGroup;
			var codeGroup; //how much is it influenced by design
			var lastGroup;
			var timeBased;
			var monthlyPrice = 0;
			var price = 0;
			var quantity = 0;
			var quantityTimesPrice = 0;
			var inputID = '';
			var url = "http://www.sunnysideup.co.nz/services?"
			url += "cost=" + webquotes.getRadioValue('costSelector')
				+ "&currency=" + webquotes.getRadioValue('currencySelector')
				+ "&time=" + webquotes.getRadioValue('timeSelector')
				+ "&code=" + webquotes.getRadioValue('codeSelector')
				+ "&design=" + webquotes.getRadioValue('designSelector')
				+ "&countAll=" + (d.length - 1)
				+ "&use=" + use
				+ "&items=";
			for(var i = 0; i < d.length; i++) {
				inputID = 'c' + i;
				currentGroup = d[i][2];
				codeGroup = d[i][4];
				timeBased = d[i][6];
				//price * currency * urgency adjuster
				price = parseFloat(d[i][1]) * currencySelectorValue * timeSelectorValue;
				if(currentGroup > 0) {
					//client discount
					price = price * costSelectorValue;
				}
				else {
					price = price;
				}
				//items that need to be designed are adjusted for (a. how much design in element, b. how complex the design will be and c. how much we do for the design
				if(codeGroup > 0) {
					price = price * codeGroup * designSelectorValue * codeSelectorValue;
				}
				quantity = parseFloat( $("#" + inputID).val() );
				if(!quantity) {
					quantity = 0;
				}
				else if(quantity > 0) {
					url += i + "," + quantity + "_";
				}
				$("#" + inputID).val(quantity);
				quantityTimesPrice = parseFloat( price * quantity );
				$("input[@name='t" + i + "']").val(quantityTimesPrice);
				if(timeBased == 1) {
					monthlyPrice = quantityTimesPrice * (1/12)
					sumMonthly += monthlyPrice;
					$("#sum"+inputID).html(webquotes.currencyFormat(monthlyPrice));
				}
				else {
					sumDev += quantityTimesPrice;
					$("#sum"+inputID).html(webquotes.currencyFormat(quantityTimesPrice));
				}
				if(currentGroup != lastGroup && i) {
					$("#subTotal" + lastGroup).html(webquotes.currencyFormat(subTotal));
					subTotal = 0;
				}
				lastGroup = currentGroup;
				subTotal += quantityTimesPrice;
				if(quantity) {
					$("#tr"+inputID).addClass("selectedRow");
					$("#tr"+inputID).removeClass("notSelectedRow");
				}
				else {
					$("#tr"+inputID).removeClass("selectedRow");
					$("#tr"+inputID).addClass("notSelectedRow");
				}
			}
			url = url + "#costCalculatorStart";
			$("#subTotal" + lastGroup).html(webquotes.currencyFormat(subTotal));
			$("#totalSumDev").html(webquotes.currencyFormat(sumDev));
			$("#totalSumMonthly").html(webquotes.currencyFormat(sumMonthly));
			var timeDivider = webquotes.getTimeSelectorValueCostDivider();
			var originalPrice = sumDev / currencySelectorValue / costSelectorValue / timeSelectorValue;
			timeDivider = (Math.round( originalPrice / timeDivider)*7);
			if(timeDivider <= 7) {
				timeDivider = "< one week";
			}
			else {
				timeDivider = "< " + timeDivider + " days";
			}
			$("#calculatorUrl").val(url);
			$("#emailQuote").attr("href", 'mailto:?subject=' + escape("quote") + '&body=' + escape(url));
			$("#totalTime").html(timeDivider);
		},

//------------------- interactions

		toggleValue: function (el) {
			var doBlur = false;
			if($(el).val() == 0) {
				$(el).val(1);
				doBlur = true;
			}
			else if($(el).val() == 1) {
				$(el).val(0);
				doBlur = true;
			}
			if(doBlur) {
				webquotes.calculate();
			}
		},

		showMore: function (el) {
			if($(el).next().is(":hidden")) {
				$(el).html("&laquo;");
				$(el).next().slideDown();
			}
			else {
				$(el).html("&raquo;");
				$(el).next().slideUp();
			}
			return true;
		},

		showGroupExplanation: function (el) {
			if($(el).next().is(":hidden")) {
				$(el).html("&laquo;");
				$(el).next().slideDown();
			}
			else {
				$(el).html("&raquo;");
				$(el).next().slideUp();
			}
			return true;
		},

		showGroup: function (newGroupShown) {
			$("th").removeClass("lefty");
			$(".groupExplanation").hide();
			$("a.groupExplanation").html("&raquo;");
			if( $(".tr" + newGroupShown).is(":hidden") ) {
				$(".row").hide();
				$(".subHeading a.hideShowGroup").text("view and select items");
				$('#exp' + newGroupShown).css("display", "inline");
				$(".tr" + newGroupShown).show();
				$("#trG" + newGroupShown + " th.subHeading").addClass("lefty");
				$("#trG" + newGroupShown + " th a.hideShowGroup").text("hide");
			}
			else {
				$(".tr" + newGroupShown).hide();
				$("#trG" + newGroupShown + " th a.hideShowGroup").text("view and select items");
			}
			return false;
		},

		showHideAllPrices: function () {
			/* just check open areas */
			if( $(".subHeadingRow").is(":hidden") ) {
				$(".row").hide();
				$(".subHeadingRow").show();
				$(".showHideAllPricesLinks").text("show selected items only");
				if(openGroupAgain) {
					webquotes.showGroup(openGroupAgain - 1);
				}
			}
			else {
				openGroupAgain = $(".lefty").attr("rel");
				$("th").removeClass("lefty");
				$(".row").show();
				$(".notSelectedRow").hide();
				$(".subHeadingRow").hide();
				$(".showHideAllPricesLinks").text("show grouped options");
			}
			return true;
		},

//----------- changes

		changeTemplate: function () {
			var message = "";
			if(confirm(webquotes.confirmLooseSelectionMsg) == true) {
				var newTemplate = webquotes.getRadioValue("templateSelector") - 1;
				var mustChange = 1;
				var value = 0;
				var inputID = '';
				if(newTemplate < 0) {
					var mustChange = 0;
				}
				for(var i = 0; i < d.length; i++) {
					inputID = 'c' + i;
					value = 0;
					if(mustChange == 1) {
						value = d[i][3][newTemplate];
					}
					$("#" + inputID).val(value);
				}
				webquotes.calculate();
				newTemplate++;
				if(newTemplate) {
					var regexp = /[aeiou]/
					var aInsert = "a";
					var matches = regexp.exec(templatePlan[newTemplate][0])
					if(matches){
						aInsert = "an";
					}

					message = "The default options for " + aInsert + ' "' + templatePlan[newTemplate] + '" have been loaded.';
				}
				else {
					message = "All options have been removed.";
				}
			}
			else {
				message = "no changes were made.";
			}
			webquotes.showHideAllPrices();
			//do we need it two times?
			webquotes.showHideAllPrices();
			alert(message);
		},


		changeCurrency: function (value, element) {
			selectedQuickCurrency = value;
			$(webquotes.quickCurrencySelect + " a").removeClass("selectedQuickCurrency");
			$(element).addClass("selectedQuickCurrency");
			webquotes.recalculateQuickSelect();
			return true;
		},

		changeCodeOption: function (value, element) {
			selectedQuickCode = value;
			$(webquotes.quickCodeSelect + " a").removeClass("selectedQuickCode");
			$(element).addClass("selectedQuickCode");
			webquotes.recalculateQuickSelect();
			return true;
		},

		recalculateQuickSelect: function () {
			$("#basicQuote td span.p").each(
				function (i) {
					var field =  $(this);
					var originalValue = parseFloat(field.attr("rel") - 0);
					var codeConversion = codeOption[selectedQuickCode][1];
					var currencyConversion = currencyPlan[selectedQuickCurrency][2];
					var currency = currencyPlan[selectedQuickCurrency][0];
					var costSelectorValue = webquotes.getCostSelectorValue();
					if(originalValue < 150) {
						var newValue = originalValue * currencyConversion;
					}
					else {
						var newValue = originalValue * currencyConversion * codeConversion * costSelectorValue;
					}
					field.html(currency + (newValue).toFixed(2));
				}
			);
		},

//----------------------- get values

		getCostSelectorValue: function () {
			var costSelectorSelection = webquotes.getRadioValue('costSelector');
			costSelectorValue = costPlan[costSelectorSelection][1];
			if(!costSelectorValue) {
				costSelectorValue = 1;
			}
			else {
				$("input[@name='hiddenCostSelector']").val(costPlan[costSelectorSelection][0])
			}
			return parseFloat(costSelectorValue);
		},

		getCurrencySelectorValue: function () {
			var currencySelectorSelection = webquotes.getRadioValue('currencySelector');
			var currencySelectorValue = currencyPlan[currencySelectorSelection][2];
			if(!currencySelectorValue) {
				currencySelectorSelection = 1;
			}
			else {
				$("input[@name='hiddenCurrencySelector']").val(currencyPlan[currencySelectorSelection][0])
			}
			return parseFloat(currencySelectorValue);
		},

		getTimeSelectorValue: function () {
			var timeSelectorSelection = webquotes.getRadioValue('timeSelector');
			var timeSelectorValue = timePlan[timeSelectorSelection][1];
			if(!timeSelectorValue) {
				timeSelectorSelection = 1;
			}
			else {
				$("input[@name='hiddenTimeSelector']").val(timePlan[timeSelectorSelection][0])
			}
			return parseFloat(timeSelectorValue);
		},

		getCodeSelectorValue: function () {
			var codeSelectorSelection = webquotes.getRadioValue('codeSelector');
			var codeSelectorValue = codeOption[codeSelectorSelection][1];
			if(!codeSelectorValue) {
				codeSelectorSelection = 0;
			}
			else {
				$("input[@name='hiddenCodeSelector']").val(codeOption[codeSelectorSelection][0])
			}
			return parseFloat(codeSelectorValue);
		},

		getDesignSelectorValue: function () {
			var designSelectorSelection = webquotes.getRadioValue('designSelector');
			var designSelectorValue = designOption[designSelectorSelection][1];
			if(!designSelectorValue) {
				designSelectorSelection = 0;
			}
			else {
				$("input[@name='hiddenDesignSelector']").val(designOption[designSelectorSelection][0])
			}
			return parseFloat(designSelectorValue);
		},

		getTimeSelectorValueCostDivider: function () {
			var timeSelectorSelection = webquotes.getRadioValue('timeSelector');
			var timeSelectorValue = timePlan[timeSelectorSelection][2];
			timeSelectorValue = parseFloat(timeSelectorValue);
			if(timeSelectorValue < 1) {
				timeSelectorValue = 1;
			}
			return timeSelectorValue;
		},


		getRadioValue: function (inputName) {
			var value = parseFloat($("input[@name='" + inputName + "']:checked").val());
			if(!value) {
				value = 0;
			}
			return value
		},



		getCurrencyShortList: function () {
			var html = '<b>Choose currency:<\/b> ';
			var className = 'selectedQuickCurrency';
			for(var i = 0; i < currencyPlan.length; i++) {
				if(i > 0) {
					html += ', ';
					className = '';
				}
				else {
					selectedQuickCurrency = i;
				}
				html += '<a href="#" onclick="return !webquotes.changeCurrency('+i+', this);" class="'+className+'">' + currencyPlan[i][0] + '</a>';
			}
			html += '';
			return html;
		},

		getCodeShortList: function () {
			var html = '<b>You will provide:</b> '
			var className = 'selectedQuickCode';
			var notFirst = false;
			for(var i=0; i < codeOption.length; i++) {
				if(codeOption[i][2])  {
					if(notFirst) {
						html += ', ';
						className = '';
					}
					else {
						selectedQuickCode = i;
					}
					html += '<a href="#" onclick="return !webquotes.changeCodeOption('+i+', this); " class="'+className+'">' + codeOption[i][2] + '</a>';
					notFirst = true;
				}
			}
			html += '';
			return html;
		},

//--------------------------- create selectors

		createCostSelector: function () {
			var html = '<p>your account:';
			var selectStatement = 'checked="checked" ';
			for(var i = 0; i < costPlan.length; i++) {
				tabindex++;
				html += '<label><input type="radio" name="costSelector" value="' + i + '" onclick="webquotes.calculate();" '+selectStatement+' tabindex="'+ tabindex + '" />' + costPlan[i][0] + '</label>';
				selectStatement = '';
			}
			html += '<input type="hidden" name="hiddenCostSelector" />';
			html += '</p>';
			$("#rateSelector").html(html);
		},

		createCurrencySelector: function () {
			var html = '<p>invoice currency:';
			var selectStatement = 'checked="checked" ';
			for(var i = 0; i < currencyPlan.length; i++) {
				tabindex++;
				html += '<label><input type="radio" name="currencySelector" value="' + i + '" onclick="webquotes.calculate();" '+selectStatement+'  tabindex="'+ tabindex + '" />' + currencyPlan[i][1] + '</label>';
				selectStatement = '';
			}
			html += '<input type="hidden" name="hiddenCurrencySelector" />';
			html += '</p>';
			$("#currencySelector").html(html);
		},

		createTemplateSelector() {
			var html = '<p>preload default options for:';
			var selectStatement = 'checked="checked" ';
			for(var i = 0; i < templatePlan.length; i++) {
				tabindex++;
				html += '<label><input type="radio" name="templateSelector" value="' + i + '" onclick="changeTemplate();" '+selectStatement+'  tabindex="'+ tabindex + '" />' + templatePlan[i] + '</label>';
				selectStatement = '';
			}
			html += '</p>';
			$("#templateSelector").html(html);
		},

		createTimeSelector: function () {
			var html = '<p>delivery speed:';
			var selectStatement = 'checked="checked" ';
			for(var i = 0; i < timePlan.length; i++) {
				tabindex++;
				html += '<label><input type="radio" name="timeSelector" value="' + i + '" onclick="webquotes.calculate();" '+selectStatement+' tabindex="'+ tabindex + '" />' + timePlan[i][0] + '</label>';
				selectStatement = '';
			}
			html += '<input type="hidden" name="hiddenTimeSelector" />';
			html += '</p>';
			$("#timeSelector").html(html);
		},

		createCodeSelector: function () {
			var html = '<p>you provide...';
			var selectStatement = 'checked="checked" ';
			for(var i = 0; i < codeOption.length; i++) {
				tabindex++;
				html += '<label><input type="radio" name="codeSelector" value="' + i + '" onclick="webquotes.calculate();" '+selectStatement+' tabindex="'+ tabindex + '" />' + codeOption[i][0] + '</label>';
				selectStatement = '';
			}
			html += '<input type="hidden" name="hiddenCodeSelector" />';
			html += '</p>';
			$("#codeSelector").html(html);
		}

		createDesignSelector: function () {
			var html = '<p>Design Standard...';
			var selectStatement = 'checked="checked" ';
			for(var i = 0; i < designOption.length; i++) {
				tabindex++;
				html += '<label><input type="radio" name="designSelector" value="' + i + '" onclick="webquotes.calculate();" '+selectStatement+' tabindex="'+ tabindex + '" />' + designOption[i][0] + '</label>';
				selectStatement = '';
			}
			html += '<input type="hidden" name="hiddenDesignSelector" />';
			html += '</p>';
			$("#designSelector").html(html);
		},


//------------ formatting
		currencyFormat: function (v) {
			var currencySelectorSelection = webquotes.getRadioValue('currencySelector');
			var currencySelectorString = currencyPlan[currencySelectorSelection][0];
			if(!v) {
				return "-";
			}
			return currencySelectorString + v.toFixed(2);
		}

	}
})(jQuery);
