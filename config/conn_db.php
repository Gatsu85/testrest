<?php

function connect(){
	$db_host = "127.0.0.1";
	$db_user = "root";
	$db_password = "";
	$db_database = "testrest_db";
	
	$___mysqli = new mysqli($db_host, $db_user, $db_password, $db_database);
	
	if ($___mysqli->connect_error) {
		die('Errore di connessione (' . $___mysqli->connect_errno . ') '. $___mysqli->connect_error);
	} else {
		//die ('Connesso. ' . $___mysqli->host_info);
		return $___mysqli;
	}
}
?>