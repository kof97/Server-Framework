<?php

namespace Framework\Coroutine;

/**
 * Class ReturnValue.
 * Get the Generator function's returns.
 *
 * @category PHP
 * @package  Coroutine
 * @author   Arno [<arnoliu@tencent.com> | <1048434786@qq.com>]
 */
class ReturnValue
{
	/**
	 * Return value.
	 */
    protected $value;

    /**
     * Init the return value.
     *
     * @param mixed $value The return value.
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Get the return value.
   	 *
   	 * @return mixed The return value.
     */
    public function getValue()
    {
        return $this->value;
    }
}

// end of script
