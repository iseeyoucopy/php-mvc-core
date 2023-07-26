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

    public static function primaryKey(): string
    {
        return 'id';
    }

    public function save()
    {
        $tableName = $this->tableName();
        $attributes = $this->attributes();
        $params = array_map(fn($attr) => ":$attr", $attributes);
        $statement = self::prepare("INSERT INTO $tableName (" . implode(",", $attributes) . ") 
                VALUES (" . implode(",", $params) . ")");
        foreach ($attributes as $attribute) {
            $statement->bindValue(":$attribute", $this->{$attribute});
        }
        $statement->execute();
        return true;
    }

    public static function prepare($sql): \PDOStatement
    {
        return Application::$app->db->prepare($sql);
    }

    public static function findOne($where)
    {
        $tableName = static::tableName();
        $attributes = array_keys($where);
        $sql = implode("AND", array_map(fn($attr) => "$attr = :$attr", $attributes));
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
        $product = $statement->fetchObject(static::class);

        return $product ?: new static(); // Return an empty model if product is not found
    }

    public function findAll()
    {
        $tableName = static::tableName();
        $stmt = self::prepare("SELECT * FROM $tableName");
        $stmt->execute();

        // Fetch all rows as an associative array
        return $stmt->fetchAll();
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
}