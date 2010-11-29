google.load('visualization', '1', {packages: ['geomap']});

google.setOnLoadCallback(drawChart_$id);

function drawChart_$id() {
	
	var data = new google.visualization.DataTable();
	
	var records = [$records];
	var totals = [$totals];
	
	data.addRows(records.length);
	data.addColumn('string', 'Country');
	data.addColumn('number', '$title');
	
	for(var i = 0; i < records.length; i++) {
		data.setValue(i, 0, records[i]);
		data.setValue(i, 1, totals[i]);
	}
	
	var chart = new google.visualization.GeoMap(document.getElementById('$id'));
    chart.draw(data, {$options});
};