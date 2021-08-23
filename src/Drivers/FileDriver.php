<?php

namespace Twillie\Expo\Drivers;

use Twillie\Expo\Drivers\Driver;
use Twillie\Expo\File;


class FileDriver extends Driver
{
    /**
     * The path to the file
     *
     * @var string $path
     */
    private $path = __DIR__ . '/../storage/subscriptions.json';

    /**
     * The storage file object
     *
     * @var File $file
     */
    private $file;

    public function __construct(array $config)
    {
        $this->build($config);
    }

    protected function build(array $config)
    {
        $path = $config['path'] ?? $this->path;

        $this->file = new File($path);
    }
}
