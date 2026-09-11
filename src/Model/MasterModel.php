<?php

namespace BioGuppy\Model;

use BioGuppy\Lib\Connection;
use PDO;

class MasterModel extends Connection
{
    public function insert(string $sql, array $params = [])
    {
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute($params);

        return $stmt;
    }

    public function select(string $sql, array $params = [])
    {
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute($params);

        return $stmt;
    }

    public function update(string $sql, array $params = [])
    {
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute($params);

        return $stmt;
    }

    public function delete(string $sql, array $params = [])
    {
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute($params);

        return $stmt;
    }
    public function findOne(string $table, string $fields, string $condition)
    {
        $sql = "SELECT $fields FROM $table WHERE $condition";
        $stmt = $this->getConnection()->query($sql);
        if ($stmt->rowCount() > 0) {
            return $stmt;
        } else {
            return "No se encontro ningun registro";
        }
    }
    public function autoincrement($table, $field)
    {
        $sql = "SELECT MAX($field) FROM $table";
        $stmt = $this->getConnection()->query($sql);
        $max_id = $stmt->fetch(PDO::FETCH_NUM);
        return ($max_id[0] ?? 0) + 1;
    }
}
