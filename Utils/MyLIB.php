<?php

class MyLIB{

    // empty()函数是用来测试变量是否已经配置。
    // 若变量已存在、非空字符串或者非零，则返回 false 值；反之返回 true值。
    public static function GetNumber($KeyName)
    {
        if( isset($_POST[$KeyName]) )
        {
            $value = $_POST[$KeyName];
            if( is_numeric($value) )
                return $value;
            else
                return 0;
        }
        else
            return 0;
    }
    //--------------------------------------------------------------------------
    //  讀取的資料，必須經過轉換，將 ' " & < > 轉換成 HTML 的特殊字元。
    public static function GetString($KeyName, $MaxLen=null, $bFromRight=null)
    {
        if( isset($_POST[$KeyName]) )
        {
            $value = trim($_POST[$KeyName]);
            // 去除斜杠
			$value = stripslashes($value);

            // 若超過最大長度，則切除多餘部份。
            if( $MaxLen != null && strlen($value) > $MaxLen)
            {
                if( $bFromRight == null )
                    $nStart = 0;
                else
                {
                    $nStart = strlen($value)-$MaxLen;
                }
                $value = substr($value, $nStart, $MaxLen);
            }
            return htmlspecialchars($value, ENT_QUOTES);
        }
        else
            return null;
    }
}
?>