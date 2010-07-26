google.load('visualization', '1', {packages:['table']});

google.setOnLoadCallback(draw_$id);

function draw_$id() {
	
	data = new google.visualization.DataTable();
	data.addColumn('string', 'Label');
	data.addColumn('number', 'Value');
	data.addColumn('string', 'Link');
	
	var words = [$words];
	var fontsizes = [$fontsizes];
	data.addRows(words.length);
	for(var i = 0; i < words.length; i++) {
		data.setCell(i, 0, words[i]);
		data.setCell(i, 1, fontsizes[i]);
		data.setCell(i, 2, '');
	}
	
	var outputDiv = document.getElementById('$id');
	var tc = new TermCloud(outputDiv);
	tc.draw(data, null);
}