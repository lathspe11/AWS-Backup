#!/bin/bash

#Dismount the device 
#run as root


s3fnam="wmi$(/bin/date +%Y%m%d).tc";

rndstr=`~/Programs/prand.php`;

#echo $s3fnam;
#mystring="/home/ubuntu/truecrypt/lamp.tc
mystring="lamp.tc\n$rndstr";
#mystring="$mystring$rndstr";

echo $mystring;
#exit;

truecrypt --create /home/ubuntu/truecrypt/lamp35g.tc --size=36000000 
--keyfiles=/home/ubuntu/wmi_tc.key --volume-type='normal' --filesystem='Linux Ext4' 
--protect-hidden=no --password='NotMyRealPassword' 
--encryption='AES' --hash='SHA-512' --random-source=./prand.out

truecrypt -t --create --size=32000000 --keyfiles=/home/ubuntu/wmi_tc.key --volume-type='normal' --filesystem=none --protect-hidden=no --password='NotMyRealPassword' --encryption='AES' --hash='SHA-512' <<AnswerQ
$mystring
AnswerQ



