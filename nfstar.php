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
//Load gitolite
system("$sdo $bptc -h gitolite -n -1 -r / -p /home/git/ -s /home/git . | gzip > $uba/gitolite.tar.gz"); 

# finish the shares for nfs
$dtstrg = date('Y-m-d  h:i:s A');
echo "Start NFS Extract at $dtstrg \n";
# Handle backups for nfs the old shell server
# nfs is different bacause we want backups in separate tarballs for each directory in the inet share
$latest = trim(shell_exec("tail -1 /var/lib/backuppc/pc/nfs/backups | cut -f 1")); // $latest holds the number of the most recent backup
$x = 1;
$savedall = array();
$savedall = explode("\n", trim(shell_exec("ls -d1 /var/lib/backuppc/pc/nfs/$latest/f%2fnfs/f* | grep -v '^f\.'  | grep -v 'pad' | grep -iv 'temp' | grep -iv 'trash' | cut -f 9 -d '/' ")));
foreach ($savedall as $key) {
	$pos = strpos($key, 'f');
	if ($pos !== false) {
		//Skip entry not prefixed with 'f'
		$str = substr($key, 1); //unmangle directory name in $i by stripping leading 'f'
		$str1 = str_replace(' ', '\ ', $str);
		$str2 = str_replace(' ', '_', $str);
		print "Extracting from host NFS nfs_$str2.tar.gz\n";
		system("rm $uba/nfs_$str2.tar.gz");
		system("$sdo $bptc -h nfs -n -1 -r / -p /nfs/ -s /nfs $str1 | gzip > $uba/nfs_$str2.tar.gz");
	}

}
# finish the shares for nfs

$dtstrg = date('Y-m-d  h:i:s A');
echo "Start zimbra Extract at $dtstrg \n";

$latest = trim(shell_exec("grep full /var/lib/backuppc/pc/zimbra8/backups | tail -1 | cut -f 1"));
# $latest holds the number of the most recent full backup
$savedall = array();
$savedall = explode("\n", trim(shell_exec("ls -d1 /var/lib/backuppc/pc/zimbra8/$latest/f%2fopt%2fbackup-zimbra/f* | cut -f 9 -d '/' | grep -v '^f\.' ")));
foreach ($savedall as $key) {
	$i = trim(shell_exec("expr $key : 'f\(.*\)'")); # unmangle directory name in $i by stripping leading 'f'
	echo "extracting zimbra-$i\n";
	system("rm $uba/zimbra_$i.tar.gz");
	system("$sdo $bptc -h zimbra8 -n -1 -r / -p /opt/backup-zimbra/ -s /opt/backup-zimbra $i | gzip > $uba/zimbra_$i.tar.gz");
}

$dtstrg = date('Y-m-d  h:i:s A');
echo "Extract Complete $dtstrg \n";
#sudo -u backuppc /usr/share/backuppc/bin/BackupPC_tarCreate -h gitolite -n -1 -s /home/gitolite /p /home/gitolite -r /
?>