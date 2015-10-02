#!/bin/bash
# This script calls BackupPC_tarCreate for all the WMI backups

bptc=/usr/share/backuppc/bin/BackupPC_tarCreate # location of tarCreate
sdo='sudo -u backuppc' # who to run BackupPC_tarCreate as
uba=/home/ubuntu/bkup # where to put the burned files

# Cleanup
#rm $uba/*.tar.gz
  date
  echo "Begin nfstar.sh"
  echo "Start nfs Extract "



# Handle backups for nfs the old shell server 
# nfs is different bacause we want backups in separate tarballs for each directory in the inet share
latest=`tail -1 /var/lib/backuppc/pc/nfs/backups | cut -f 1` # $latest holds the number of the most recent backup
x=1
savedall=`ls -d1 /var/lib/backuppc/pc/nfs/$latest/f%2fnfs/f* | grep -v '^f\.'  | grep -v 'pad' | grep -iv 'temp' | cut -f 9 -d '/' `
for i in "${savedall[@]}"
do
  x=$x+1
  k=`expr $i : 'f\(.*$\)'` # unmangle directory name in $i by stripping leading 'f'
  printf '%d%s\n' "$x$k"

 # echo "extracting nfs-$k"
  #j=`echo $k | sed 's/[:blank:]+/,/g' `
  #echo $j
  #echo " $k ";echo ""
 # if [ "$i" == "webalizer" ] || [ "$i" == "reports" ]
  #	then
  	#rm $uba/nfs_$i.tar.gz
  	#$sdo $bptc -h nfs -n -1 -r / -p /nfs/ -s /nfs $k | gzip > $uba/nfs_$k.tar.gz
  #fi
done
# finish the shares for nfs
date;



