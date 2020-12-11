<?php
/**
 * Query Builder
 */

namespace App\Core\Database;

use PDO;
use PDOException;

class QueryBuilder
{

    protected $db;
    protected $stmt;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function query($sqlQuery)
    {
        $this->stmt = $this->db->prepare($sqlQuery);
    }

    public function bind($param, $value, $type = null)
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    public function setFetchMode($mode, $param = '')
    {
        if(! empty($param))
            $this->stmt->setFetchMode($mode, $param);
        else
            $this->stmt->setFetchMode($mode);
    }

    public function execute($parameters = [])
    {
        if(! empty($parameters))
            return $this->stmt->execute($parameters);
        else
            return $this->stmt->execute();
    }

    public function getColumns($table)
    {
        $this->query("SHOW COLUMNS FROM {$table}");
        return $this->fetchAll();
    }

    public function fetchAll($intoClass = '')
    {
        $this->execute();
        if($intoClass)
            return $this->stmt->fetchAll(PDO::FETCH_CLASS, $intoClass);
        else
            return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function fetchSingle($intoClass = '')
    {
        $this->execute();
        if($intoClass) {
            $this->setFetchMode(PDO::FETCH_CLASS, $intoClass);
            return $this->stmt->fetch(PDO::FETCH_CLASS);
        } else
            return $this->stmt->fetch(PDO::FETCH_OBJ);
    }

    public function count()
    {
        return $this->stmt->rowCount();
    }

    public function selectAll($table, $intoClass = '')
    {
        $this->query("select * from {$table}");
        return $this->fetchAll($intoClass);
    }

    public function find($table, $where, $values, $intoClass = '')
    {
        $sql = sprintf('select * from %s where %s LIMIT 1', $table, $where);
        $this->query($sql);

        foreach ($values as $key => $value) {
            $this->bind(":".$key, $value);
        }

        return $this->fetchSingle($intoClass);
    }

    public function delete($table, $where, $whereValues)
    {
        $sql = sprintf('delete from %s where %s', $table, $where);
        $this->query($sql);

        foreach ($whereValues as $key => $value) {
            $this->bind(":".$key, $value);
        }

        $this->execute();
    }

    public function insert($table, $values)
    {
        $values = get_object_vars($values);

        $this->query( sprintf(
            'insert into %s (%s) values (%s)',
            $table,
            implode(', ', array_keys($values)),
            ':'. implode(', :', array_keys($values))
        ));

        $this->execute($values);
    }

    public function update($table, $values, $where, $whereValues)
    {
        $sql = sprintf('update %s set',$table);

        foreach($values as $key => $value) {
            if (is_numeric($value)) {
                $vals[] = sprintf('%s=%d', $key, $value);
            } else {
                $vals[] = sprintf("%s='%s'", $key, $value);
            }
        }

        $this->query($sql.=sprintf(' %s WHERE %s', implode(',',$vals), $where));

        foreach ($whereValues as $key => $value) {
            $this->bind(":".$key, $value);
        }

        $this->execute();
    }

    public function lastId() {
        return $this->db->lastInsertId();
    }
}
