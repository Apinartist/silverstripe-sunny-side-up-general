google.load('visualization', '1', {packages: ['gauge']});
 
google.setOnLoadCallback(drawChart_$id);

function drawChart_$id() {
	
	var data = new google.visualization.DataTable();
	
	data.addColumn('string', 'Label');
	data.addColumn('number', 'Value');
	data.addRows(1);
	
	data.setValue(0, 0, '$title');
	data.setValue(0, 1, $value);

	var chart = new google.visualization.Gauge(document.getElementById('$id'));
    chart.draw(data, {$options});
}