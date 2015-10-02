#!/usr/bin/php5
<?php
	function generateRandomString($length = 400) {
	    $characters = "0123456789abcdefghijklmnopqrstuvwxyz !~`@#$%^&*()_+-=';:><ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	    $clen = strlen($characters); //echo "clen = $clen\n";
	    $randstring = '';
	    for ($i = 0; $i < $length; $i++) {
	    	$dx = rand(0, $clen-1);
	        $randstring .= $characters[$dx];
	    }
	    return $randstring;
	}

	print generateRandomString()."\n";

?>