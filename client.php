<?php
// web service client
	include("lib/nusoap.php");
	function sayHelloToPersonInvoke(){
		try {   
	       $client = new nusoap_client ( "http://127.0.0.1:8080/axis2/services/HelloServiceNew?wsdl");   
	       $client->soap_defencoding = "UTF-8";   
	           
	       $aryPara = array(
	       	"name" => "Hxuhao"
	       	);
	       $aryResult = $client->call ( "sayHelloToPerson", $aryPara, "http://service.hxuhao.com" );   
	       $document = $client->document;   
	       echo $document;   
	   	} catch ( SOAPFault $e ) {   
	           
	      	print $e;   
	    	}   
	}

	sayHelloToPersonInvoke();
?>