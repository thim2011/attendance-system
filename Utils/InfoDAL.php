<?php
require_once dirname(__FILE__).'/../Utils/DBConnect.php';

abstract class InfoDAL
{
    private $pdo = null;

    abstract public function getTableName();
    abstract public function getPrimaryKey();

    public function __construct() 
    {
        $this->pdo = DBConnect::getInstance()->getConnection();
    }

    /**
     * 取得資料表的多筆資料
     * @return array
     */
    public function query($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $results = array();
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $results[] = $row;
        }

        return $results;
    }

    /**
     * 取得資料表的單筆資料
     * @return object
     */
    public function getOne($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result ? $result : "";
    }

    /**
     * 取得資料表的數量
     * @return int
     */
    public function count($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $count = $stmt->fetchColumn();

        return $count !== false ? (int)$count : 0;
    }

    
    public function insert($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function update($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }
    
    public function getLastInsertId() {
        return $this->pdo->lastInsertId();
    }

    public function RollBack() {
        $this->pdo->rollBack();
    }
     
    public function BeginTransaction() {
        $this->pdo->beginTransaction();
    }

    public function Commit() {
        $this->pdo->commit();
    }
}
?>
