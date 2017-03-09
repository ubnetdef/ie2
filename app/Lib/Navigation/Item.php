<?php
namespace Navigation;

class Item extends Navigation {
	private $title;
	private $url;
	private $priority;
	private $active;
	private $acl = true;

	private $parent;

	public function __construct($title, $url, $priority=100) {
		parent::__construct();

		$this->setTitle($title);
		$this->setURL($url);
		$this->setPriority($priority);

		return $this;
	}

	/*
	 * ========================================
	 * Getters and Setters
	 * ========================================
	 */
	public function setTitle($title) {
		$this->title = $title;

		return $this;
	}

	public function getTitle() {
		return $this->title;
	}

	public function setURL($url) {
		$this->url = $url;

		return $this;
	}

	public function getURL() {
		return $this->url;
	}

	public function setPriority($priority) {
		$this->priority = $priority;

		return $this;
	}

	public function getPriority() {
		return $this->priority;
	}

	public function setActive($status=true) {
		$this->active = $status;

		return $this;
	}

	public function getActive() {
		return $this->active;
	}

	public function setACL($bool_or_closure) {
		if ( is_callable($bool_or_closure) ) {
			$bool_or_closure = function() use($bool_or_closure) {
				return (bool) $bool_or_closure;
			};
		}

		$this->acl = $bool_or_closure;

		return $this;
	}

	public function getACL() {
		return $this->acl();
	}

	public function setParent($parent) {
		$this->parent = $parent;

		return $this;
	}

	public function getParent() {
		return $this->parent;
	}
}