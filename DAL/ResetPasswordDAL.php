<?php 
    require_once dirname(__FILE__).'/../Utils/InfoDAL.php';

    class ResetPasswordDAL extends InfoDAL
    {
        // 實現父類別的抽象方法。
        public function getTableName()
        {
            return "`reset_password`";
        }
        //--------------------------------------------------------------------------
        // 實現父類別的抽象方法。
        public function getPrimaryKey()
        {
            return 'Reset_id';
        }

        public function insertResetPassword($resetPassword){
            $sql = "INSERT INTO " .$this->getTableName()." (Email, Token, Expired_at) 
                    VALUES (:Email, :Token, :Expire_at);";
            $params = array(':Email'  => $resetPassword['Email'], 
                            ':Token'  => $resetPassword['Token'],
                            ':Expire_at' => date('Y-m-d H:i:s', strtotime('+20 minutes')));
            return parent::insert($sql, $params);
        }

        public function getEmailbyToken($token){
            $sql = "SELECT * FROM " .$this->getTableName()." WHERE Token = :token And Expired_at > NOW();";
            $params = array(':token' => $token);
            $result= parent::getOne($sql, $params);
            return $result;
        }
    }