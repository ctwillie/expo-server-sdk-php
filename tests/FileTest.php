<?php

namespace ExpoSDK\Tests;

use ExpoSDK\Exceptions\UnableToReadFileException;
use ExpoSDK\Exceptions\UnableToWriteFileException;
use ExpoSDK\File;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    private $path = TEST_DIR . '/storage/expo.json';
    private $txtPath = TEST_DIR . '/storage/expo.txt';
    private $testPath = TEST_DIR . './storage/test.json';
    private $file;

    protected function setUp(): void
    {
        // so file can be populated with empty object
        file_put_contents($this->path, "");

        $this->file = new File($this->path);

        $this->file->empty();
    }

    protected function tearDown(): void
    {
        $this->file->empty();

        @unlink($this->testPath);

        @unlink($this->txtPath);
    }

    /** @test */
    public function file_class_instantiates()
    {
        $file = new File($this->path);

        $this->assertInstanceOf(File::class, $file);
    }

    /** @test */
    public function throws_exception_for_non_json_file()
    {
        $file = fopen($this->txtPath, "w");
        fclose($file);

        $this->expectExceptionMessage(
            'The storage file must have a .json extension.'
        );

        new File($this->txtPath);
    }

    /** @test */
    public function throws_exception_if_unable_to_read_file()
    {
        $file = fopen($this->testPath, "w");
        fclose($file);
        $file = new File($this->testPath);
        @unlink($this->testPath);

        $this->expectException(UnableToReadFileException::class);

        $file->read();
    }

    /** @test */
    public function throws_exception_if_unable_to_write_file()
    {
        $file = fopen($this->testPath, "w");
        fclose($file);
        $file = new File($this->testPath);
        @unlink($this->testPath);

        $this->expectException(UnableToWriteFileException::class);

        $file->write(new \stdClass());
    }
}
