<?php
namespace InjectTypes;

class Manager {
	protected $types = [];

	public function __construct($types) {
		foreach ( $types AS $type_name ) {
			$name = sprintf('InjectTypes\\%s', $type_name);
			$type = new $name();

			$this->types[$type->getID()] = $type;
		}
	}

	public function get($id) {
		if ( !isset($this->types[$id]) ) {
			throw new BadMethodCallException('Unknown type!');
		}

		return $this->types[$id];
	}

	public function getAll() {
		return $this->types;
	}
}