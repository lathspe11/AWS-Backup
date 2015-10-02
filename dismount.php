#!/usr/bin/php5
<?php
	
//Define directory paths and standard container and file names
require_once('/home/ubuntu/bin/AWS_include.php');

/////////////////////////////////////////////////////////////////////////////
// This script will dismount all the truecrypt volumes from /mnt/secure
//

 	foreach ($volcontnr as $key => $value) {
	 	$contrnam=trim(shell_exec('ls '. $tcdir.$key.'_*.tc'));
		$tcMtPt = "/mnt/secure/$key";

		if (!file_exists($tcMtPt)){
			`mkdir --parents $tcMtPt`;
		}else{

			$tccmd  = "truecrypt -d --keyfiles='$kyfile' --filesystem='none' ";
			$tccmd .= "--protect-hidden=no --password='$vpw' ";
			$tccmd .= $contrnam;

			$dtstrg = date('Y-m-d  h:i:s A');
			echo "$dtstrg \n$tccmd\n";			

			`$tccmd`;
		}
	}
	$dtstrg = date('Y-m-d  h:i:s A');
	echo "$dtstrg \n";
	`truecrypt -t -l`;
?>