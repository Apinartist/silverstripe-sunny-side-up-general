google.load('visualization', '1', {packages: ['corechart']});

function drawBarChart_Interactive(params) {
	
	var id = params['id'];
	var xTitles = params['xTitles'];
	var yTitles = params['yTitles'];
	var values = params['values'];
	var options = params['options'];
	
	var data = new google.visualization.DataTable();
	
	data.addColumn('string', 'XTitle');
	for(var i = 0; i < yTitles.length; i++) {
		data.addColumn('number', yTitles[i]);
	}
	
	data.addRows(xTitles.length);
	
	for(var i = 0; i < xTitles.length; i++) {
		data.setCell(i, 0, xTitles[i]);
		for(var j = 0; j < yTitles.length; j++) {
			var value = values[j][i];
			if(value !== null) {
				data.setCell(i, j + 1, value);
			}
		}
	}
	
	var chartClass = 'google.visualization.' + params['type'] + 'Chart';
	eval('var chart = new ' + chartClass + '(document.getElementById(id))');
	
	chart.draw(data, options);
	
	google.visualization.events.addListener(chart, 'ready', fixTooltip);
}