<?php
// capture their IP address
$ip = $_SERVER['REMOTE_ADDR'];

// this is the path to the arp command used to get user MAC address
// from it's IP address in linux environment.

$arp = "/usr/sbin/arp";

// execute the arp command to get their mac address

$mac = shell_exec("sudo $arp -an " . $ip);

preg_match('/..:..:..:..:..:../',$mac , $matches);
$mac = @$matches[0];

echo $mac;

// if MAC Address couldn't be identified.

if( $mac === NULL) { echo "Access Denied."; exit; } ?>
