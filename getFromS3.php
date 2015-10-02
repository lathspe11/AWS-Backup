#!/usr/bin/php5
<?php

////////////////////////////////////////////////////////////////////////
//This command will attempt to download the latest volume uploaded to S3. 
//Requires an arg that is defined in the $volcontnr array
// Run after allstar.sh script 

//Define directory paths and standard container and file names
//require_once('/home/ubuntu/bin/AWS_include.php');
require_once('/home/djwilli/Programs/AWS/AWS_include.php');
#require_once('/home/ubuntu/bin/AWS_include.php');

	if (isset($argv)){
		if (array_key_exists($argv[1], $volcontnr)){
			$key = $argv[1];
			$value = $volcontnr[$key];
			$s3bucket= $bucket2[$key];

			$curdate = date("Ymd-Hi");
			$awscmd = 'aws s3 --region us-west-2 ls s3://'.$s3bucket.'/ 2>/dev/null | grep "' . $key .'"';
		 	print "$awscmd\n";
		 	$checkstr=trim(shell_exec($awscmd)); //should result in all uploaded files matching $key
			

		 	//Assure that we found a file to download. 
			if (strlen($checkstr) == 0){ //Did not find a file for $key.  Output list of bucket
				print "$key container not located.\n";
				$awscmd = 'aws s3 --region us-west-2 ls s3://'.$bucket2[$key].'/ ' ;
			 	print "$awscmd\n";
			 	$checkstr=trim(shell_exec($awscmd)); //should result in the latest upload

				//All good Jump to cp from S3
			}else{
				print "Found $checkstr \n";
			}
		}else{
			print("$argv[1] not found.\nUse one of -");
			var_dump(array_keys($volcontnr));
			die("correct command arguments and retry\n");
		}
	}else{
		print("no command args\n");
		die("correct command arguments and retry\n");
	}

	//Bucket level list 
	$awscmd = 'aws s3 --region us-west-2 ls s3://'.$s3bucket.'/ | grep "'.$key.'" | tail -1 | cut -d " " -f4' ;
	print "$awscmd\n";
	$s3fnam=trim(shell_exec($awscmd)); //should result in the latest upload being downloaded 
 	
 	//For transfer efficiency the aws cmd wants to know the size of the file
	//$contrsiz is passed to AWS as a estimate for move size
	//This helps the AWS command move the bits efficiently
	$contrsiz= $value * $oneGig;
	$contrnam= './truecrypt/'.$key.'_'.$curdate.'.tc';
	// If ./trucrypt dir doesn't exist then create it
	if (!file_exists('./truecrypt/')){
		mkdir('./truecrypt');
	}
	$dtstrg = date("Y-m-d H:i A");
	echo "Start Download from S3 at $dtstrg \n";
	
	$s3cmd = "aws s3 --region us-west-2 cp s3://$s3bucket/$s3fnam $contrnam "; //--expected-size $contrsiz";
	print "Executing::$s3cmd\n";
	system($s3cmd);

	$dtstrg = date('Y-m-d  H:i A');
	echo "End of move to S3 $dtstrg \n";
	echo "Now use trucrypt to mount the volume $contrnam\n";
	echo "$dtstrg \n";

?>