<?php
/**	@file
 *  @brief CSVファイル出力
 *
 *  @author SystemSoft Arita-takahiro
 *  @date 2018/05/31 最終更新
 */
require '../src/database/web_db.php';
$db = new Database();
// 出力情報の設定
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename=qiita.csv');
header('Content-Transfer-Encoding: binary');

// 変数の初期化
$member = [];
$csv = null;

// 出力したいデータのサンプル
$csv_sql = 'SELECT *
        FROM authors_tbl, articles_tbl ,qiita_page_tags ,tags_tbl
        WHERE authors_tbl.permanent_id = articles_tbl.permanent_id
        AND articles_tbl.post_id = qiita_page_tags.post_id
        AND tags_tbl.tag_id = qiita_page_tags.tag_id';
$sth = $db->pdo->prepare($csv_sql);
$sth->execute();
$csv_array = $sth->fetchAll(PDO::FETCH_ASSOC);

// 1行目のラベルを作成
$csv_data = '"permanent_id","user_id","name","profile_image_url","description","location","organization","followees_count","followers_count","items_count","github_login_name","linkedin_id","facebook_id","twitter_screen_name","website_url","post_id","url","title","likes_count","private","page_views_count","comments_count","reactions_count","coediting","created_at","updated_at","record_created_at","tag_id","tag_name","user_id"'."\n";

// 出力データ生成
foreach ($csv_array as $value) {
    $csv_data .= '"'.$value['permanent_id'].'","'.
    $value['user_id'].'", "'.
    $value['name'].'","'.
    $value['profile_image_url'].'","'.
    $value['description'].'","'.
    $value['location'].'","'.
    $value['organization'].'","'.
    $value['followees_count'].'","'.
    $value['followers_count'].'","'.
    $value['items_count'].'","'.
    $value['github_login_name'].'","'.
    $value['linkedin_id'].'","'.
    $value['facebook_id'].'","'.
    $value['twitter_screen_name'].'","'.
    $value['website_url'].'","'.
    $value['post_id'].'","'.
    $value['url'].'","'.
    $value['title'].'","'.
//    $value['body'] .'","' .
    $value['likes_count'].'","'.
    $value['private'].'","'.
    $value['page_views_count'].'","'.
    $value['comments_count'].'","'.
    $value['reactions_count'].'","'.
    $value['coediting'].'","'.
    $value['created_at'].'","'.
    $value['updated_at'].'","'.
    $value['record_created_at'].'","'.
    $value['tag_id'].'","'.
    $value['tag_name'].'","'.
    $value['user_id'].'"'.
    "\n";
}
$csv_data = mb_convert_encoding($csv_data, 'sjis-win', 'auto');

// CSVファイル出力
echo $csv_data;

return;
