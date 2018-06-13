<?php
/**	@file
 *  @brief WEBに表示するためのmainファイル
 *
 *  @author SystemSoft Arita-takahiro
 *  @date 2018/05/31 最終更新
 */
require '/var/www/html/php_kiso/xml/web/src/database/web_SELECT.php';
require '/var/www/html/php_kiso/xml/web/src/database/web_db.php'; //Databaseの基本クラス
$db = new Database();
$result = bunki($db);
$search_result_message = get_search_result_message();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>教えてQiita君</title>
        <link href="/php_kiso/xml/web/webroot/css/web_kensaku.css" rel="stylesheet">
        <link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
    </head>
    <body>
        <div class="top" align="center">
            <font color="White"><h2>教えてQiita君</h2></font>
        </div>
        <div class="flex">
            <div class="left">
                <form action="web_main.php" method="post">フリーワード検索<br />
                    <input type="text" name="name" />
                    <input type="text" name="run_status" value =1 hidden />
                    <input type="submit" name="submit" value="検索する" />
                </form>
            </div>

            <div class="center">
                <p>
                    <b>人気の記事</b>
                    <button><a href="/php_kiso/xml/web/src/csv.php">記事のCSV出力</a></button>
                </p>
                <?php
                    echo $search_result_message;
                    // 人気記事20件の情報をWEB上に表示する処理　そのため20回ループを行っている
                    for ($j = 0; $j < count($result[0]); ++$j) {
                        ?>
                        <div class="flex">
                            <div class="image">
                                <a href = "https://qiita.com/<?php echo $result[0][$j]['name']; ?>" target="_blank">
                                    <img src="<?php echo $result[0][$j]['profile_image_url']; ?>">
                                </a>
                            </div>
                            <div class="kiji">
                                <p>
                                    <a href = "https://qiita.com/<?php echo $result[0][$j]['name']; ?>" target="_blank">
                                        <?php echo $result[0][$j]['name']; ?>
                                    </a>
                                    さんが
                                    <?php echo $result[0][$j]['created_at']; ?>
                                    に投稿
                                </p>
                                <p>
                                    <a href="<?php echo $result[0][$j]['url']; ?>" target="_blank"><?php echo $result[0][$j]['title']; ?></a>
                                </p>
                                <p>
                                    <i class="far fa-thumbs-up" style = "color:green"></i>
                                    <?php echo $result[0][$j]['likes_count']; ?>
                                    <?php for ($l = 0; $l < count($result[1][$j]); ++$l) {
                            ?>
                                    <div class="box13">
                                        <?php echo $result[1][$j][$l]['tag_name']; ?>
                                    </div>
                                    <?php
                        } ?>
                                </p>
                                <div class="clear"></div>
                                <p>
                                    <?php $body = strip_tags($result[0][$j]['body']);
                        echo mb_strimwidth($body, 0, 80, '...'); ?>
                                </p>
                            </div>
                        </div>
                        <?php
                    } ?>
            </div>
            <div class="right"></div>
        </div>
    </body>
</html>
