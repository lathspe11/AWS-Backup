<?php

//Byte sized memory Constants for Estimates and Calculations
$oneBlock = 1024;
$oneMeg = 1000 * $oneBlock;
$oneGig = 1000000 * $oneBlock;
$fsfree = 0.05; //Percent of container unusable dedicated to filesystem overhead

$prandout = '/tmp/prand.out'; //Generate random input for truecrypt

//Volume conatainer names and sizes sorted by size in Gig
$volcontnr = array('lamp' => 3,
	'post64r710' => 6,
	'post64r910' => 30,
	'gsap' => 37,
	'newlamp' => 40,
	'nfs' => 40,
	//'mytest' => 3,
	'ocd' => 126,
	'zimbra' => 110,
	'gitolite' => 3);

$bucket2 = array('gsap' => 'swatbackups',
	'post64r710' => 'xombackups',
	'post64r910' => 'swatbackups',
	'newlamp' => 'xombackups',
	//'mytest' => 'wmibackups',
	'lamp' => 'swatbackups',
	'ocd' => 'wmibackups',
	'nfs' => 'wmibackups',
	'zimbra' => 'wmibackups',
	'gitolite' => 'xombackups');

//$testhome=1;

if (isset($testhome)) {
	$hdir = '/home/wmi/';
	$bkupdir = $hdir . "bkup/";
	$prgdir = $hdir . "Programs/AWS/";
	$contrdir = $prgdir;
	$vpw = 'NotMyRealPassword';
	$kyfile = $prgdir . 'wmi_tc.key';
	$tcdir = $prgdir . 'truecrypt/';
} else {
	$hdir = '/home/ubuntu/';
	$bkupdir = $hdir . "bkup/";
	$prgdir = $hdir . "bin/";
	$vpw = 'NotMyRealPassword';
	$contrdir = $hdir . 'truecrypt/';
	$kyfile = $hdir . 'wmi_tc.key';
	$tcdir = $hdir . 'truecrypt/';
}
echo "\nStarting ${argv[0]} \n";
?>