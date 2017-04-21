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

/**
 * Fuzzy Duration Generator
 *
 * @source http://stackoverflow.com/a/18602474
 * @param $start The start time
 * @param $end The end time
 * @return string The fuzzy time
 */
function fuzzy_duration($start, $end) {
    $start = new DateTime('@'.$start);
    $end = new DateTime('@'.$end);
    $diff = $end->diff($start);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = [
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    ];

    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    return implode(', ', $string);
}

/**
 * Get a (bool) env variable
 *
 * @param $name The env variable name
 * @return bool true/false
 */
public function benv($name) {
    return (bool)env($name);
}
