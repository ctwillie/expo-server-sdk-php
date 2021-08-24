<?php

namespace ExpoSDK\Expo;

use ExpoSDK\Expo\Exceptions\FileDoesntExistException;
use ExpoSDK\Expo\Exceptions\InvalidFileException;
use ExpoSDK\Expo\Exceptions\UnableToReadFileException;
use ExpoSDK\Expo\Exceptions\UnableToWriteFileException;

class File
{
    private $path;

    public function __construct(string $path)
    {
        $this->path = $path;

        if (! $this->isValidPath($path)) {
            throw new FileDoesntExistException(sprintf(
                'The file %s does not exist.',
                $path
            ));
        }

        if (! $this->isJson($path)) {
            throw new InvalidFileException(sprintf(
                'The storage file must have a .json extension.'
            ));
        }

        $this->validateContents();
    }

    /**
     * Check if the file path is valid and exists
     *
     * @param string $path
     * @return bool
     */
    private function isValidPath(string $path)
    {
        return strlen($path) > 0 && file_exists($path);
    }

    /**
     * Check if the file has a json extension
     *
     * @param string $path
     * @return bool
     */
    private function isJson(string $path)
    {
        return Helper::strEndsWith($path, '.json');
    }

    /**
     * Ensures the file contains an object
     *
     * @return void
     */
    private function validateContents()
    {
        $contents = $this->read();

        if (gettype($contents) !== "object") {
            $this->write(new \stdClass());
        }
    }

    /**
     * Reads the files contents
     *
     * @return object
     * @throws UnableToReadFileException
     */
    public function read()
    {
        $contents = @file_get_contents($this->path);

        if ($contents === false) {
            throw new UnableToReadFileException(sprintf(
                'Unable to read file at %s.',
                $this->path
            ));
        }

        return json_decode($contents);
    }

    /**
     * Writes content to the file
     *
     * @param object $contents
     * @return bool
     * @throws UnableToWriteFileException
     */
    public function write(object $contents)
    {
        $result = @file_put_contents(
            $this->path,
            json_encode($contents)
        );

        if ($result === false) {
            throw new UnableToWriteFileException(sprintf(
                'Unable to write file at %s.',
                $this->path
            ));
        }

        return true;
    }

    /**
     * Empties the files contents
     *
     * @return void
     */
    public function empty()
    {
        $this->write(new \stdClass());
    }
}
