		<h1 class="frmsearch">Photo Upload</h1>
		<p>No valid image files were uploaded.</p>
		<p><form action="<?=$a_home.$pg[0]?>/photos/upload" method="post" enctype="multipart/form-data" id="upfrm">
			<input type="hidden" name="js" id="jsinput" value="0">
			Upload Photos: <input name="uploads[]" type="file" multiple="multiple">
			<span id="upmsg" style="display:none">Uploading&hellip;</span>
			<input type="submit" value="Upload">
		</form></p>
		<p><a href="<?=$a_home.$pg[0]?>/photos">Return to My Photos</a></p>