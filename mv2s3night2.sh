#!/bin/bash
# This script calls mv2s3.php to Push encrypted containers to the cloud
# Intent is to hold the durration of this to one evening.  Not all containers will be done

# Copy Tar ball to container command
sdo='sudo -u backuppc' # who to run BackupPC_tarCreate as
ubin=/home/ubuntu/bin # where to put the burned files

#Command to invoke aws takes as an argument one of the key values used to create the containers
#lamp post64r710 post64r910 gsap newlamp nfs ocd
mv2s3cmd="$ubin/mv2s3.php"

echo "Begin mv2s3" >>/home/ubuntu/logs/bkuptar.txt 2>&1

# Hand copy of each set of backups to s3 in size sorted order 
for i in post64r710 post64r910 nfs ocd #lamp post64r710 post64r910 gsap newlamp 
do
  echo "mv2s3 $i file set";
  $mv2s3cmd $i >>/home/ubuntu/logs/bkuptar.txt 2>&1
done



