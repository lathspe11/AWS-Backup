#!/usr/bin/bash

#Mount the device 
#run as root

mount /dev/mapper/truecrypt1 /mnt/secure;
truecrypt --keyfiles=/home/ubuntu/wmi_tc.key --password='NotMyRealPassword' --protect-hidden=no wmi.tc /mnt/secure/;
