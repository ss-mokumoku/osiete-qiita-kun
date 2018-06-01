<?php

require('web_db.php');
$db = new Database();
function def(){
    $db = new Database();
    $sql = "SELECT *
            FROM authors_tbl, articles_tbl, rss_history
            WHERE authors_tbl.permanent_id = articles_tbl.permanent_id
            AND articles_tbl.post_id = rss_history.post_id
            ORDER BY record_crated_at DESC
            LIMIT 20;";
    $res = $db->select($sql);

    $sql2 = "SELECT tag_name
         FROM tags_tbl, qiita_page_tags
         WHERE tags_tbl.tag_id = qiita_page_tags.tag_id
         AND qiita_page_tags.post_id = :post_id";
    $tagid = [];
    $tag_count = count($res);
    for($k=0;$k<$tag_count;$k++){
        $params = [':post_id'=>$res[$k]['post_id']];
        $sth = $db->pdo->prepare($sql2,$params);
        $sth->bindParam(':post_id',$params[":post_id"],PDO::PARAM_STR);
        $sth->execute();
        $tag_name[] = $sth->fetchAll(PDO::FETCH_ASSOC);
    }
    return [$res,$tag_name];
}

function kensaku(){
    $db = new Database();
    $name = htmlspecialchars($_POST['name'], ENT_QUOTES);

    $sql3 = "SELECT *
        FROM authors_tbl, articles_tbl ,qiita_page_tags ,tags_tbl
        WHERE authors_tbl.permanent_id = articles_tbl.permanent_id
        AND articles_tbl.post_id = qiita_page_tags.post_id
        AND tags_tbl.tag_id = qiita_page_tags.tag_id
        AND (articles_tbl.title LIKE '%:name%'
        OR articles_tbl.body LIKE '%:name%'
        OR tags_tbl.tag_name LIKE '%:name%')
			  GROUP BY qiita_page_tags.post_id";

    $sql8 = str_replace(":name", $name , $sql3);
    $sql9 = str_replace(":tag_name", $name , $sql8);

    $sth = $db->pdo->prepare($sql9);
    $sth->execute();
    $res = $sth->fetchAll(PDO::FETCH_ASSOC);

    $sql4 = "SELECT tag_name
         FROM tags_tbl, qiita_page_tags
         WHERE tags_tbl.tag_id = qiita_page_tags.tag_id
         AND qiita_page_tags.post_id = :post_id ";
    $tagid = [];
    $tag_count = count($res);
    for($k=0;$k<$tag_count;$k++){
        $params = [':post_id'=>$res[$k]['post_id']];
        $sth = $db->pdo->prepare($sql4,$params);
        $sth->bindParam(':post_id',$params[":post_id"],PDO::PARAM_STR);
        $sth->execute();
        $tag_name[] = $sth->fetchAll(PDO::FETCH_ASSOC);
    }
    return [$res,$tag_name];
}
?>
<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <title>教えてQiita君</title>
    <link href="web_kensaku.css" rel="stylesheet">
    <link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
</head>
<body>
    <div class="top" align="center">
        <h2>教えてQiita君</h2>
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
                <button><a href="csv.php">記事のCSV出力</a></button>
            </p>
            <?php

            if(isset($_POST['run_status'])){
              $input = preg_replace("/( |　)/","",$_POST['name']);
              if(!empty($input)){
                  $result = kensaku();
                  $name = htmlspecialchars($input, ENT_QUOTES);
                  echo mb_strimwidth($name,0,40,'...')."についての検索結果です";
              }else{
                  echo "検索用語を入力してください";
                  $result = def();
              }
            }else{
                $result = def();
            }

            ?>

            <?php
                $json_count = count($result[0]);
                for($j=0; $j<$json_count; $j++){
            ?>
            <div class="flex">
                <div class="image">
                    <a href = "https://qiita.com/<?php echo $result[0][$j]["name"]; ?>" target="_blank">
                        <img src="<?php echo $result[0][$j]['profile_image_url']; ?>">
                    </a>
                </div>
            <div class="kiji">
            <p>
                <a href = "https://qiita.com/<?php echo $result[0][$j]["name"]; ?>" target="_blank">
                    <?php echo $result[0][$j]['name']; ?>
                </a>
                    さんが
                    <?php echo $result[0][$j]['created_at']; ?>
                    に投稿
            </p>
            <p>
                <a href="<?php echo $result[0][$j]["url"]; ?>" target="_blank"><?php echo $result[0][$j]['title']; ?></a>
                </p>
            <p>
                <i class="far fa-thumbs-up" style = "color:green"></i>
                <?php echo $result[0][$j]['likes_count']; ?>
                <?php
                    $tag_n_count = count($result[1][$j]);
                    for($l=0; $l<$tag_n_count; $l++){
                ?>
                <div class="box13">
                    <?php
                        echo $result[1][$j][$l]['tag_name'];
                    ?>
                </div>
                    <?php
                        }
                    ?>
            </p>
                <div class="clear"></div>
                    <p>
                        <?php
                            $body = strip_tags($result[0][$j]['body']);
                            echo mb_strimwidth($body,0,80,'...');
                        ?>
                    </p>
                </div>
            </div>
                 <?php } ?>
                        </div>
                        <div class="right"></div>
            </div>
        </body>
</html>
