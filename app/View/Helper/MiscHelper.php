<?php
App::uses('AppHelper', 'View/Helper');

class MiscHelper extends AppHelper {
	const NAVBAR_ITEM = '<li class="%s"><a href="%s">%s</a></li>';
	const NAVBAR_MENU = '<li class="dropdown %s">'.
		'<a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button">'.
		'%s <span class="caret"></span></a><ul class="dropdown-menu">%s</ul></li>';

	public function navbarItem($name, $url, $active=false) {
		return sprintf(self::NAVBAR_ITEM, ($active ? 'active' : ''), $this->url($url), $name);
	}

	public function navbarDropdown($name, $active, $children) {
		if ( empty(array_filter($children)) ) return '';
		
		return sprintf(self::NAVBAR_MENU, ($active ? 'active' : ''), $name, implode('', $children));
	}

	public function date($format, $ts) {
		$date = new DateTime('@'.$ts);
		$date->setTimezone(new DateTimeZone(env('TIMEZONE_USER')));

		return $date->format($format);
	}
}