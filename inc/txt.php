<?php

function sms($num,$cli,$msg) {
	$sms_cli = array(
	  "at" => "txt.att.net",            // AT&T
	  "nx" => "messaging.nextel.com",   // Nextel
	  "sp" => "messaging.sprintpcs.com",// Sprint
	  "tm" => "tmomail.net",            // T-Mobile
	  "vz" => "vtext.com",              // Verizon
	  "vm" => "vmobl.com",              // Virgin Mobile
	  "gv" => "txt.voice.google.com"    // Google Voice [USE VOICE API]
	);
	
	if($cli=="gv" && false) { // Google Voice disabled, remove `false` if you add user details below.
		require_once('sms/class.xhttp.php');

$data = array();$data['post'] = array('accountType'=>'GOOGLE',
'Email'       => 'GOOGLE_VOICE_USERNAME',
'Passwd'      => 'GOOGLE_VOICE_PASSWORD',
'service'=>'grandcentral','source'=>'xusix.com-smssender-0.2');$response = xhttp::fetch('https://www.google.com/accounts/ClientLogin', $data);preg_match('/Auth=(.+)/', $response['body'], $matches);$auth = $matches[1];$data['post'] = null;$data['headers'] = array('Authorization' => 'GoogleLogin auth='.$auth);$response = xhttp::fetch('https://www.google.com/voice/b/0', $data);if(!$response['successful']) return false;preg_match("/'_rnr_se': '([^']+)'/", $response['body'], $matches);$rnrse = $matches[1];

		$data['post'] = array (
			'_rnr_se'     => $rnrse,
			'phoneNumber' => $num, // country code + area code + phone number (international notation)
			'text'        => $msg,
			'id'          => ''
		);
		$response = xhttp::fetch('https://www.google.com/voice/sms/send/',$data);
		$value = json_decode($response['body']);
		return $value->ok;
	} else {
		return mail($num."@".$sms_cli[$cli],"Xusix",$msg,"From: sms@xusix.com");
	}
}

?>