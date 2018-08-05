<?php

class Main_Plugin_Class
{
    protected function __construct() { }

    /** @var static */
    protected static $instance;

    /** @return static */
    public static function get_instance() {
        if ( null === static::$instance ) {
            static::$instance = new static;
        }
        return static::$instance;
    }

    public function register_components() {
    }
}
