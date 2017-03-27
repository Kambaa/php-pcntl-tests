<?php
/**
 * User: Kambaa
 * Date: 27.04.2016
 * Time: 23:40
 */
require_once 'Database.php';

class DbConnectionFactory
{
    private static $factory;
    
    private $db;

    public static function getFactory()
    {
        if (!self::$factory)
            self::$factory = new DbConnectionFactory();
        return self::$factory;
    }

    public function getConnection($host=null, $dbName=null, $username=null, $password=null) {
        if (!$this->db){
            $this->db = new Database($host,$dbName,$username,$password);
        }
        return $this->db ;
    }
}


























