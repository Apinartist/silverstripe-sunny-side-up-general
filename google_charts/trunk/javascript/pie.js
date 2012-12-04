google.load('visualization', '1', {packages: ['corechart']});

function drawPieChart_Interactive(params) {
	
	var id = params['id'];
	var titles = params['titles'];
	var values = params['values'];
	var options = params['options'];
	
	var data = new google.visualization.DataTable();
	
	data.addColumn('string', 'Task');
	data.addColumn('number', 'Number');
	
	data.addRows(titles.length);
	
	for(var i = 0; i < titles.length; i++) {
		data.setCell(i, 0, titles[i]);
		data.setCell(i, 1, values[i]);
	}
	
	var chart = new google.visualization.PieChart(document.getElementById(id));
	chart.draw(data, options);
}