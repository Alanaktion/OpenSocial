<?php

function clean($str) {
	return trim(html_entity_decode(preg_replace('`<br(?: /)?>([\\n\\r])`','$1',$str),ENT_QUOTES,'UTF-8'));
}

function response($str) {
	if(is_numeric($str))
		header('',true,(int) $str);
	else switch($str) {
		// Success
		case 'ok':
			header(':',true,200);
			break;
		case 'created': // stored new data
			header(':',true,201);
			break;
		case 'accepted': // started asynchronous action
			header(':',true,202);
			break;
		case 'nocontent': // intentionally empty (rare)
			header(':',true,204);
			break;
		
		// No Content
		case 'seeother': // returned data considered optional
			header(':',true,303);
			break;
		case 'notmodified':
			header(':',true,304);
			break;
		// 301 (permanent) and 307 (temporary) redirect statii are declared automatically with Location headers.
		// IF INCLUDING A Location WITHOUT ISSUING A REDIRECT, RETURN IT BEFORE USING Response()
		
		// Errors
		case 'badrequest': // client's fault
			header(':',true,400);
			break;
		case 'unauthorized':
			header(':',true,401);
			break;
		case 'forbidden': // cannot access, regardless of credentials
			header(':',true,403);
			break;
		case 'notfound':
			header(':',true,404);
			break;
		case 'notacceptable': // unable to deliver in specified format/context
			header(':',true,406);
			break;
		case 'conflict': // cannot perform action because affected object cannot currently be modified (rare)
			header(':',true,409);
			break;
		case 'precondition': // client should take another action first (rare)
		case 'preconditionrequired':
			header(':',true,412);
			break;
		case 'unsupported': // client's request was not a usable type
		case 'unsupportedmedia':
		case 'unsupportedmediatype':
			header(':',true,416);
			break;
		case 'internal':
		case 'internalerror':
		case 'internalservererror': // server's fault, client should try again
			header(':',true,500);
			break;
	}
};

?>