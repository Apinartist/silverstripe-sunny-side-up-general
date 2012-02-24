<div id="MainContentSection">
	<div class="galleryIntro">
		<h1 id="PageTitle">$Title</h1>
		$Content
	</div>
	<div class="boxThoseGalleries" >
		<% if MyChildGalleries %>
			<ul id="Galleries">
				<% control MyChildGalleries	 %>
					<li class="$FirstLast $EvensOdd $LinkingMode">
						<% if ImageGalleryEntries %><% control ImageGalleryEntries.First %>$Image.CroppedImage(100, 100)<% end_control %><% end_if %>
						<div class="details">
							<h3>$Title</h3>
							<p>$Content.FirstSentence <a href="$Link">more</a></p>
						</div>
					</li>
				<% end_control %>
			</ul>
		<% end_if %>
	</div>
</div>
<% include FormSection %>
<% include PageCommentSection %>
