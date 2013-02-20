<?php
	$source = 'uc/img/'.$_GET['id'].$_GET['ext'];
	
	// Output image stream and end processing
	function showimg($r) {
		$expires = 3600*24*30; // 30 days
		header("Pragma: public");
		header("Cache-Control: maxage=".$expires);
		header('Expires: '.gmdate('D, d M Y H:i:s',time()+$expires).' GMT');
		
		header('Content-Type: image/jpeg');
		imagejpeg($r);
		exit();
	}
	
	// Issue a 301 Permanent Redirect to the original image
	function redirorig() {
		global $source;
		
		if(isset($_SERVER['HTTPS']) && 'on' === $_SERVER['HTTPS'])
		    $location = 'https://';
		else
		    $location = 'http://';
		
		$location .= $_SERVER['SERVER_NAME'].'/'.$source;
		
		header('Location: '.$location, false, 301);
		exit();
	}
	
	// Source Image Unavailable
	if(!$_GET['id'] || !$_GET['ext'] || !is_file($source)) {
		header('Content-Type: image/png');
		readfile('uc/img/error96.png');
		die();
	}
	
	// Load Source Image
	$img = imagecreatefromstring(file_get_contents($source));
	
	// No sizing data, output full JPEG
	if(!$_GET['w'] && !$_GET['h'])
		showimg($img);
	
	// Only one dimension given, resize using given dimension
	elseif($_GET['w'] && !$_GET['h']) {
		if($_GET['w']>=imagesx($img))
			redirorig();
		else {
			$resized = imagecreatetruecolor($_GET['w'],$_GET['w']/imagesx($img)*imagesy($img));
			imagecopyresampled($resized,$img,0,0,0,0,imagesx($resized),imagesy($resized),imagesx($img),imagesy($img));
		}
	} elseif($_GET['h'] && !$_GET['w']) {
		if($_GET['h']>=imagesy($img))
			redirorig();
		else {
			$resized = imagecreatetruecolor($_GET['h']/imagesy($img)*imagesx($img),$_GET['h']);
			imagecopyresampled($resized,$img,0,0,0,0,imagesx($resized),imagesy($resized),imagesx($img),imagesy($img));
		}
	}
	
	// Both dimensions given, crop and resize
	elseif($_GET['w'] && $_GET['h'] && !$_GET['s']) {
		if($_GET['w']>=imagesx($img) && $_GET['h']>=imagesy($img))
			redirorig();
		if($_GET['w']>=imagesx($img))
			$_GET['w'] = imagesx($img);
		if($_GET['h']>=imagesy($img))
			$_GET['h'] = imagesy($img);
		$resized = imagecreatetruecolor($_GET['w'],$_GET['h']);
		imagecopyresampled($resized,$img,0,0,(imagesx($img)/2 - $_GET['w']/2),(imagesy($img)/2 - $_GET['h']/2),$_GET['w'],$_GET['h'],$_GET['w'],$_GET['h']);
	}
	
	// Both dimensions given, stretch to fit (s=1)
	elseif($_GET['w'] && $_GET['h'] && $_GET['s']) {
		$resized = imagecreatetruecolor($_GET['w'],$_GET['h']);
		imagecopyresampled($resized,$img,0,0,0,0,$_GET['w'],$_GET['h'],imagesy($img),imagesy($img));
	}
	
	// Output Resized Image
	showimg($resized);

?>