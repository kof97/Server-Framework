<?php

namespace Framework\Base;

/**
 * interface RouteInterface.
 *
 * @category PHP
 * @author   Arno <1048434786@qq.com>
 */
interface RouteInterface
{
	public function load();

	public function run();

	public function getResource();

	public function getAct();
}

// end of script
