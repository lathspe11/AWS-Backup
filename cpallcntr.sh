#!/bin/bash
# This script calls BackupPC_tarCreate for all the WMI backups

bptc=/usr/share/backuppc/bin/BackupPC_tarCreate # location of tarCreate
# Copy Tar ball to container command
cpnam=/home/ubuntu/bin/cpnam2tc.php 
sdo='sudo -u backuppc' # who to run BackupPC_tarCreate as
uba=/home/ubuntu/bkup # where to put the burned files

# Cleanup
#rm $uba/*.tar.gz
date

# Handle copy of each set of backups to containers in size sorted order 

for i in gitolite lamp post64r710 post64r910 gsap newlamp nfs zimbra ocd 
do
  echo "cpnam2tc HOME/bkup/$i file set to /mnt/secure/$i ";
  $cpnam $i >>/home/ubuntu/logs/bkuptar.txt 2>&1
  date
done



