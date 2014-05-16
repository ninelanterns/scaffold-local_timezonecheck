<?php
/**
 * Checks the timezone data from the user's browser and the GeoIP data from their
 * IP address against the timezone value saved in their profile.
 *
 * @package    local
 * @subpackage timezonecheck
 * @copyright  &copy; 2014 Nine Lanterns Pty Ltd  {@link http://www.ninelanterns.com.au}
 * @author     evan.irving-pease
 * @version    1.0
 */

require_once("../../config.php");

require_once($CFG->dirroot."/local/timezonecheck/lib.php");

// get the timezone from the client's browser
$browserzone = optional_param('browserzone', null, PARAM_TEXT);

$userzone = $USER->timezone;

$ipzone = null;

// get the client IP
$clientIP = getremoteaddr();

// make a cURL request to get the geolocation data for that IP
$ch = curl_init();

// TODO MaxMind have a much better paid service for this http://www.maxmind.com/en/web_services
curl_setopt($ch, CURLOPT_URL, "http://api.hostip.info/get_json.php?ip={$clientIP}");
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

// did we get a valid JSON response
if ($geoip = json_decode($response)) {
    // does the location match a valid timezone (not ideal)
    $ipzone = $DB->get_field('timezone', 'name', array('name'=>$geoip->country_name.'/'.$geoip->city), IGNORE_MULTIPLE);
}

// check both possible timezone differences
$diffgeoipzone = (!empty($ipzone) && !local_timezonecheck_compare($ipzone, $userzone));
$diffbrowserzone = (!empty($browserzone) && !local_timezonecheck_compare($browserzone, $userzone));

if (!$diffgeoipzone && !$diffbrowserzone) {
    // stop checking
    $SESSION->stoptimezonecheck = true;

    return;
}

// has a previous choice been made
if ($choice = $DB->get_record('timezonecheck', array('user_id'=>$USER->id))) {
    // check if the conditions have changed
    if ($choice->userzone != $userzone || $choice->ipzone != $ipzone || $choice->browserzone != $browserzone) {
        // remove the saved choice record
        $DB->delete_records('timezonecheck', array('id'=>$choice->id));
    } else {
        // set a flag in the session so we stop doing this check
        $SESSION->stoptimezonecheck = true;

        return;
    }
}

$params = compact('userzone', 'ipzone', 'browserzone');

// setup the base url
$baseurl = new moodle_url('/local/timezonecheck/update.php', $params);

$params['baseurl'] = $baseurl->out();

echo '<div id="timezone-container"><div id="timezone-warning">';
echo get_string('detectedpossiblechange', 'local_timezonecheck', $params);

if ($diffgeoipzone && $diffbrowserzone && $ipzone != $browserzone) {
    // both are different and conflicting
    echo get_string('diffbothzone', 'local_timezonecheck', $params);

} else if ($diffbrowserzone) {
    // different browser zone (or diff both but not conflicting)
    echo get_string('diffbrowserzone', 'local_timezonecheck', $params);

} else if ($diffgeoipzone) {
    // different GeoIP zone
    echo get_string('diffipzone', 'local_timezonecheck', $params);
}

echo get_string('pleaseresovethis', 'local_timezonecheck');

echo '</div></div>';