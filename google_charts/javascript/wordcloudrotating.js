google.load('visualization', '1');

// Set callback to run when API is loaded
google.setOnLoadCallback(drawVisualization_$id);

// Called when the Visualization API is loaded.
function drawVisualization_$id() {
	
	// Create and populate a data table.
	
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
	
	// Instantiate our table object.
	
	var vis = new gviz_word_cumulus.WordCumulus(document.getElementById('$id'));
	
	// Draw our table with the data we created locally.
	
	vis.draw(data, {text_color: '#$color', hover_text_color: '#$hoverColor', speed: $speed, width: $width, height: $height});
}