google.load('visualization', '1');

google.setOnLoadCallback(drawChart_$id);

function drawChart_$id() {
	
	var data = new google.visualization.DataTable();
	
	data.addColumn('string', 'Tag');
	data.addColumn('string', 'URL');
	data.addColumn('number', 'Font size');
	
	var words = [$words];
	var fontsizes = [$fontsizes];
	
	data.addRows(words.length);
	
	for(var i = 0; i < words.length; i++) {
		data.setCell(i, 0, words[i]);
		data.setCell(i, 1, '');
		data.setCell(i, 2, fontsizes[i]);
	}
	
	var chart = new gviz_word_cumulus.WordCumulus(document.getElementById('$id'));
	chart.draw(data, {$options});
}