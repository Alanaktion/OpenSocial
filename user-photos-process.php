<script type="text/javascript">
var imgs = [<?php
	$numimgs = count($img);
	for($i=0;$i<$numimgs;$i++) {
		echo '[\''.$img[$i].'\',\''.$ext[$i].'\']';
		if($i < ($numimgs - 1)) echo ',';
	}
?>];
var dims = [28,32,64,96,128];

window.onbeforeunload = function() {return 'Your photos are still being processed.';}

$(function(){
	$('#frmsubmit').attr('disabled','disabled');
	$('div.comment').hide();
	processImage(0,0);
});

function processImage(i,d) {
	$.post('/ajax.php',{
		req: 'processimage',
		img: imgs[i][0],
		ext: imgs[i][1],
		dim: dims[d]
	},function(data){
		console.log(data);
		$('#prog-'+i).css('width',((d+1)/dims.length*100)+'%');
		if(d < dims.length - 1) {
			// Process current image at next size
			processImage(i,d+1);
		} else {
			if(i < imgs.length - 1) {
				// This image is complete, hide progress bar
				$('#pbar-'+i).hide();
				$('img[alt=img-'+i+']').attr('src','<?=$a_home?>uc/img/'+imgs[i][0]+'128'+imgs[i][1]);
				$('div[rel=c-'+i+']').show();
				processImage(i+1,0);
			} else { // All images complete, enable submit button
				$('#pbar-'+i).hide();
				$('img[alt=img-'+i+']').attr('src','<?=$a_home?>uc/img/'+imgs[i][0]+'128'+imgs[i][1]);
				$('div[rel=c-'+i+']').show();
				$('#frmsubmit').removeAttr('disabled');
				$('#prog-msg').text('Images processed, add captions if desired and click Publish.');
				window.onbeforeunload = undefined;
			}
		}
	});
}
</script>
<h1 class="frmsearch">Processing Photos</h1>
<p id="prog-msg">Please wait while we process your upload&hellip;</p>
<form action="<?=$a_home.$pg[0]?>/photos/publish" method="post">
<div class="gallery-editor">
<?php
		$numimgs = count($img);
		for($i=0;$i<$numimgs;$i++) {
			echo '<div class="item">';
			echo '<img src="/img/pixel.gif" class="photo" alt="img-'.$i.'">';
			echo '<div class="pbar" id="pbar-'.$i.'"><b id="prog-'.$i.'"></b></div>';
			echo '<div class="comment" rel="c-'.$i.'">';
			echo '<input type="text" name="'.$img[$i].'" placeholder="Write a caption&hellip;">';
			echo '</div></div>';
		}
?>
	<div class="clearfloat"></div>
</div>
	<input type="submit" value="Publish" id="frmsubmit">
</form>