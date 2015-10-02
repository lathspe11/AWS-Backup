#!/usr/bin/php5
<?php

//######################################################################################
//This will mount the encrypted container files based on the secmented data partitions.
// Run after createvol.php script has created the container files

//Define directory paths and standard container and file names
require_once '/home/ubuntu/bin/AWS_include.php';

if (isset($argv)) {
	print $argv[0] . ' ' . $argv[1] . "\n";
	if (array_key_exists($argv[1], $volcontnr)) {
		$key = $argv[1];
		$curdate = date("Ymd-Hi");
		$adirsiz = array();
		$sumsizes = $oneMeg; //Add for space
		$tcMtPt = "/mnt/secure/$key";
		$contrnam = trim(shell_exec('ls ' . $tcdir . $key . '_*.tc'));
		if (file_exists($contrnam)) {
			//Encrypted Vol Exists
			$contrnam = trim(shell_exec('ls ' . $tcdir . $key . '_*.tc 2>/dev/null'));
			$contflag = true;
		} else {
			//Create the Encrypted Vol.
			print "Creating new Encrypted Volume\n";
			system($prgdir . "cnamedvol.php $key");
			$contrnam = trim(shell_exec('ls ' . $tcdir . $key . '_*.tc 2>/dev/null'));
			$contflag = false;
		}
		//$contrsiz= $value * $oneGig;
		//echo "$contrsiz size container $contrnam\n";
		$tcdev = "/dev/mapper/tc_" . $key;
		$tcMtPt = "/mnt/secure/$key";
		if (!file_exists($tcMtPt)) {
			echo 'Creating Mount Point $tcMtPt\n';
			`mkdir --parents $tcMtPt`;
		}
		//Used for full volume encryption
		//`mkfs.ext2 -t ext2 -v $tcdev`;
		//`mount $tcdev $tcMtPt`;
		$checkstr = trim(shell_exec('truecrypt -t -l 2>/dev/null| grep /' . $key));
		if (strlen($checkstr) == 0) {
			print "$key Not mounted\n";
			$nextLoop = trim(shell_exec('/sbin/losetup -f 2>/dev/null')); //Check if we can make a new container
			if (strncmp($nextLoop, "/dev/loop", 9) != 0) {
				//No next loop device. drop a tc container
				system('truecrypt -d');
			}
			//Create Virtual mountable device
			$tccmd = "truecrypt --keyfiles='$kyfile' ";
			$tccmd .= "--protect-hidden=no --password='$vpw' ";
			$tccmd .= " $contrnam /mnt/secure/" . $key;
			$dtstrg = date('Y-m-d  h:i:s A');
			$pccmd = "truecrypt --keyfiles='$kyfile' --filesystem='none' ";
			$pccmd .= "--protect-hidden=no --password='Sytem Password' "; //Let's not output PW to the log
			$pccmd .= " $contrnam /mnt/secure/" . $key;
			echo "$dtstrg \n$pccmd\n";
			system($tccmd);
			//Grab mountable device name
			$tcDevice = trim(shell_exec('truecrypt -t -l 2>/dev/null| grep /' . $key . ' | cut -d" " -f 3 '));
			if (strlen($tcDevice) == 0) {
				echo "ERROR: Trucrypt failed to create device.  Try dropping a mounted truecrypt container and rerun\n";
			} else {
				system("/sbin/mkfs.ext2  -t ext2 -v $tcDevice "); //Set FS on Volume
				echo "1: mount $tcDevice $tcMtPt \n";
				system("mount $tcDevice $tcMtPt "); //Mount the device
			}

		} else {
			//Mounted at some level
			$checkstr = trim(shell_exec('truecrypt -t -l 2>/dev/null| grep /' . $key . ' | cut -d" " -f 4 '));
			print "$key mounted as \n$checkstr \n";
			if (strcmp($checkstr, $tcMtPt) == 0) { //Volume attached at mount point?
				print "Mounted Vol at $checkstr , $tcMtPt \n"; //We should be good to copy the data
			} else {
				//Volume Not attached at mount point?
				print "Mount not complete\n";
				//Grab mountable device name
				$tcDevice = trim(shell_exec('truecrypt -t -l 2>/dev/null| grep /' . $key . ' | cut -d " " -f 3 '));
				//system("/sbin/mkfs.ext2  -t ext2 -v $tcDevice 2>/dev/null "); //Set FS on Volume
				echo "2: mount $tcDevice $tcMtPt \n";
				system("mount $tcDevice $tcMtPt "); //Mount the device
			}
			// Continue die ("test mount");
		}
	}
}
$dtstrg = date('Y-m-d  h:i:s A');
echo "End of mount.php $dtstrg \n";
`truecrypt -t -l`;
?>