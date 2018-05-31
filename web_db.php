<?php

/**
* Databaseの基本クラス
*/
class Database
{
    private $host = 'localhost';
    private $user = 'root';
    private $pass = 'systemsoftkensyu2018';
    private $databaseName = 'qiita_kadai';
    public $pdo = null;

    public function __construct()
    {
       $this->pdo = new PDO('mysql:host=localhost;dbname=qiita_kadai' , 'root' , 'systemsoftkensyu2018');
    // MySQLに接続する
    }

    public function select($sql, array $params = [])
    {
        $sth = $this->pdo->prepare($sql);
        $sth->execute();
        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }


function web_select($data2){
    $db = new Database();
//タグがすでにタグ管理テーブルに登録されていたらIDを返す
    $sql = "select tag_id from authors_tbl" ;

        $sth = $this->pdo->prepare($sql);
        $sth->execute();
    }
}
?>
