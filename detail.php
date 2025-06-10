 <!DOCTYPE html>

<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>入力フォーム</title>
    <link rel = "stylesheet" href = "style.css?v=2">
</head>
<body>
    <h1>
        迷子ペット 情報交換掲示板
    </h1>
    <?php
    $reply_to_get = isset($_GET["reply_to"]) ? $_GET["reply_to"]:'';
    ?>
    
    <form action="" method="post" enctype="multipart/form-data">
        <input type="text" name="name" value="<?php if(isset($edit_name)) echo $edit_name; ?>" placeholder="名前">
        <textarea name="comment" placeholder="コメント" rows="5" cols ="40"><?php if(isset($edit_comment))echo htmlspecialchars($edit_comment,ENT_QUOTES,'UTF-8');?></textarea>
        <?php
        $button = empty($reply_to_get) ? '送信':'返信'; //reply_to_getが空なら"送信"、空でなければ"返信"という文字列を代入
        ?>
        <button type="submit" name="submit" >
            <?php echo $button;    //返信モードなら"返信"、新規投稿モードなら"送信"とボタンに表示される?>   
        </button>
        <input type="hidden" name="post_id" >   <!--今までの会話の履歴を取得するための隠しフォーム-->
        <input type="hidden" name="reply_to"value="<?php echo $reply_to_get;?>">
    </form>
    <a href="https://tech-base.net/tb-270029/m6/m6-2-pet.php"target="_blank">一覧に戻る</a>
 <?php
     //データベース接続
    $dsn = 'mysql:dbname=tb270029db;host=localhost';
    $user = 'tb-270029';
    $password = 'fzAfPbAVeg';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    
    $id = $_GET['detail_id'];   //遷移前のページで取得したidを受け継ぐ
    $post_id = $id;
    $reply_to_get = isset($_GET["reply_to"]) ? $_GET["reply_to"]:'';
    $reply_to= isset($_POST["reply_to"]) ? $_POST["reply_to"]:null;

    //遷移前に入力したidの情報をデータベースから取得
    $sql = 'SELECT * FROM tbtest4 WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $post_id, PDO::PARAM_INT);
    $stmt->execute();
    $results = $stmt->fetchAll();
    
    //名前とコメントの取得
    $name = isset($_POST["name"]) ? $_POST["name"] : "";
    $comment = isset($_POST["comment"]) ? $_POST["comment"] : "";
    
    
    //遷移前に入力したidのデータの詳細情報を出力するための表
    echo '<table border = "1">
            <tr>
            <th>ID</th>
            <th>名前</th>
            <th>コメント</th>
            <th>記入日</th>
            <th>画像</th>
            <th>見失った場所</th>
            <th>見失った日時</th>
            <th>連絡先</th>
            </tr>';
       
    //詳細情報の出力
    foreach ($results as $row){
            echo '<tr>';
            echo '<td>'.$row['id'].'</td>';
            echo '<td>'.$row['name'].'</td>';
            echo '<td>'.nl2br(htmlspecialchars($row['comment'],ENT_QUOTES,'UTF-8')).'</td>';    //改行込みで出力
            echo '<td>'.$row['date'].'</td>';
            echo '<td>';
            if(!empty($row['image'])){
                echo '<img src="'.$row['image'].'"width="100">';
            }
            else{
                echo "画像データなし";
            }
            echo '</td>';
            echo '<td>'.$row['place'].'</td>';
            $map_url = 'https://www.google.com/maps?q=' . urlencode($row['place']) . '&output=embed';
            echo '<br>';
            echo "<br>見失った場所(GoogleMap)";
            echo '<br><iframe width="300" height="200" frameborder="0" style="border:0" src="' . $map_url . '" allowfullscreen></iframe>';
            echo '<td>'.$row['lost_date'].'</td>';
            echo '<td>'.$row['address'].'</td>';
            echo '</tr>';
        }
    echo '<br>';
    echo '<br><p style="color:red;">詳細情報</p>';

    echo '</table>';
    if(!empty($name) && !empty($comment)){
        $sql = "INSERT INTO tbtest7 (name, comment,post_id,reply_to) VALUES (:name, :comment,:post_id,:reply_to)";
        $stmt = $pdo->prepare($sql);
        
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindParam(':post_id', $post_id, PDO::PARAM_STR);
        $stmt->bindParam(':reply_to', $reply_to, PDO::PARAM_INT);
        //実行
        $stmt->execute();
        header("Location:?detail_id=".$post_id);
        exit();
       
    }
    echo'<br>';
    
    //過去にやり取りした記録の取得
    $sql = 'SELECT * FROM tbtest7 WHERE post_id=:post_id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':post_id', $id, PDO::PARAM_STR);
    $stmt->execute();
    $results = $stmt->fetchAll();
    
    $comment_group =[];
    foreach($results as $comment){
        $parent_id = $comment['reply_to'];
        $comment_group[$parent_id][] = $comment;
    }
    
    
    function echo_comments($parent_id,$tree,$depth=0){
        if(!isset($tree[$parent_id]))return 0;  //過去の記録がない場合は何も表示しない
        foreach($tree[$parent_id] as $comment){
            echo '<div style="margin-left:'.($depth*20).'px; border-left:1px solid #ccc;padding-left:10px;margin-top:10px">';
            echo '<strong>'."投稿者(".$comment['name'].') '.'<strong>';
            echo "内容:".nl2br($comment['comment']);
            $current_reply=isset($_GET['reply_to']) ? $_GET['reply_to']:'';
            $new_reply=($current_reply==$comment['id'])?'':$comment['id'];
            echo '<a href="?detail_id='.$_GET['detail_id'].'&reply_to='.$new_reply.'">返信</a>';
            echo '</div>';
            echo_comments($comment['id'],$tree,$depth+1);   //再帰的にすることで返信におけるインデントを表現
        }
    }
    
    echo_comments(0,$comment_group);
    
    
    
    ?>
    </body>    
   
</html>
