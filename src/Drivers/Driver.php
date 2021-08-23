<?php

namespace Twillie\Expo\Drivers;

abstract class Driver
{
    /**
     * Builds the driver instance.
     *
     * @param array $config
     * @return void
     */
    abstract protected function build(array $config);
}
