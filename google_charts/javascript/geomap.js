google.load('visualization', '1', {packages: ['geomap']});

google.setOnLoadCallback(drawChart_$id);

function drawChart_$id() {
	
	var data = new google.visualization.DataTable();
	
	data.addColumn('string', 'Country');
	data.addColumn('number', '$title');
	
	var records = [$records];
	var totals = [$totals];
	
	data.addRows(records.length);
	
	for(var i = 0; i < records.length; i++) {
		data.setValue(i, 0, records[i]);
		data.setValue(i, 1, totals[i]);
	}
	
	var chart = new google.visualization.GeoMap(document.getElementById('$id'));
    chart.draw(data, {$options});
};