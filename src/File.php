<?php

namespace ExpoSDK;

use ExpoSDK\Exceptions\FileDoesntExistException;
use ExpoSDK\Exceptions\InvalidFileException;
use ExpoSDK\Exceptions\UnableToReadFileException;
use ExpoSDK\Exceptions\UnableToWriteFileException;

class File
{
    /** @var string */
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
     */
    private function isValidPath(string $path): bool
    {
        return strlen($path) > 0 && file_exists($path);
    }

    /**
     * Check if the file has a json extension
     */
    private function isJson(string $path): bool
    {
        return Utils::strEndsWith($path, '.json');
    }

    /**
     * Ensures the file contains an object
     */
    private function validateContents(): void
    {
        $contents = $this->read();

        if (gettype($contents) !== "object") {
            $this->write(new \stdClass());
        }
    }

    /**
     * Reads the files contents
     *
     * @return object|null
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
     * @throws UnableToWriteFileException
     */
    public function write(object $contents): bool
    {
        $exception = new UnableToWriteFileException(sprintf(
            'Unable to write file at %s.',
            $this->path
        ));

        if (! file_exists($this->path)) {
            throw $exception;
        }

        $result = @file_put_contents(
            $this->path,
            json_encode($contents)
        );

        if ($result === false) {
            throw $exception;
        }

        return true;
    }

    /**
     * Empties the files contents
     */
    public function empty(): void
    {
        $this->write(new \stdClass());
    }
}
