jQuery(document).ready(

	function() {
		jQuery("#Form_PageRatingForm_Rating").stars();
			$("#Form_PageRatingForm_Rating").children().not(":radio").hide();
			$("#Form_PageRatingForm_Rating").stars({
				callback: function(ui, type, value)
				{
					$.post("demo1.php", {rate: value}, function(data)
					{
						$("#Form_PageRatingForm_Rating").html(data);
					});
				}
			});
	}

);
