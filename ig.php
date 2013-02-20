<?php
// Xusix Image Generator
// Used to generate images and icons styled to fit the user's theme
// Also loads dynamically sized photos on-demand

header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + 3600 * 12)); // 12 hour cache

switch($pg[1]) {
	case 'user':
		header('Expires: Tue, 03 Jul 2001 06:00:00 GMT');
		header('Last-Modified: '.gmdate("D, d M Y H:i:s").' GMT');
		header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
		header('Cache-Control: post-check=0, pre-check=0', false);
		header('Pragma: no-cache');
		$pid = mysql_fetch_assoc(mysql_query("SELECT picture FROM mbasic WHERE user = '{$pg[2]}'"));
		if(!$pg[3]) $pg[3] = '96';
		if($pid['picture']) {
			@header('Content-type: image/jpg');
			echo file_get_contents('uc/img/'.$pid['picture'].$pg[3].'.jpg');
		} else {
			@header('Content-type: image/png');
			echo file_get_contents('img/profile'.$pg[3].'.png');
		}
		break;
	case 'plusp':
		if($pg[2]) $t_fore = explode('-',$pg[2]);
		if($pg[3]) $t_fill = explode('-',$pg[3]);
		$img = imagecreatetruecolor(8,8);
		if($pg[2])
			$fore = imagecolorallocate($img, $t_fore[0], $t_fore[1], $t_fore[2]);
		else
			$fore = imagecolorallocate($img, 120, 0, 0);
		if($pg[3])
			$fill = imagecolorallocate($img, $t_fill[0], $t_fill[1], $t_fill[2]);
		else
			$fill = imagecolorallocate($img, 255, 255, 255);
		$back = imagecolorallocate($img, 0, 0, 0);
		imagecolortransparent($img, $back);
		imagefilledrectangle($img,0,2,6,4,$fore);
		imagefilledrectangle($img,2,0,4,6,$fore);
		imageline($img,1,3,5,3,$fill);
		imageline($img,3,1,3,5,$fill);
		
		header("Content-type: image/gif");
		imagegif($img);
		imagedestroy($img);
		
		break;
}
?>