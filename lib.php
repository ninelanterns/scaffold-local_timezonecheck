<?php
/**
 * Function library for the timezonecheck local plugin.
 *
 * @package    local
 * @subpackage timezonecheck
 * @copyright  &copy; 2014 Nine Lanterns Pty Ltd  {@link http://www.ninelanterns.com.au}
 * @author     evan.irving-pease
 * @version    1.0
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Callback intended for extending the navigation menu, but we're using it to
 * execute the timezone check on each page.
 *
 * Kind of hacky, but I couldn't see another way to call the JS in this module
 * on every page.
 *
 */
function local_timezonecheck_extends_navigation(global_navigation $nav) {
    global $PAGE, $SESSION;

    // only detect the timezone for logged in users
    if (isloggedin() && !isguestuser() && empty($SESSION->stoptimezonecheck)) {
        $PAGE->requires->js('/local/timezonecheck/javascript/jstz-1.0.4.min.js', true);
        $PAGE->requires->js_init_call('M.local_timezonecheck.detect', null, true);
    }
}

/**
 * Checks if two timezones are materially different.
 *
 */
function local_timezonecheck_compare($zone1, $zone2) {
    global $DB;

    // are the zones different
    if ($zone1 != $zone2) {

        // the list of timezone fields which might differ
        $fields = 'year, gmtoff, dstoff, dst_month, dst_startday, dst_weekday, dst_skipweeks, dst_time, std_month, std_startday, std_weekday, std_skipweeks, std_time';

        // get the most recent timezone records for each zone
        $zone1data = $DB->get_records('timezone', array('name'=>$zone1), 'year DESC', $fields, 0, 1);
        $zone2data = $DB->get_records('timezone', array('name'=>$zone2), 'year DESC', $fields, 0, 1);

        // is the difference material (use simple object comparison)
        if ($zone1data != $zone2data) {
            return false;
        }
    }

    return true;
}