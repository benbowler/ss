<?php

function bp_maps_ip_first($ips) {
  if (($pos = strpos($ips, ',')) != false) {
    return substr($ips, 0, $pos);
  } else {
    return $ips;
  }
}

function bp_maps_ip_valid($ips) {
  if (isset($ips)) {
    $ip    = bp_maps_ip_first($ips);
	
    $ipnum = ip2long($ip);
    if ($ipnum !== -1 && $ipnum !== false && (long2ip($ipnum) === $ip)) { // PHP 4 and PHP 5
      if (($ipnum < 167772160   || $ipnum >   184549375) && // Not in 10.0.0.0/8
          ($ipnum < -1408237568 || $ipnum > -1407188993) && // Not in 172.16.0.0/12
          ($ipnum < -1062731776 || $ipnum > -1062666241))   // Not in 192.168.0.0/16
        return true;
    }
  }
  return false;
}

function bp_maps_get_ip() {
  $check = array('HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED',
                 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED',
                 'HTTP_VIA', 'HTTP_X_COMING_FROM', 'HTTP_COMING_FROM');

  foreach ($check as $c) {
    if (bp_maps_ip_valid(&$_SERVER[$c])) {
      return bp_maps_ip_first($_SERVER[$c]);
    }
  }

  return $_SERVER['REMOTE_ADDR'];
}

?>