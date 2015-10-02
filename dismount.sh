#!/usr/bin/bash

#Dismount the device 
#run as root	as ubuntu

date;
umount /mnt/secure;
truecrypt -d /home/ubuntu/truecrypt/wmi.tc;
date;
