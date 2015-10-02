#!/usr/bin/php5
<?php

//######################################################################################
//This will mount the encrypted container files based on the segmented data partitions.
// Run after createvol.php script has created the container files
	
	
//Define directory paths and standard container and file names
require_once('/home/ubuntu/bin/AWS_include.php');

	foreach ($volcontnr as $key => $value) {
	 	$contrnam=trim(shell_exec('ls '. $tcdir.$key.'_*.tc'));

		//$contrsiz= $value * $oneGig;
	 	//echo "$contrsiz size container $contrnam\n";
		$tcdev  = "/dev/mapper/tc_".$key;
		$tcMtPt = "/mnt/secure/$key";
		if (!file_exists($tcMtPt)){
			`mkdir --parents $tcMtPt`;
		}
		//Used for full volume encryption
		//`mkfs.ext2 -t ext2 -v $tcdev`;
	 	//`mount $tcdev $tcMtPt`;
		$checkstr = trim(shell_exec('truecrypt -t -l 2>/dev/null| grep /'.$key));
		if (strlen($checkstr) == 0){
			print "$key Not mounted\n";
			//Create Virtual mountable device 
			$tccmd  = "truecrypt --keyfiles='$kyfile' --filesystem='none' ";
			$tccmd .= "--protect-hidden=no --password='$vpw' ";
			$tccmd .= " $contrnam /mnt/secure/".$key;
			$dtstrg = date('Y-m-d  h:i:s A');
			echo "$dtstrg \n$tccmd\n";
			system($tccmd);
			//Grab mountable device name
			$tcDevice = trim(shell_exec('truecrypt -t -l 2>/dev/null| grep /'.$key . ' | cut -d " " -f 3 '));
			//system("/sbin/mkfs.ext2  -t ext2 -v $tcDevice >/dev/null "); //Set FS on Volume
			system("mount $tcDevice $tcMtPt "); //Mount the device
			
		}else{ //Mounted at some level
			print "$key mounted as \n$checkstr \n";
			$checkstr = trim(shell_exec('truecrypt -t -l 2>/dev/null| grep /'.$key . ' | cut -d " " -f 4 '));
			if (strcmp($checkstr,$tcMtPt) == 0){ //Volume attached at mount point? 
				print "Mounted Vol at $checkstr , $tcMtPt \n"; //We should be good to copy the data 
			}else { //Volume Not attached at mount point? 
				print "Mount not complete\n";
				//Grab mountable device name
				$tcDevice = trim(shell_exec('truecrypt -t -l 2>/dev/null| grep /'.$key . ' | cut -d " " -f 3 '));
				//system("/sbin/mkfs.ext2  -t ext2 -v $tcDevice >/dev/null "); //Set FS on Volume
				system("mount $tcDevice $tcMtPt "); //Mount the device
			}
			// Continue die ("test mount");
		}
	}
	$dtstrg = date('Y-m-d  h:i:s A');
	echo "End of mount.php $dtstrg \n";
	`truecrypt -t -l`;
?>