<?php

namespace tests\cases\mvc;

use qeeplay\Config;
use qeeplay\mvc\App;

use tests\includes\TestCase;
use tests\fixture\TestApp;

require __DIR__ . '/__init.php';

class ActionTest extends TestCase
{
	/**
	 * @var TestApp
	 */
	private $_app;

    function test_run()
    {
        ob_start();
        $this->_app->run();
        $result = ob_get_clean();
        $this->assertEquals('indexAction', $result);

        ob_start();
        $this->_app->run('help');
        $result = ob_get_clean();
        $this->assertEquals('helpAction', $result);

        ob_start();
        $this->_app->run('tests/empty');
        $result = ob_get_clean();
        $this->assertEquals('tests/emptyAction', $result);

        Config::set('app.default_action', 'help');
        ob_start();
        $this->_app->run();
        $result = ob_get_clean();
        $this->assertEquals('helpAction', $result);
    }

    function test_url()
    {
        $url = $this->_app->url('help');
        $this->assertEquals('?action=help', $url);
        $url = $this->_app->url('more', array('tag' => 'any', 'order' => 'desc'));
        $this->assertEquals('?action=more&tag=any&order=desc', $url);
    }

    function test_view()
    {
        $view = $this->_app->view('index', array());
        $this->assertType('qeeplay\\mvc\\view', $view);
        $this->assertEquals('indexView', $view->execute());

        $view = $this->_app->view('help', array());
        $this->assertType('qeeplay\\mvc\\view', $view);
        $this->assertEquals('helpView', $view->execute());

        $view = $this->_app->view('tests/empty', array());
        $this->assertType('qeeplay\\mvc\\view', $view);
        $this->assertEquals('tests/emptyView', $view->execute());

        $this->assertEquals('viewView', $this->_app->run('view'));
        $this->assertEquals('tests/viewView', $this->_app->run('tests/view'));
    }

    function test_tool()
    {
        $other_tool_config = array('test_key' => 'test_value');
        $more_tool_config = array(
            'class' => 'tests\\fixture\\tools\\more\\MoreTool',
            'test_key' => 'test_value',
        );
        Config::set('app.tools.other', $other_tool_config);
        Config::set('app.tools.more', $more_tool_config);
        $holder = $this->_app->tool('holder');
        $this->assertType('tests\\fixture\\tools\\HolderTool', $holder);

        $holder2 = $this->_app->tool('holder');
        $this->assertSame($holder, $holder2);

        $other = $this->_app->tool('other');
        $this->assertType('tests\\fixture\\tools\\OtherTool', $other);
        $this->assertEquals($other_tool_config, $other->config);

        $more = $this->_app->tool('more');
        $this->assertType('tests\\fixture\\tools\\more\\MoreTool', $more);
        $this->assertEquals($more_tool_config, $more->config);
    }

    function test_action_validate()
    {
        unset($_GET['failed']);

        $result = $this->_app->run('validations/test');
        $test   = array('_validate_input', 'execute', '_validate_output');
        $this->assertEquals($test, $result);

        $_GET['failed'] = 'input';
        $result = $this->_app->run('validations/test');
        $test   = array('_validate_input', '_on_validate_input_failed');
        $this->assertEquals($test, $result);

        $_GET['failed'] = 'output';
        $result = $this->_app->run('validations/test');
        $test   = array('_validate_input', 'execute', '_validate_output', '_on_validate_output_failed');
        $this->assertEquals($test, $result);
    }

    protected function setup()
    {
        Config::set('app.default_action', Config::get('defaults.default_action'));
    	$this->_app = new TestApp();
    }

    protected function teardown()
    {
        unset($this->_app);
    }
}
