<?php
/**	@file
 *  @brief WEBに表示するためのmainファイル
 *
 *  @author SystemSoft Arita-takahiro
 *  @date 2018/05/31 最終更新
 */
//require '../src/popular_tags.php';
require '../src/database/web_SELECT.php';
require '../src/database/web_db.php'; //Databaseの基本クラス

$db = new Database();
$result = bunki($db);
$search_result_message = get_search_result_message();
$popular_person = select_popular_person($db);
//カウントの数が多い人ごとにランキングにする
//カウント数が同じならば同順にする
for ($i = 0; $i < count($popular_person); ++$i) {
    if (0 === $i) {
        $ranking = 1;
        $popular_person[0]['ranking'] = 1;
    } elseif ($popular_person[$i]['count'] === $popular_person[$i - 1]['count']) {
        $popular_person[$i]['ranking'] = $ranking;
    } else {
        $ranking = $ranking + 1;
        $popular_person[$i]['ranking'] = $ranking;
    }
}
//人気タグのランキングを配列にしたもの
$popular_tags = select_popular_tags($db);

//人気タグのランキングの配列を、タグの名前の配列とタグの数の配列の二つにわける
for ($i = 0; $i < 10; ++$i) {
    $popular_tags_name[] = $popular_tags[$i]['tag_name'];
}
for ($i = 0; $i < 10; ++$i) {
    $popular_tags_count[] = $popular_tags[$i]['count'];
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>教えてQiita君</title>

        <link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.4/Chart.min.js"></script>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- ↓左よせになる原因 -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
<!--        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script> -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
        <link href="css/web_kensaku.css" rel="stylesheet">
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
                <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#sampleModal" onclick='popular_tags_graph_window(<?php echo json_encode($popular_tags_name); ?>,<?php echo json_encode($popular_tags_count); ?>)'>
	            人気のタググラフ
                </button>
                    <!-- モーダル・ダイアログ -->
                <div class="modal fade" id="sampleModal" tabindex="-1">
	                <div class="modal-dialog modal-lg">
		                <div class="modal-content">
	　　　　　　　　　　　　  <div class="modal-header">
				                <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
				                <h4 class="modal-title">人気タグのグラフ</h4>
                            </div>
	                        <div class="modal-body">
                            　　<canvas id="myChart"></canvas>
                                    <!--<script>
                                        var tag_name = <?php echo  json_encode($popular_tags_name); ?>;
                                        var tag_count = <?php echo  json_encode($popular_tags_count); ?>;
                                        console.log(tag_name);
                                        console.log(tag_count);
                                        var ctx = document.getElementById("myChart").getContext('2d');
                                        var myChart = new Chart(ctx, {
                                            type: 'bar',
                                            data: {
                                                labels: tag_name,
                                                datasets: [{
                                                    label: 'タグ数',
                                                    backgroundColor: "rgba(0,200,1,.2)",
                                                    data: tag_count
                                                }]
                                            }
                                        });
                                    </script>-->
			                </div>
                        　　<div class="modal-footer">
				                <button type="button" class="btn btn-default" data-dismiss="modal">閉じる</button>
			                </div>
		                </div>
	                </div>
            　　</div>
    　　　　</div>

            <div class="center">
                <p>
                    <b>人気の記事</b>
                    <a href="/php_kiso/xml/web/src/csv.php"><button　type="button" class="btn btn-info btn-lg">記事のCSV出力</button></a>
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
                                    <div class="box_tag">
                                        <?php echo $result[1][$j][$l]['tag_name']; ?>
                                    </div>
                                    <?php
                        } ?>
                                </p>
                                <!-- タグ表示で利用していたfloatの解除を行います -->
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
            <div class="right">
                <p>
                    <b>人気投稿者</b>
                </p>
                <?php for ($j = 0; $j < count($popular_person); ++$j) {
                        ?>
                <div class="flex">
                    <!-- 順位 -->
                    <div class ="ranking_number">
                        <?php if (1 === $popular_person[$j]['ranking']) {
                            ?>
                            <i class="fas fa-trophy fa-gold"></i>
                            <!-- <span class="fa-stack fa-lg">
                                <span class="fa fa-trophy fa-stack-2x"></span>
                                <span class="fa fa-stack-1x">1</span>
                            </span> -->
                        <?php
                        } elseif (2 === $popular_person[$j]['ranking']) {
                            ?>
                            <i class="fas fa-trophy fa-silver"></i>
                        <?php
                        } elseif (3 === $popular_person[$j]['ranking']) {
                            ?>
                            <i class="fas fa-trophy fa-bronze"></i>

                        <?php
                        } else {
                            ?>

                            <i class="fas fa-trophy fa-gray"></i>

                        <?php
                        } ?>
                        <!-- フリー素材の画像を使った場合のコード　<img src="ranking_image/ranking<?php echo $popular_person[$j]['ranking']; ?>.png" > -->
                    </div>
                    <!-- 人気投稿者の写真 -->
                    <div class ="popular_image">
                        <a href = "https://qiita.com/<?php echo $popular_person[$j]['name']; ?>" target="_blank">
                            <img src="<?php echo $popular_person[$j]['profile_image_url']; ?>">
                        </a>
                        <p>
                            <a href = "https://qiita.com/<?php echo $popular_person[$j]['name']; ?>" target="_blank">
                                <?php echo $popular_person[$j]['name']; ?>
                            </a>
                        </p>
                    </div>
                </div>
                <?php
                    }
                ?>
            </div>
        </div>
        <script src="javascript/module_window.js"></script>

    </body>
</html>
