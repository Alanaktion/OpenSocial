<?php
	require 'pop3/mime_parser.php';
	require 'pop3/rfc822_addresses.php';
	require 'pop3/pop3.php';

	if(!$inc) chdir('../');
	require_once 'config.php';

	stream_wrapper_register('pop3','pop3_stream');  // Register the pop3 stream handler class

	$user=UrlEncode("xmail@alanaktion.net");
	$password=UrlEncode("6wE4d1w1ed");
	$realm=UrlEncode("");                         // Authentication realm or domain
	$workstation=UrlEncode("");                   // Workstation for NTLM authentication
	$apop=0;                                      // Use APOP authentication
	$authentication_mechanism=UrlEncode("USER");  // SASL authentication mechanism
	$debug=0;                                     // Output debug information
	$html_debug=1;                                // Debug information is in HTML
	$message=1;
	/*$message_file='pop3://'.$user.':'.$password.'@localhost/'.$message.
	'?debug='.$debug.'&html_debug='.$html_debug.'&realm='.$realm.'&workstation='.$workstation.
	'&apop='.$apop.'&authentication_mechanism='.$authentication_mechanism;*/
	$message_file='pop3://'.$user.':'.$password.'@pop.gmail.com:995/1?tls=1&debug='.$debug.'&html_debug='.$html_debug;

	$mime=new mime_parser_class;

	//Set to 0 for not decoding the message bodies
	$mime->decode_bodies = 1;

	$parameters=array(
		'File'=>$message_file,
		// 'Data'=>'My message data string', // Read a message from a string instead of a file
		'SkipBody'=>0
	);

	while($mime->Decode($parameters, $decoded)) {
		$mime->Analyze($decoded[0],$results);
		foreach($results['To'] as $to) {
			mysql_query("INSERT INTO messages VALUES ('".x::id()."','{$results['From'][0]['name']} <{$results['From'][0]['address']}>','{$to['address']}','".addslashes($results['Subject'])."','".addslashes(trim($results['Alternative'][0]['Data']))."','".addslashes(trim($results['Data']))."','".strtotime($results['Date'])."','0')");
		}
	}

	@mysql_close();
?>