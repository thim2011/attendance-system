<?php 
    require_once dirname(__FILE__).'/../Utils/InfoDAL.php';

    class PolicyDAL extends InfoDAL
    {
        // 實現父類別的抽象方法。
        public function getTableName()
        {
            return "`policy`";
        }
        //--------------------------------------------------------------------------
        // 實現父類別的抽象方法。
        public function getPrimaryKey()
        {
            return 'Policy_id';
        }

        public function insertPolicy($chapter, $article)
        {
            $sql = "INSERT INTO policy (Chapter, Article) VALUES (:chapter, :article)";
            $params = array(':chapter' => $chapter, ':article' => $article);
         
            return parent::insert($sql, $params); 
        }

        public function getPolicy($article){
            $sql = "SELECT * FROM policy WHERE Article LIKE :article";
            $params = array(':article' => '%' . $article . '%');
            return parent::query($sql, $params);
        }
    }
?>