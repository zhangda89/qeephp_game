<?php

namespace tests\cases\core;

use qeeplay\Config;

use tests\includes\TestCase;

require_once __DIR__ . '/__init.php';

class ConfigTest extends TestCase
{
    private static $_test_config = array(
        'key1' => 'value1',
        'nested' => array(
            'subkey1' => 'subvalue1',
            'subkey2' => 'subvalue2',
        ),
    );

    function test_import()
    {
        Config::import(self::$_test_config);
        $this->assertEquals('value1', Config::$_config['key1']);
        Config::$_config['key1'] = 'changed';
        Config::import(self::$_test_config);
        $this->assertEquals('value1', Config::$_config['key1']);
    }

    function test_get()
    {
        Config::$_config = self::$_test_config;
        $this->assertEquals('value1', Config::get('key1'));
        $this->assertEquals('default value', Config::get('not exists key', 'default value'));

        $nested = Config::get('nested');
        $this->assertType('array', $nested);
        $this->assertEquals(self::$_test_config['nested'], $nested);

        Config::$_config['first_key'] = 'first_value';
        $this->assertEquals('first_value', Config::get(array('first_key', 'second_key')));
        unset(Config::$_config['first_key']);
        Config::$_config['second_key'] = 'second_value';
        $this->assertEquals('second_value', Config::get(array('first_key', 'second_key')));
    }

    function test_set()
    {
        $defaults = require(QPLAY_PATH . '/__defaults.php');
        Config::$_config = array_merge($defaults, self::$_test_config);
        Config::set('key2', 'changed');
        $this->assertEquals('changed', Config::get('key2'));

        Config::set('key3', 'new key');
        $this->assertEquals('new key', Config::get('key3'));
    }

    protected function setup()
    {
        Config::$_config = array();
    }
}

