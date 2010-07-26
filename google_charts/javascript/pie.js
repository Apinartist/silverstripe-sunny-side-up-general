google.load('visualization', '1', {packages: ['corechart']});

google.setOnLoadCallback(drawChart_$id);

function drawChart_$id() {
	
	var data = new google.visualization.DataTable();
	
	data.addColumn('string', 'Task');
	data.addColumn('number', 'Number');
	
	var titles = [$titles];
	var values = [$values];
	
	data.addRows(titles.length);
	
	for(var i = 0; i < titles.length; i++) {
		data.setCell(i, 0, titles[i]);
		data.setCell(i, 1, values[i]);
	}
	
	var chart = new google.visualization.PieChart(document.getElementById('$id'));
	chart.draw(data, {$options});
}