google.load('visualization', '1', {packages: ['geomap']});

function drawMapChart_Geo(params) {
	
	var id = params['id'];
	var title = params['title'];
	var records = params['records'];
	var totals = params['totals'];
	var options = params['options'];
	
	var data = new google.visualization.DataTable();
	
	data.addColumn('string', 'Country');
	data.addColumn('number', title);
	
	data.addRows(records.length);
	
	for(var i = 0; i < records.length; i++) {
		data.setValue(i, 0, records[i]);
		data.setValue(i, 1, totals[i]);
	}
	
	var chart = new google.visualization.GeoMap(document.getElementById(id));
    chart.draw(data, options);
};