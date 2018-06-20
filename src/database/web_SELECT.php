<?php
/**	@file
 *  @brief 記事検索関係の関数を扱うファイル
 *
 *  @author SystemSoft Arita-takahiro
 *  @date 2018/06/12 新規作成
 *
 * @param mixed $rss_data
 * @param mixed $db
 */

/**
 *  DBに登録されている、最新20件のQiitaの投稿情報を取得する.
 *
 *  @date 2018/05/31 takahiro-arita
 *  @note
 *  特にありません。
 *
 *  @param mixed $db データベースクラス
 *
 *  @return        $res 記事情報をデータベースから取得したものを連想配列にしている
 *                 $tag_name　記事のタグ情報をデータベースから取得したものを連想配列にしている
 */
function def($db)
{
    $sql = 'SELECT *
            FROM authors_tbl, articles_tbl, rss_history
            WHERE authors_tbl.permanent_id = articles_tbl.permanent_id
            AND articles_tbl.post_id = rss_history.post_id
            ORDER BY record_crated_at DESC
            LIMIT 20;';
    $res = $db->select($sql);
    $tag_name = get_tags($db, $res);

    return [$res, $tag_name];
}
/**
 *  フリーワードで検索した後のページを表示するための関数.
 *
 *  @date 2018/05/31 takahiro-arita
 *  @note
 *  特にありません。
 *
 *  @param mixed $db
 *
 *  @return        $res 記事情報をデータベースから取得したものを連想配列にしている
 *                 $tag_name　記事のタグ情報をデータベースから取得したものを連想配列にしている
 */
function kensaku($db)
{
    $name = '%'.$_POST['name'].'%';
    $sql3 = 'SELECT *
        FROM authors_tbl, articles_tbl ,qiita_page_tags ,tags_tbl
        WHERE authors_tbl.permanent_id = articles_tbl.permanent_id
        AND articles_tbl.post_id = qiita_page_tags.post_id
        AND tags_tbl.tag_id = qiita_page_tags.tag_id
        AND (articles_tbl.title LIKE :name
        OR articles_tbl.body LIKE :name
        OR tags_tbl.tag_name LIKE :name)
			  GROUP BY qiita_page_tags.post_id';
    $sth = $db->pdo->prepare($sql3);
    $sth->bindParam(':name', $name, PDO::PARAM_STR);
    $sth->execute();
    $res = $sth->fetchAll(PDO::FETCH_ASSOC);
    $tag_name = get_tags($db, $res);

    return [$res, $tag_name];
}

/**
 *  フリーワードで検索した後のページを表示するための関数.
 *
 *  @date 2018/05/31 takahiro-arita
 *
 *  @note
 *  特にありません。
 *
 *  @param mixed $db  データベースクラス
 *  @param mixed $res データベースから取得した記事情報の連想配列
 *
 *  @return        $tag_name　記事のタグ情報をデータベースから取得したものを連想配列にしている
 */
function get_tags($db, $res)
{
    $sql2 = 'SELECT tag_name
             FROM tags_tbl, qiita_page_tags
             WHERE tags_tbl.tag_id = qiita_page_tags.tag_id
             AND qiita_page_tags.post_id = :post_id';
    $sth = $db->pdo->prepare($sql2);
    for ($k = 0; $k < count($res); ++$k) {
        $params = [':post_id' => $res[$k]['post_id']];
        $sth->bindParam(':post_id', $params[':post_id'], PDO::PARAM_STR);
        $sth->execute();
        $tag_name[] = $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    return $tag_name;
}

/**
 *　入力された文字列にたいする検索結果を返す関数.
 *
 *  @date 2018/05/31 takahiro-arita
 *
 *  @note
 *  特にありません。
 *
 *  @param mixed $db  データベースクラス
 *
 *  @return     $result 人気記事20件の連想配列
 */
function bunki($db)
{
    if (isset($_POST['run_status'])) {
        $input = preg_replace('/( |　)/', '', $_POST['name']);
        if (!empty($input)) {
            $result = kensaku($db);
        } else {
            $result = def($db);
        }
    } else {
        $result = def($db);
    }

    return $result;
}

/**
 * 検索結果のメッセージを返す関数.
 */
function get_search_result_message()
{
    if (isset($_POST['run_status'])) {
        $input = preg_replace('/( |　)/', '', $_POST['name']);
        if (!empty($input)) {
            $name = htmlspecialchars($input, ENT_QUOTES);

            return mb_strimwidth($name, 0, 40, '...').'についての検索結果です';
        }

        return '検索用語を入力してください';
    }

    return '';
}

function select_popular_person($db)
{
    $sql = 'SELECT name, profile_image_url, articles_tbl.permanent_id , count(*) as count
             from articles_tbl inner join authors_tbl on articles_tbl.permanent_id = authors_tbl.permanent_id
             WHERE record_created_at > (now() - INTERVAL 3650 DAY)
             group by permanent_id order by count desc, name asc limit 5';
    $sth = $db->pdo->prepare($sql);
    $sth->execute();

    return $sth->fetchAll(PDO::FETCH_ASSOC);
}

function select_popular_tags($db)
{
    $sql = 'SELECT tag_name, count(*) as count
            FROM authors_tbl, articles_tbl ,qiita_page_tags ,tags_tbl
            WHERE authors_tbl.permanent_id = articles_tbl.permanent_id
            AND articles_tbl.post_id = qiita_page_tags.post_id
            AND tags_tbl.tag_id = qiita_page_tags.tag_id
            AND record_created_at > (now() - INTERVAL 3650 DAY)
            group by tag_name order by count desc, tag_name asc limit 10';
    $sth = $db->pdo->prepare($sql);
    $sth->execute();

    return $sth->fetchAll(PDO::FETCH_ASSOC);
}
