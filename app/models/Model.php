<?php
/**
 * Base Model
 */

namespace App\Model;

use App\Core\App;
use App\Traits\JsonSerializer;

class Model implements \JsonSerializable
{
    use JsonSerializer;

    protected $db;

    public function __construct()
    {
        $this->db = App::get('database');
    }

    public static function table()
    {
        return get_called_class()::$table;
    }

    public function get_object_vars()
    {
        return get_object_vars($this);
    }

    public function save()
    {
        // Insert if id is null
        if( is_null($this->id) )
        {
            $this->db->insert(self::table(), $this);
            $this->id = $this->db->lastId();
            $this->getTimestamps();
        }
        // Update otherwise
        else
        {
            $this->db->update(self::table(), $this, "id=:id", $this->id);
        }

        return $this;
    }

    public function delete()
    {
        $this->db->query("delete from ".self::table()." where id=:id");
        $this->db->bind(":id", $this->id);

        $this->db->execute();
    }

    public static function find($whereValues, $where = "id=:id")
    {
        $instance = new static;
        $type = gettype($whereValues);

        switch ($type) {
            case 'integer':
                $key = trim(explode("=", $where)[0]);
                return $instance->db->find(self::table(), $where, [$key => $whereValues], get_called_class());
                break;
            case 'array':
                return $instance->db->find(self::table(), $where, $whereValues, get_called_class());
                break;
            default: //integer
                $key = trim(explode("=", $where)[0]);
                return $instance->db->find(self::table(), $where, [$key => $whereValues], get_called_class());
        }

    }

    public static function deleteALl($whereValues, $where = "id=:id")
    {
        $instance = new static;
        $type = gettype($whereValues);

        switch ($type) {
            case 'integer':
                $key = trim(explode("=", $where)[0]);
                return $instance->db->delete(self::table(), $where, [$key => $whereValues]);
                break;
            case 'array':
                return $instance->db->delete(self::table(), $where, $whereValues);
                break;
            default: //integer
                $key = trim(explode("=", $where)[0]);
                return $instance->db->delete(self::table(), $where, [$key => $whereValues]);
        }

    }

    public static function update($values, $whereValues, $where = "id=:id")
    {
        $instance = new static;
        $type = gettype($whereValues);

        switch ($type) {
            case 'integer':
                $key = trim(explode("=", $where)[0]);
                return $instance->db->update(self::table(), $values, $where, [$key => $whereValues]);
                break;
            case 'array':
                return $instance->db->update(self::table(), $values, $where, $whereValues);
                break;
            default: //integer
                $key = trim(explode("=", $where)[0]);
                return $instance->db->update(self::table(), $values, $where, [$key => $whereValues]);
        }

    }

    public static function all()
    {
        $instance = new static;
        return $instance->db->selectAll(self::table(), get_called_class());
    }

    public function getTimestamps()
    {
        $this->db->query("select created_at, updated_at from ".self::table()." where id=:id");
        $this->db->bind(":id", $this->id);

        $result = $this->db->fetchSingle();
        $this->created_at = $result->created_at;
        $this->updated_at = $result->updated_at;
    }

    public function getIdentifier()
    {
        return $this->id;
    }
}
