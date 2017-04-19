<?php
use AD7six\Dsn\Wrapper\CakePHP\V2\DbDsn;

class DATABASE_CONFIG {
    public function __construct() {
        $this->default = DbDsn::parse(env('INJECTENGINE_DB'));
        $this->scoreengine = DbDsn::parse(env('SCOREENGINE_DB'));
        $this->test = DbDsn::parse(env('DATABASE_TEST_URL'));
    }
}
