<?php
namespace Navigation;

class Navigation {
	private $items;

	public function __construct() {
		$this->items = new Collection();
	}

	public function create($name, $items) {}
	public function get($path, $separator='/') {}
	public function add($title, $url, $priority=100) {}
}