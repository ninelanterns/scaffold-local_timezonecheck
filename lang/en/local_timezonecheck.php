<?php
/**
 * English lang file for the timezonecheck local plugin.
 *
 * @package    local
 * @subpackage timezonecheck
 * @copyright  &copy; 2014 Nine Lanterns Pty Ltd  {@link http://www.ninelanterns.com.au}
 * @author     evan.irving-pease
 * @version    1.0
 */

defined('MOODLE_INTERNAL') || die;

$string['pluginname'] = 'Timezone check';

$string['detectedpossiblechange'] = 'Detected possible change in time zone. The time zone in your user profile is <a href="{$a->baseurl}&use=userzone">{$a->userzone}</a>, ';
$string['diffbothzone'] = 'but your IP address suggests you are in <a href="{$a->baseurl}&use=ipzone">{$a->ipzone}</a> and your computer reports that you are in <a href="{$a->baseurl}&use=browserzone">{$a->browserzone}</a>. ';
$string['diffipzone'] = 'but your IP address suggests you are in <a href="{$a->baseurl}&use=ipzone">{$a->ipzone}</a>. ';
$string['diffbrowserzone'] = 'but your computer reports that you are in <a href="{$a->baseurl}&use=browserzone">{$a->browserzone}</a>. ';
$string['pleaseresovethis'] = 'Please <strong>click on the correct time zone</strong> to resove this apparent discrepancy.';
$string[''] = '';
$string[''] = '';