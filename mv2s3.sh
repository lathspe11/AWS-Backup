#!/bin/bash

#Dismount the device 
#run as root


s3fnam="wmi$(/bin/date +%Y%m%d).tc";

#echo $s3fnam;

/usr/local/bin/aws s3 --region us-west-2 --profile backuppc cp /home/ubuntu/truecrypt/wmi.tc s3://wmibackups/$s3fnam;
