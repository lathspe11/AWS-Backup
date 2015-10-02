#!/bin/bash
# This script calls BackupPC_tarCreate for all the WMI backups

bptc=/usr/share/backuppc/bin/BackupPC_tarCreate # location of tarCreate
sdo='sudo -u backuppc' # who to run BackupPC_tarCreate as
uba=/home/ubuntu/bkup # where to put the burned files

# Cleanup
#rm $uba/*.tar.gz
  date
  echo "Begin allstar.sh"
  echo "Start gsap Extract "

# Handle backups for gsap, post gsap shell server 
# gsap is different bacause we want backups in separate tarballs for each directory in the inet share
latest=`tail -1 /var/lib/backuppc/pc/gsap/backups | cut -f 1` # $latest holds the number of the most recent backup
for i in `ls -d /var/lib/backuppc/pc/gsap/$latest/f%2finet/f* | cut -f 9 -d '/' | grep -v '^f\.'`
do
  i=`expr $i : 'f\(.*\)'` # unmangle directory name in $i by stripping leading 'f'
  echo "extracting gsap-$i"
  rm $uba/gsap_$i.tar.gz
  $sdo $bptc -h gsap -n -1 -r / -p /inet/ -s /inet $i | gzip > $uba/gsap_$i.tar.gz
done
# finish the shares for gsap, shell server
  echo "extracting gsap-etc"
  rm $uba/gsap_etc.tar.gz
$sdo $bptc -h gsap -n -1 -r / -p /etc/ -s /etc . | gzip > $uba/gsap_etc.tar.gz #Load gsap:/etc dir
  echo "extracting gsap-jail"
  rm $uba/gsap_jail.tar.gz
$sdo $bptc -h gsap -n -1 -r / -p /srv/ -s /srv . | gzip > $uba/gsap_jail.tar.gz #Load gsap:gsap_jail dir 
#$sdo $bptc -h gsap -n -1 -r / -p /var/lib/mysql/ -s /var/lib/mysql . | gzip > $uba/gsap_mysql.tar.gz
date;


# Handle backups for post64r910, shell db
for i in shell gsap
do
  echo "extracting post64r910 db dump-$i";
  rm $uba/post64r910_$i.tar.gz
  $sdo $bptc -h post64r910 -n -1 -r / -s /data /$i.dump | gzip > $uba/post64r910_$i.tar.gz
done
date;

# Handle backups for post64710, xom db
for i in wmi xom derm lubes mobil1 reports fleetcard
do
  echo "extracting post64r710 db dump-$i"
  rm $uba/post64r710_$i.tar.gz
  $sdo $bptc -h post64r710 -n -1 -r / -s /data /$i.dump | gzip > $uba/post64r710_$i.tar.gz
done
date;

echo "extracting OC Derm-$i";
# Handle backups for the rest
for i in ocdapache  #removed because it grew to be overlarge, scp root@mail:/opt/backup-zimbra/* instead
do
  rm $uba/$i.tar.gz
  $sdo $bptc -h $i -n -1 -s \* . | gzip > $uba/$i.tar.gz
done
date;

rm $uba/newlamp*.tar.gz
# Handle backups for newlamp
# newlamp is different bacause we want backups in separate tarballs for each directory in the inet share
latest=`tail -1 /var/lib/backuppc/pc/newlamp/backups | cut -f 1` # $latest holds the number of the most recent backup
for i in `ls -d /var/lib/backuppc/pc/newlamp/$latest/f%2finet/f* | cut -f 9 -d '/' | grep -v '^f\.'`
do
  echo "extracting newlamp-$i";
  i=`expr $i : 'f\(.*\)'` # unmangle directory name in $i by stripping leading 'f'
  $sdo $bptc -h newlamp -n -1 -r / -p /inet/ -s /inet $i | gzip > $uba/newlamp_$i.tar.gz
done
# finish the shares for newlamp
echo "extracting newlamp-etc"
$sdo $bptc -h newlamp -n -1 -r / -p /etc/ -s /etc . | gzip > $uba/newlamp_etc.tar.gz
echo "extracting mysql"
$sdo $bptc -h newlamp -n -1 -r / -p /var/lib/mysql/ -s /var/lib/mysql . | gzip > $uba/newlamp_mysql.tar.gz

# Handle backups for lamp the old shell server 
# lamp is different bacause we want backups in separate tarballs for each directory in the inet share
latest=`tail -1 /var/lib/backuppc/pc/lamp/backups | cut -f 1` # $latest holds the number of the most recent backup
for i in `ls -d /var/lib/backuppc/pc/lamp/$latest/f%2finet/f* | cut -f 9 -d '/' | grep -v '^f\.'`
do
  i=`expr $i : 'f\(.*\)'` # unmangle directory name in $i by stripping leading 'f'
  if [ "$i" == "webalizer" ] || [ "$i" == "reports" ]
  	then
  	rm $uba/lamp_$i.tar.gz
  	echo "extracting lamp-$i";
  	$sdo $bptc -h lamp -n -1 -r / -p /inet/ -s /inet $i | gzip > $uba/lamp_$i.tar.gz
  fi
done

# finish the shares for lamp
date;



