#!/usr/bin/php5
<?php

////////////////////////////////////////////////////////////////////////
//Requires an arg that is defined in the $volcontnr array
// Run after allstar.sh script 

//Define directory paths and standard container and file names
require_once('/home/ubuntu/bin/AWS_include.php');
		
	if (isset($argv)){ 
		if (array_key_exists($argv[1], $volcontnr)){
			$key = $argv[1];
			$value = $volcontnr[$key];
			$curdate = date("Ymd-Hi");
			$s3fnam = $key . $curdate . ".tc";
			print "$s3fnam\n";
		 	$contrnam=trim(shell_exec('ls '. $tcdir.$key.'_*.tc'));

		 	//Assure that Volume is Dismounted before moving it 
			$checkstr = system('truecrypt -t -l 2>/dev/null| grep /'.$argv[1]);
			if (strlen($checkstr) == 0){
				print "$argv[1] Not mounted\n";
				//All good Jump to cp 2 S3
			}else{
				print "$argv[1] mounted as \n$checkstr \n";
				//Dismount the Container 
				$tccmd  = "truecrypt -d ";
				//$tccmd .= "--keyfiles='$kyfile' --filesystem='none' --protect-hidden=no --password='$vpw' ";
				$tccmd .= $contrnam;

				$dtstrg = date('Y-m-d  h:i:s A');
				echo "Dismounting Container \n$dtstrg \n$tccmd\n";			
				`$tccmd`;
			}
		}else{ //Bad args
			print("$argv[1] not found.\nUse one of -");
			var_dump(array_keys($volcontnr));
			die("correct command arguments and retry\n");
		}
	}else{ //User did not invoke command correctly 
		print("no command args\n");
		die("correct command arguments and retry\n");
	}

 	$contrnam=trim(shell_exec('ls '. $tcdir.$key.'_*.tc'));

 	//For transfer efficiency the aws cmd wants to know the size of the file
	$adirsiz = array();
	$sumsizes = $oneMeg; //Add for space
	//Compute container sizes 
	$dusiz = $bkupdir . $key . '*.tar.gz | cut -f1';      //What we are Monitoring
	$adirsiz = explode ("\n",shell_exec("du -s $dusiz")); //Get size in blocks into array

	foreach ($adirsiz as $duval) {
		$sumsizes += ($duval*$oneBlock);
	}
	$value = round((($sumsizes+($oneGig/2)) / $oneGig)); //Compute size to Gig, round up
	//$contrsiz is passed to AWS as a estimate for move size
	//This helps the AWS command move the bits efficiently
	$contrsiz= $value * $oneGig;
	$s3bucket= $bucket2[$key];

	$dtstrg = date("Y-m-d H:i A");
	echo "Start Move to S3 at $dtstrg \n";
	
	echo "1 gig == $oneGig\n";
	
	if (file_exists($contrnam)){ //Container exists 
		$s3cmd = "nice -n 10 /usr/local/bin/aws s3 --region us-west-2 --profile backuppc cp $contrnam s3://$s3bucket/$s3fnam --expected-size $contrsiz";
		print "Executing::$s3cmd\n";
		system($s3cmd);

		$dtstrg = date('Y-m-d  H:i A');
		echo "End of move to S3 $dtstrg \n";

	}else {
		print "$contrnam not found\n";
	}

	echo "Show truecrypt status \n";
	system('truecrypt -t -l');

?>