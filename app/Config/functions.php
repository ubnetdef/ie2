<?php

/**
 * Encode only quotes
 *
 * @param $str The input string
 * @return str The encoded string
 */
function encode_quotes($str) {
	return str_replace(['"', "'", '&'], ['&quot;', '&apos;', '&amp;'], $str);
}

/**
 * Format a timestamp according to the
 *  user's timezone
 *
 * @param $format The format according to `date`
 * @param $ts The timestamp
 * @return string The formatted time from the timestamp
 */
function tz_date($format, $ts) {
	$date = new DateTime('@'.$ts);
	$date->setTimezone(new DateTimeZone(env('TIMEZONE_USER')));

	return $date->format($format);
}