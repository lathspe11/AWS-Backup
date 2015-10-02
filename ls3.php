#!/usr/bin/php5
<?php

////////////////////////////////////////////////////////////////////////
//Requires an arg that is defined in the $volcontnr array
// Run after allstar.sh script 

//Test aws s3 commands 
require_once('/home/ubuntu/bin/AWS_include.php');
		
	if (isset($argv)){ 
		if (array_key_exists($argv[1], $volcontnr)){
			$key = $argv[1];
			$value = $volcontnr[$key];
			$curdate = date("Ymd-Hi");
			$s3fnam = $key . $curdate . ".tc";

		}else{ //Bad args
			print("$argv[1] not found.\nUse one of -");
			var_dump(array_keys($volcontnr));
			die("correct command arguments and retry\n");
		}
	}else{ //User did not invoke command correctly 
		print("no command args\n");
		die("correct command arguments and retry\n");
	}
	system("whoami");

 	
	//This helps the AWS command move the bits efficiently
	$contrsiz= $value * $oneGig;
	$s3bucket= $bucket2[$key];

	$dtstrg = date("Y-m-d H:i A");
	echo "Start Move to S3 at $dtstrg \n";
	
	echo "1 gig == $oneGig\n";
	
	$s3cmd = "nice -n 10 /usr/local/bin/aws s3 --region us-west-2 --profile backuppc ls s3://$s3bucket/ | grep $key";
	print "Executing::$s3cmd\n";
	system($s3cmd);

	$dtstrg = date('Y-m-d  H:i A');
	echo "End of move to S3 $dtstrg \n";

?>