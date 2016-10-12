<?php

namespace Framework\Base;

/**
 * interface RouteInterface.
 *
 * @category PHP
 */
interface RouteInterface
{
    public function load();

    public function run();

    public function getResource();

    public function getAct();
}

// end of script
