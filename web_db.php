<?php

/**	@file
 *  @brief データベースの基本クラス
 *
 *  @author SystemSoft Arita-takahiro
 *  @date 2018/05/31 最終更新
 */
class Database
{
    public $pdo;
    private $host = 'localhost';
    private $user = 'root';
    private $pass = 'systemsoftkensyu2018';
    private $databaseName = 'qiita_kadai';

    /**
     *  @brief コンストラクタ
     *
     *  @date 2018/05/31 takahiro-arita
     *
     *  @note
     *  特にありません。
     *
     *  @return
     */
    public function __construct()
    {
        // MySQLに接続する
        $this->pdo = new PDO('mysql:host=localhost;dbname=qiita_kadai', 'root', 'systemsoftkensyu2018');
    }

    /**
     *  @brief データベースからSELECTする
     *
     *  @date 2018/05/31 takahiro-arita
     *
     *  @note
     *  特にありません。
     *
     * @param string $sql
     * @param array  $params
     *
     * @return fetchALLの結果の連想配列
     */
    public function select($sql, array $params = [])
    {
        $sth = $this->pdo->prepare($sql);
        $sth->execute();

        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }
}
