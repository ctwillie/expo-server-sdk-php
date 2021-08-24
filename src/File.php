<?php

namespace Twillie\Expo;

use Twillie\Expo\Exceptions\FileDoesntExistException;
use Twillie\Expo\Exceptions\InvalidFileException;
use Twillie\Expo\Exceptions\UnableToReadFileException;
use Twillie\Expo\Exceptions\UnableToWriteFileException;

class File
{
    private $path;

    public function __construct(string $path)
    {
        $this->path = $path;

         if (! $this->isValidPath($path)) {
            throw new FileDoesntExistException(sprintf(
                'The file %s does not exist.', $path
            ));
        }

        if (! $this->isJson($path)) {
            throw new InvalidFileException(sprintf(
                'The storage file must have a .json extension.'
            ));
        }

        $this->validateContents();
    }

    private function isValidPath($path)
    {
        return is_string($path) && strlen($path) !== 0 && file_exists($path);
    }

    private function isJson(string $path)
    {
        $ext = '.json';
        $length = strlen($ext);

        return $length > 0
            ? substr($path, -$length) === $ext
            : true;
    }

    private function validateContents()
    {
        $contents = $this->read();

        if (gettype($contents) !== "object") {
            $this->write(new \stdClass);
        }
    }

    /**
     * Reads a files contents
     *
     * @return object
     * @throws UnableToReadFileException
     */
    public function read()
    {
        $contents = @file_get_contents($this->path);

        if ($contents === false) {
            throw new UnableToReadFileException(sprintf(
                'Unable to read file at %s.', $this->path
            ));
        }

        return json_decode($contents);
    }

    /**
     * Writes to a file
     *
     * @param object $contents
     * @return bool
     * @throws UnableToWriteFileException
     */
    public function write(object $contents)
    {
        $result = @file_put_contents($this->path, json_encode($contents));

        if ($result === false) {
            throw new UnableToWriteFileException(sprintf(
                'Unable to write file at %s.', $this->path
            ));
        }

        return true;
    }

    /**
     * Empties a files contents
     *
     * @return void
     */
    public function empty()
    {
        $this->write(new \stdClass());
    }
}
