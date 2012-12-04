google.load('visualization', '1', {packages: ['gauge']});
 
function drawGaugeChart(params) {
	
	var id = params['id'];
	var title = params['title'];
	var value = params['value'];
	var options = params['options'];
	
	var data = new google.visualization.DataTable();
	
	data.addColumn('string', 'Label');
	data.addColumn('number', 'Value');
	data.addRows(1);
	
	data.setValue(0, 0, title);
	data.setValue(0, 1, value);

	var chart = new google.visualization.Gauge(document.getElementById(id));
    chart.draw(data, options);
}