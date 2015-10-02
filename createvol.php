#!/usr/bin/php5
<?php

//Define directory paths and standard container and file names
require_once('/home/ubuntu/bin/AWS_include.php');

///////////////////////////////////////////////////////////////////////////////////
// This function will Examine the tarballs to move and create volumes for all keyed 
// truecrypt containers sized to fit. 
// Run after allstar.sh script 
//
	$rndstr=$prgdir.'prand.php >'.$prandout;
	$output = `$rndstr`;
	//die($output);
	//echo "1 gig == $oneGig\n";
	foreach ($volcontnr as $key => $value) {
		$adirsiz = array();
		$sumsizes = $oneMeg; //Add for space
		//Compute container sizes 
		$dusiz = $bkupdir . $key . '*.tar.gz | cut -f1';      //What we are Monitoring
	 	$adirsiz = explode ("\n",trim(shell_exec("du -s $dusiz"))); //Get size in blocks into array

	 	foreach ($adirsiz as $duval) { //file sizes in blocks 
	 		$sumsizes += ($duval*$oneBlock);
	 		//echo "$duval $oneBlock\n";
	 	}
	 	$value = round((($sumsizes+($oneGig/2))+($fsfree*$sumsizes)) / $oneGig); //Convert size to Gig, round up
	 	$value += 1;
	 	$contrnam=$tcdir.$key."_".$value.".tc"; //Must build the name to use for container. (Content)_(Size).tc
		$contrsiz= $sumsizes; //$value * $oneGig;
	 	echo "$key $value - > $contrsiz\n";
	 	//echo "$contrsiz size container $contrnam\n";
	 
		$tccmd  = "truecrypt -t --create $contrnam --size=$contrsiz ";
		$tccmd .= "--keyfiles='$kyfile' --volume-type='normal' --filesystem='Linux Ext2' ";
		$tccmd .= "--protect-hidden=no --password='$vpw' ";
		$tccmd .= "--encryption='AES' --hash='SHA-512' --random-source=$prandout ";

		$dtstrg = date('Y-m-d  h:i:s A');
		echo "Start Create at $dtstrg \n$tccmd\n";

		`$tccmd`;
	}
	$dtstrg = date('Y-m-d  h:i:s A');
	echo "$dtstrg \n";
	`truecrypt -t -l`;
?>