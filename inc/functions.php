<?php
function distance_of_time_in_words($from_time,$to_time = 0, $include_seconds = false) {
	$dm = $distance_in_minutes = abs(($from_time - $to_time))/60;
	$ds = $distance_in_seconds = abs(($from_time - $to_time));
	
	switch ($distance_in_minutes) {
		case $dm > 0 && $dm < 2:
		if($include_seconds == false) {
			if ($dm == 0) {
				return 'less than a minute';
			} else {
				return '1 minute';
			}
		} else {
			switch ($distance_of_seconds) {
				case $ds > 0 && $ds < 4:
					return 'less than 5 seconds';
					break;
				case $ds > 5 && $ds < 9:
					return 'less than 10 seconds';
					break;
				case $ds > 10 && $ds < 19:
					return 'less than 20 seconds';
					break;
				case $ds > 20 && $ds < 39:
					return 'half a minute';
					break;
				case $ds > 40 && $ds < 59:
					return 'less than a minute';
					break;
				default:
					return '1 minute';
				break;
			}
		}
		break;
		case $dm > 2 && $dm < 44:
			return round($dm) . ' minutes';
			break;
		case $dm > 45 && $dm < 89:
			return 'about 1 hour';
			break;
		case $dm > 90 && $dm < 1439:
			return 'about ' . round($dm / 60.0) . ' hours';
			break;
		case $dm > 1440 && $dm < 2879:
			return '1 day';
			break;
		case $dm > 2880 && $dm < 43199:
			return round($dm / 1440) . ' days';
			break;
		case $dm > 43200 && $dm < 86399:
			return 'about 1 month';
			break;
		case $dm > 86400 && $dm < 525599:
			return round($dm / 43200) . ' months';
			break;
		case $dm > 525600 && $dm < 1051199:
			return 'about 1 year';
			break;
		default:
			return 'over ' . round($dm / 525600) . ' years';
		break;
	}
}

function parse_http_response ($string) {
  $headers = array();
  $content = '';
  $str = strtok($string, "\n");
  $h = null;
  while ($str !== false) {
    if ($h and trim($str) === '') {
      $h = false;
      continue;
    }
    if ($h !== false and false !== strpos($str, ':')) {
      $h = true;
      list($headername, $headervalue) = explode(':', trim($str), 2);
      $headername = strtolower($headername);
      $headervalue = ltrim($headervalue);
      if (isset($headers[$headername]))
        $headers[$headername] .= ',' . $headervalue;
      else
        $headers[$headername] = $headervalue;
    }
    if ($h === false) {
      $content .= $str."\n";
    }
    $str = strtok("\n");
  }
  return array($headers, trim($content));
}
?>
