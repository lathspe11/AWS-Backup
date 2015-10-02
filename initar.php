#!/usr/bin/php5
<?php

//Define directory paths and standard container and file names
require_once '/home/ubuntu/bin/AWS_include.php';

/////////////////////////////////////////////////////////////////////////////
// This script will download and break out the dirs for the nfs server
//

$bptc = '/usr/share/backuppc/bin/BackupPC_tarCreate '; // location of tarCreate
$sdo = 'sudo -u backuppc'; // who to run BackupPC_tarCreate as
$uba = '/home/ubuntu/bkup'; // where to put the burned files

$dtstrg = date('Y-m-d  h:i:s A');
echo "Start gitolite Extract at $dtstrg \n";

system("rm $uba/gitolite.tar.gz");
system("$sdo $bptc -h gitolite -n -1 -r / -p /home/git/ -s /home/git . | gzip > $uba/gitolite.tar.gz"); #Load gsap:/etc dir
/* */
$dtstrg = date('Y-m-d  h:i:s A');
echo "Start zimbra Extract at $dtstrg \n";
/*
$latest = trim(shell_exec("grep full /var/lib/backuppc/pc/zimbra8/backups | tail -1 | cut -f 1"));
# $latest holds the number of the most recent full backup
$savedall = array();
$savedall = explode("\n", trim(shell_exec("ls -d1 /var/lib/backuppc/pc/zimbra8/$latest/f%2fopt%2fbackup-zimbra/f* | cut -f 9 -d '/' | grep -v '^f\.' ")));
foreach ($savedall as $key) {
$i = strstr($key, "f");
echo "extracting zimbra-$i\n";

system("rm $uba/zimbra.tar.gz");
system("$sdo $bptc -h zimbra8 -n -1 -r / -p /opt/backup-zimbra/ -s /opt/backup-zimbra . | gzip > $uba/zimbra.tar.gz");
//}
/* */
$dtstrg = date('Y-m-d  h:i:s A');
echo "Extract Complete $dtstrg \n";
#sudo -u backuppc /usr/share/backuppc/bin/BackupPC_tarCreate -h zimbra8 -n -1 -r / -p /opt/backup-zimbra/ -s /opt/backup-zimbra
?>