<div class="typography">
	<% if Menu(2) %>
		<% include SideBar %>
		<div id="Content">
	<% end_if %>
			
	<% if Level(2) %>
	  	<% include BreadCrumbs %>
	<% end_if %>
	
		<h1>$Title</h1>
			
		$Content

		<div id="GalleryWrapper">
			<% include GalleryPageContent %>
		</div>

		<p class="gallery-note">The <a href="http://doc.silverstripe.com/doku.php?id=modules:gallery">Gallery module</a> uses the <a href="http://stickmanlabs.com/lightwindow">Lightwindow 2.0</a> to show and read these media files.</p>

		$Form
		$PageComments
	
	<% if Menu(2) %>
		</div>
	<% end_if %>
</div>

	
	
