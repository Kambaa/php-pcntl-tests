<?php
/**
 * User: Kambaa
 * Date: 27.04.2016
 * Time: 23:40
 */

namespace ExtreApp\Utils;

use Exception;
use PDO;

class Database
{
    /**
     * @var null when we don't want to use placeholder on our queries, we can use it to set it null.s
     */
    public $NOPLACEHOLDER = null;

    /**
     * @var PDO Database connection handler
     */
    protected $dbch;

    public function __construct($hostname,$dbName,$username,$password)
    {
        $dsn = "mysql:host=$hostname;dbname=$dbName;charset=utf8mb4";
        $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
        return $this->dbch = new PDO($dsn, $username, $password, $options);
    }


    public function select($query, $datas = null, $fetchClassName = null)
    {
        if (null === $datas) {
            $stmt = $this->dbch->query($query);
        } else {
            $stmt = $this->dbch->prepare($query);
            foreach ($datas as $k => $v) {
                if (is_array($v)) {
                    $stmt->bindValue(":$k", $v[0], $v[1]);
                } else {
                    $stmt->bindValue(":$k", $v);
                }
            }
            $stmt->execute();
        }
        if (null === $fetchClassName) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return $stmt->fetchAll(PDO::FETCH_CLASS, $fetchClassName);
        }
    }

    public function delete($table_name, $id)
    {
        $stmt = $this->dbch->prepare("DELETE FROM $table_name WHERE id= :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function insert($table_name, $data)
    {
        if (!is_array($data)) {
            throw new Exception("Insertion data null");
        }
        $sqlFields = implode(",", array_keys($data));
        $sqlPlaceHolders = implode(",", array_map(function ($v) {
            return ":" . $v;
        }, array_keys($data)));
        $sql = "INSERT INTO $table_name($sqlFields) VALUES($sqlPlaceHolders)";
        $stmt = $this->dbch->prepare($sql);
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                $stmt->bindValue(":$k", $v[0], $v[1]);
            } else {
                $stmt->bindValue(":$k", $v);
            }
        }
        $stmt->execute();
        return $this->dbch->lastInsertId();
    }

    public function update($table_name, $data)
    {
        if (!is_array($data)) {
            throw new Exception("Insertion data null");
        }

        $sqlUpdateFields = implode(",", array_map(function ($v) {
            return "$v = :$v";
        }, array_keys($data)));

        $sql = "UPDATE $table_name SET $sqlUpdateFields WHERE id = :id";
        $stmt = $this->dbch->prepare($sql);
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                $stmt->bindValue(":$k", $v[0], $v[1]);
            } else {
                $stmt->bindValue(":$k", $v);
            }
        }
        $this->dbch->beginTransaction();
        $stmt->execute();
        $rc = $stmt->rowCount();
        $this->dbch->commit();
        return $rc;
    }

    public function disconnect()
    {
        $this->dbch = null;
    }
    
    public function getConnectionHandler(){
        return $this->dbch;
    }
}