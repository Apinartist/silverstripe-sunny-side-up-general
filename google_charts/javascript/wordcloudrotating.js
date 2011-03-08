google.load('visualization', '1');

function drawWordCloudChart_Rotating(params) {
	
	var id = params['id'];
	var words = params['words'];
	var fontsizes = params['fontsizes'];
	var options = params['options'];
	
	var data = new google.visualization.DataTable();
	
	data.addColumn('string', 'Tag');
	data.addColumn('string', 'URL');
	data.addColumn('number', 'Font size');
	
	data.addRows(words.length);
	
	for(var i = 0; i < words.length; i++) {
		data.setCell(i, 0, words[i]);
		data.setCell(i, 1, '');
		data.setCell(i, 2, fontsizes[i]);
	}
	
	var chart = new gviz_word_cumulus.WordCumulus(document.getElementById(id));
	chart.draw(data, options);
}