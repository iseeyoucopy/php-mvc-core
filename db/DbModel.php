<?php

namespace iseeyoucopy\phpmvc\db;

use iseeyoucopy\phpmvc\Application;
use iseeyoucopy\phpmvc\Model;

/**
 * Class DbModel
 *
 * @author  iseeyoucopy <iseeyoucopy@yahoo.com>
 * @package iseeyoucopy\phpmvc
 */
abstract class DbModel extends Model
{
    abstract public static function tableName(): string;
    /**
     * Retrieves the primary key of the model.
     *
     * @return string The name of the primary key column.
     */
    public static function primaryKey(): string
    {
        return 'id';
    }

    public static function prepare($sql): \PDOStatement
    {
        try {
            return Application::$app->db->prepare($sql);
        } catch (\PDOException $e) {
            self::handleError($e->getMessage());
            throw $e;
        }
    }

    /*
    public static function prepare($sql): \PDOStatement
    {
        try {
            return Application::$app->db->prepare($sql);
        } catch (\PDOException $e) {
            if ($e->getCode() === 'IMSSP' || $e->getCode() === 'IM001') {
                // IMSSP: SQL Server "Invalid SQL statement"; IM001: Driver does not support this function
                self::handleError("Row not found in the database");
                throw new Exception("Row not found in the database", 404);
            } else {
                self::handleError($e->getMessage());
                throw $e;
            }
        }
    }
*/
    public static function handleError($errorMessage)
    {
        // Custom error handling logic for DbModel class
        // This method could log the error, send alerts, or display a friendly message
        echo "An error occurred in DbModel: " . $errorMessage;
        // You can customize this method to handle errors according to your application's needs
    }
    public function save()
    {
        $tableName = $this->tableName();
        $attributes = $this->attributes();
        $params = array_map(fn($attr) => ":$attr", $attributes);
        $sql = "INSERT INTO $tableName (" . implode(",", $attributes) . ") 
            VALUES (" . implode(",", $params) . ")";
        // Log the SQL query (for debugging)
        error_log("SQL Query: $sql");

        $statement = self::prepare($sql);
        foreach ($attributes as $attribute) {
            $statement->bindValue(":$attribute", $this->{$attribute});
        }
        $statement->execute();
        return true;
    }


    public static function findOne($where)
    {
        $tableName = static::tableName();
        $attributes = array_keys($where);
        $sql = implode(" AND ", array_map(fn($attr) => "$attr = :$attr", $attributes));
        // Log the SQL query (for debugging)
        error_log("SQL Query: $sql");
        $statement = self::prepare("SELECT * FROM $tableName WHERE $sql");
        foreach ($where as $key => $item) {
            $statement->bindValue(":$key", $item);
        }
        $statement->execute();
        return $statement->fetchObject(static::class);
    }

    public static function findById($id)
    {
        $tableName = static::tableName();
        $primaryKey = static::primaryKey();
        $statement = self::prepare("SELECT * FROM $tableName WHERE $primaryKey = :$primaryKey");
        $statement->bindValue(":$primaryKey", $id);
        $statement->execute();
        return $statement->fetchObject(static::class);

        //return $product ?: new static(); // Return an empty model if product is not found

    }

    public function findAll()
    {
        $tableName = static::tableName();
        $stmt = self::prepare("SELECT * FROM $tableName");
        $stmt->execute();

        // Fetch all rows as an associative array
        return $stmt->fetchAll();
    }

    public function getLatestProducts($limit = 3)
    {
        $tableName = $this->tableName();
        $query = "SELECT * FROM $tableName ORDER BY created_at DESC LIMIT :limit";
        $statement = self::prepare($query);
        $statement->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
    public static function findOneWithRoles($where)
    {
        $tableName = static::tableName();
        $attributes = array_keys($where);
        $sql = implode(" AND ", array_map(fn($attr) => "$attr = :$attr", $attributes));
        $statement = self::prepare("SELECT * FROM $tableName WHERE $sql");
        foreach ($where as $key => $item) {
            $statement->bindValue(":$key", $item);
        }
        $statement->execute();

        $user = $statement->fetchObject(static::class);

        if ($user) {
            // Fetch user roles using the fetchUserRolesFromDatabase method
            $userRoles = self::fetchUserRolesFromDatabase($user->id);

            // Set the roles to the user instance
            $user->roles = $userRoles;
        }

        return $user;
    }
    public function delete($id)
    {
        $tableName = static::tableName();
        $stmt = self::prepare("DELETE FROM $tableName WHERE id = :id");
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    public function update()
    {
        $tableName = $this->tableName();
        $attributes = $this->attributes();
        // create a string for the SQL update statement
        $params = array_map(fn($attr) => "$attr = :$attr", $attributes);
        // create the SQL update statement
        $sql = "UPDATE $tableName SET " . implode(", ", $params) . " WHERE id = :id";
        $statement = self::prepare($sql);
        // bind values to the parameters in the SQL statement
        foreach ($attributes as $attribute) {
            $statement->bindValue(":$attribute", $this->{$attribute});
        }
        // bind the id value
        $statement->bindValue(':id', $this->id);
        // execute the SQL statement
        $statement->execute();

        return true;
    }

    public static function findLatest($limit = 3)
    {
        $tableName = static::tableName();
        $statement = self::prepare("SELECT * FROM $tableName ORDER BY created_at DESC LIMIT :limit");
        $statement->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_CLASS, static::class);
    }
    public function getColumnValue($tableName, $columnName, $whereColumn, $whereValue): string {
        $db = Application::$app->db;
        $statement = $db->prepare("SELECT $columnName FROM $tableName WHERE $whereColumn = :value");
        $statement->bindValue(':value', $whereValue);
        $statement->execute();

        return $statement->fetchColumn() ?: '';
    }
}