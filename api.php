<?php
require 'api-functions.php';
header('Content-type: application/json; charset=UTF-8');


// Exit on maintenance mode
if($_GET['maintenance']) {
	response(500);
	echo json_encode(
		array(
			'error' => 'api unavailable'
		)
	);
	die();
}

response(500);
echo json_encode(
	array(
		'error' => 'api unavailable'
	)
);

?>