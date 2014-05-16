<?php
/**
 * Update the user's timezone to the given value and save the specifics of the
 * decision so we know not to ask again unless something changes.
 *
 * @package    local
 * @subpackage timezonecheck
 * @copyright  &copy; 2014 Nine Lanterns Pty Ltd  {@link http://www.ninelanterns.com.au}
 * @author     evan.irving-pease
 * @version    1.0
 */

require_once("../../config.php");

// get the params
$use = required_param('use', PARAM_TEXT);

$userzone = optional_param('userzone', null, PARAM_TEXT);
$ipzone = optional_param('ipzone', null, PARAM_TEXT);
$browserzone = optional_param('browserzone', null, PARAM_TEXT);

$newtimezone = ${$use};

// update the user's timezone if necessary
if ($use != 'userzone') {
    $DB->set_field('user', 'timezone', $newtimezone, array('id'=>$USER->id));
    $USER->timezone = $newtimezone;
}

// save the choice relative to these options so we don't constantly hassle the user
$check = new stdClass();
$check->user_id = $USER->id;
$check->userzone = $newtimezone;
$check->ipzone = $ipzone;
$check->browserzone = $browserzone;
$check->timecreated = time();
$check->timemodified = time();

$DB->insert_record('timezonecheck', $check);

// set a flag in the session so we stop doing this check
$SESSION->stoptimezonecheck = true;