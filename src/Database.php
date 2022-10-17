<?php

namespace ExpoSDK;

use ExpoSDK\Exceptions\TableDoesntExistException;

class Database
{
    private $tableName;
    private $serverName;
    private $userName;
    private $password;
    private $database;
    private $connection;

    public function __construct(string $tableName, string $serverName, string $userName, string $password, string $database)
    {
        $this->tableName = $tableName;
        $this->serverName = $serverName;
        $this->userName = $userName;
        $this->password = $password;
        $this->database = $database;
        $this->connection = mysqli($serverName, $userName, $password, $database);

        if ($this->connection->connect_error) {
            die("Connection failed: " . $conn->connect_error);
          }
        if (! $this->isValidtable()) {
            throw new TableDoesntExistException(sprintf(
                'The database table %s does not exist.',
                $tableName
            ));
        }
    }

    /**
     * Check if the table exists on the current database
     */
    private function isValidTable(string $table): bool
    {
        return strlen($table) > 0; // How would a vanilla php project validate a table?
    }

    /**
     * Writes content to the file
     *
     * @throws UnableToSaveDataException
     */
    public function storeInTable(object $contents): bool
    {
        $exception = new UnableToSaveDataException(sprintf(
            'Unable to save data to %s.',
            $this->tableName
        ));

        // if table doesn't exist - how to check in generic PHP?

        $sql = "INSERT INTO MyGuests (firstname, lastname, email)
        VALUES ('John', 'Doe', 'john@example.com')";

        if ($conn->query($sql) === TRUE) {
          echo "New record created successfully";
        } else {
          echo "Error: " . $sql . "<br>" . $conn->error;
        }

        $conn->close();

        if ($result === false) {
            throw $exception;
        }

        return true;
    }

    /**
     * Empties the files contents
     */
    public function deleteFromTable(): void
    {
        $this->write(new \stdClass());
    }
}
