<?php
class DBConnect
{
    private static $instance;
    private $connection;

    private function __construct()
    {
        //port=$port
        require_once 'DBpassword.php';
        try {
            $dsn = "mysql:host=$host;dbname=$database;charset=utf8;";
            $this->connection = new PDO($dsn, $username, $password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new DBConnect();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function closeConnection()
    {
        $this->connection = null;
    }

    public function __destruct()
    {
        $this->closeConnection();
    }

    private function __clone(){}
}

?>