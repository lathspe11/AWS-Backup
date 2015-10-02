#!/usr/bin/php5
<?php

//######################################################################################
//This will copy the tar balls created from backuppc to the proper encrypted container.
// Run after allstar.sh / createvol.php / mount.php script

//Define directory paths and standard container and file names
require_once '/home/ubuntu/bin/AWS_include.php';

if (isset($argv)) {
	//Check that we call the command with corect args.
	if (array_key_exists($argv[1], $volcontnr)) {
		//key passed in exists
		$key = $argv[1];
		$curdate = date("Ymd-Hi");
		$adirsiz = array();
		$sumsizes = $oneMeg; //Add for space
		$tcMtPt = "/mnt/secure/$key";
		$tcnam = trim(shell_exec('ls ' . $tcdir . $key . '_*.tc'));
		if (file_exists($tcnam)) {
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
	} else {
		//Not a correct key
		print("$argv[1] not found.\nUse one of -");
		var_dump(array_keys($volcontnr));
		die("correct command arguments and retry\n");
	}
	//We have a container
	if (file_exists($tcMtPt)) {
		//Mount Point Exists
		$checkstr = trim(shell_exec('truecrypt -t -l 2>/dev/null| grep /' . $key));
		if (strlen($checkstr) == 0) {
			//Container not mounted
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
			$pccmd = "truecrypt --keyfiles='$kyfile' ";
			$pccmd .= "--protect-hidden=no --password='Sytem Password' "; //Let's not output PW to the log
			$pccmd .= " $contrnam /mnt/secure/" . $key;
			$dtstrg = date('Y-m-d  h:i:s A');
			echo "$dtstrg \n$pccmd\n";
			system($tccmd);
			//Grab mountable device name
			$tcDevice = trim(shell_exec('truecrypt -t -l 2>/dev/null| grep /' . $key . ' | cut -d " " -f 3 '));
			echo "Mount $tcDevice $tcMtPt \n";
			if (strlen($tcDevice) == 0) {
				echo "ERROR: Trucrypt failed to create device.  Try dropping a mounted truecrypt container and rerun\n";
			} else {
				if ($contflag == true) {
					echo "container should already have fs \n";
				} else {
					system("/sbin/mkfs.ext2  -t ext2 -v $tcDevice >/dev/null "); //Set FS on Volume
				}
				system("mount $tcDevice $tcMtPt "); //Mount the device
			}
		} else {
			//Mounted at some level
			$checkstr = trim(shell_exec('truecrypt -t -l 2>/dev/null| grep /' . $key . ' | cut -d " " -f 4 ')); //Mount point Cut
			print "$key mounted as \n$checkstr \n";
			if (strcmp($checkstr, $tcMtPt) == 0) { //Volume attached at mount point?
				print "Mounted Vol at $checkstr , $tcMtPt \n"; //We should be good to copy the data
			} else {
				//Volume Not attached at mount point?
				print "$checkstr Mount not complete at $tcMtPt\n";
				//Grab mountable device name
				$tcDevice = trim(shell_exec('truecrypt -t -l 2>/dev/null| grep /' . $key . ' | cut -d " " -f 3 '));
				echo "Mount $tcDevice $tcMtPt \n";
				//system("/sbin/mkfs.ext2  -t ext2 -v $tcDevice >/dev/null "); //Set FS on Volume will overwite
				system("mount $tcDevice $tcMtPt "); //Mount the device
			}
			// Continue die ("test mount");
		}
	} else {
		//Need to create the Mount Point
		`mkdir --parents $tcMtPt`;
		$nextLoop = trim(shell_exec('/sbin/losetup -f 2>/dev/null')); //Check if we can make a new container
		if (strncmp($nextLoop, "/dev/loop", 9) != 0) {
			//No next loop device. drop a tc container
			system('truecrypt -d');
		}
		//Create Virtual Mountable Device
		$tccmd = "truecrypt --keyfiles='$kyfile' --filesystem='none' ";
		$tccmd .= "--protect-hidden=no --password='$vpw' ";
		$tccmd .= " $contrnam /mnt/secure/" . $key;
		$dtstrg = date('Y-m-d  h:i:s A');
		$pccmd = "truecrypt --keyfiles='$kyfile' --filesystem='none' ";
		$pccmd .= "--protect-hidden=no --password='Sytem Password' "; //Let's not output PW to the log
		$pccmd .= " $contrnam /mnt/secure/" . $key;
		echo "$dtstrg \n$pccmd\n";
		system($tccmd);
		//Grab mountable device name
		$tcDevice = trim(shell_exec('truecrypt -t -l 2>/dev/null| grep /' . $key . ' | cut -d " " -f 3 '));
		if (strlen($tcDevice) == 0) {
			echo "ERROR: Trucrypt failed to create device.  Try dropping a mounted truecrypt container and rerun\n";
		} else {
			if ($contflag == true) {
				echo "container should already have fs \n";
			} else {
				system("/sbin/mkfs.ext2  -t ext2 -v $tcDevice >/dev/null "); //Set FS on Volume
			}
			system("mount $tcDevice $tcMtPt "); //Mount the device
		}
	}
	//Used for full volume encryption
	// We can now move the tarballs created by allstar to the correct encrypted container file

	$tccmd = "/bin/cp --remove-destination " . $bkupdir . $key . '*.tar.gz /mnt/secure/' . $key . '/.';

	$dtstrg = date('Y-m-d  h:i:s A');
	echo "$dtstrg \n$tccmd\n";

	`$tccmd`;
}
$dtstrg = date('Y-m-d  h:i:s A');
echo "$dtstrg \n";
`truecrypt -t -l`;
?>