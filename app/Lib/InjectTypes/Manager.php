<?php
namespace InjectTypes;

class Manager {

    protected $types = [];

    public function __construct($types) {
        foreach ($types as $type_name) {
            $name = sprintf('InjectTypes\\%s', $type_name);
            $type = new $name();

            $this->types[$type->getID()] = $type;
        }
    }

    public function get($id) {
        if (!isset($this->types[$id])) {
            throw new \BadMethodCallException('Unknown type - '.$id.'!');
        }

        return $this->types[$id];
    }

    public function getAll() {
        return $this->types;
    }
}
