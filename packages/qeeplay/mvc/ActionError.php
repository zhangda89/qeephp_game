<?php

namespace qeeplay\mvc;

use qeeplay\Error;

/**
 * 与 MVC 有关的异常
 */
class ActionError extends Error
{
    const NOT_SET_TOOL      = 1;
    const ACTION_NOT_FOUND  = 2;

    static function not_set_tool_error($toolname)
    {
        return new static("NOT_SET_TOOL: {$toolname}", self::NOT_SET_TOOL);
    }

    static function action_not_found_error($action_name)
    {
        return new static("ACTION_NOT_FOUND: {$action_name}", self::ACTION_NOT_FOUND);
    }
}

