<?php
namespace Twillie\Expo;

use Twillie\Expo\Exceptions\UnsupportedDriverException;
use Twillie\Expo\Drivers\FileDriver;

class DriverManager
{
    /**
     * @var array $supportedDrivers
     */
    private $supportedDrivers = [
        'file',
    ];

    /**
     * @var string $driverKey
     */
     private $driverKey;

    /**
     * @var Driver $driver
     */
    private $driver;

    public function __construct(string $driver, array $config = [])
    {
        $this->validateDriver($driver)
            ->buildDriver($config);
    }

    private function validateDriver(string $driver)
    {
        $this->driverKey = strtolower($driver);

        if (! in_array($this->driverKey, $this->supportedDrivers)) {
            throw new UnsupportedDriverException(sprintf(
                'Driver %s is not supported', $driver
            ));
        }

        return $this;
    }

    private function buildDriver(array $config)
    {
        if ($this->driverKey === 'file') {
            $this->driver = new FileDriver($config);
        }
    }
}
