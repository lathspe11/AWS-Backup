#!/usr/bin/php5
<?php

////////////////////////////////////////////////////////////////////////
// This was used to split buckets out of the wmibackups bucket to defined bucket for the tarball
//Requires an arg that is defined in the $volcontnr array

//Define directory paths and standard container and file names
require_once('/home/ubuntu/bin/AWS_include.php');
		
	if (isset($argv)){
		if (array_key_exists($argv[1], $volcontnr)){
			$key = $argv[1];
			$value = $volcontnr[$key];
			$curdate = date("Ym");
			$s3fnam = $key . $curdate . "*.tc";
			print "$s3fnam\n";
		 	$contrnam=trim(shell_exec('ls '. $tcdir.$key.'_*.tc'));
			//$contrnam=$tcdir.$key."_".$value.".tc";

			$checkstr = system('truecrypt -t -l 2>/dev/null| grep /'.$argv[1]);
			if (strlen($checkstr) == 0){
				print "$argv[1] Not mounted\n";
			}else{
				print "$argv[1] mounted as \n$checkstr \n";
				//unmount container
				$tccmd  = "truecrypt -d --keyfiles='$kyfile' --filesystem='none' ";
				$tccmd .= "--protect-hidden=no --password='$vpw' ";
				$tccmd .= $contrnam;
				echo "$tccmd\n";
				system($tccmd);
			}
		}else{
			print("$argv[1] not found.\nUse one of -");
			var_dump(array_keys($volcontnr));
			die("correct command arguments and retry\n");
		}
	}else{
		print("no args\n");
		die("correct command arguments and retry\n");
	}

 	$contrnam=shell_exec('ls '. $tcdir.$key.'_*.tc');
	//$contrnam=$tcdir.$key."_".$value.".tc";
	$s3bucket= $bucket2[$key];

	$dtstrg = date("Y-m-d H:i A");
	echo "Start Move to S3 at $dtstrg \n";
	
	echo "1 gig == $oneGig\n";
	
	if (file_exists($contrnam)){ //Container exists 
		$s3cmd = "/usr/local/bin/aws s3 --region us-west-2 --profile backuppc mv s3://wmibackups/  s3://$s3bucket/ --include '$key'";
		print "$s3cmd\n";
		system($s3cmd);

		$dtstrg = date('Y-m-d  H:i A');
		echo "End of move to S3 $dtstrg \n";

	}

	$dtstrg = date('Y-m-d  h:i:s A');
	echo "$dtstrg \n";
	system('truecrypt -t -l');

?>