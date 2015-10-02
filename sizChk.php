#!/usr/bin/php5
<?php

//Define directory paths and standard container and file names
require_once('/home/ubuntu/bin/AWS_include.php');

///////////////////////////////////////////////////////////////////////////////////
// This function will Examine the tarballs to move and create volumes for truecrypt 
// sized to fit. 
// Run after allstar.sh script 
//
function sortByOrder($a, $b) {
    return $a['order'] - $b['order'];
}

	$rndstr=$prgdir.'prand.php >'.$prandout;
	$output = `$rndstr`;
	//die($output);
	$toparray = array();
	$bdirsiz  = array();
	
	echo "List of current truecrypt containers\n";
	system("ls -1 $tcdir*.tc | cut -d'/' -f5 ");

	$cmpcmd="ls -1 $tcdir*.tc  | cut -d'/' -f5 | cut -d'.' -f1 | cut -d'_' --output-delimiter=' ' -f '1 2'";
	//" | awk -F' ' '{print $2\" \"$1}'";
	$cdirsiz = explode ("\n", trim(shell_exec($cmpcmd))); //Get size in blocks into array
	echo "\n";
	//echo "$cmpcmd\n";
	foreach ($cdirsiz as $line) { //Collect the containers and sizes that exist
		$row = array();
		$row = explode(" ",$line);
		$bdirsiz[$row[0]] = $row[1];
	}
	//var_dump($bdirsiz);
	foreach ($volcontnr as $key => $value) { //For each known Container
		$curdate = date("Ymd-Hi");
		$adirsiz = array();
		$sumsizes = $oneMeg; //Add for space
		$myarray = array();

		//Compute container sizes 
		$dusiz = $bkupdir . $key . '*.tar.gz | cut -f1';      //What we are Monitoring
	 	$adirsiz = explode ("\n", trim(shell_exec("du -s $dusiz"))); //Get size in blocks into array
	 	foreach ($adirsiz as $duval) { //file sizes in blocks 
	 		$sumsizes += ($duval*$oneBlock);
	 		//echo "$duval $oneBlock\n";
	 	}
	 	$contrnam=$tcdir.$key."_".$value.".tc"; //Must build the name to use for container. (Content)_(Size).tc
	 	$value = round(($sumsizes+($oneGig/2)+($fsfree*$sumsizes)) / $oneGig); //Convert size to Gig, round up
	 	$value += 1;
		$contrsiz=$value * $oneGig; //$value * $oneGig;
		if (array_key_exists($key,$bdirsiz)){
	 		if ($bdirsiz[$key] == $value) {
	 			//print "Current Size Matches estimate.";
			 	$myarray= array("key"=>$key,"order"=>$value,"size"=>$contrsiz);
				$toparray [] = $myarray;
			 	//echo "$key $value - > $contrsiz\n";
	 		}else {
	 			print "****Sizes do not Match. drop ".$key."_tc and rebuild\n";
	 			print "try->truecrypt -d $contrnam";
	 			//system ("truecrypt -d $contrnam");
	 			//system("rm -f $contrnam");
	 			print "****$key is ${bdirsiz[$key]} should be $value for $contrsiz\n";
	 		}
	 	}else {
	 			print "****No Container for the Tarballs.  Create volume ".$key."_tc and rebuild\n";
	 	}
	 	
		//$contrnam=trim(shell_exec('ls '. $tcdir.$key.'_*.tc'));
	}
	print "Size Sorted Array\n";
	usort($toparray, 'sortByOrder');
	foreach ($toparray as $arow) {
		print "    ${arow['order']} ${arow['key']} \n";
	}

?>