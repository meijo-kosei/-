<!DOCTYPE html>

<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>入力フォーム</title>
    <link rel = "stylesheet" href = "style.css?v=2">
</head>
<body>
   <h1>
       迷子ペット相談所
   </h1>
   
    <?php
            //データベース接続
            $dsn = 'mysql:dbname=tb270029db;host=localhost';
            $user = 'tb-270029';
            $password = 'fzAfPbAVeg';
            $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        
            //フォームから入力したデータの取得
            $name = isset($_POST["name"]) ? $_POST["name"] : "";
            $comment = isset($_POST["comment"]) ? $_POST["comment"] : "";
            $delete_id = isset($_POST["delete_id"]) ? $_POST["delete_id"] : "";
            
            $edit_id = isset($_POST["edit_id"]) ? $_POST["edit_id"] : "";
            $edit_target_id = isset($_POST["edit_target_id"]) ? $_POST["edit_target_id"] : "";
            
            $new_password = isset($_POST["new_password"]) ? $_POST["new_password"] : "";
            $delete_password = isset($_POST["delete_password"]) ? $_POST["delete_password"] : "";
            $edit_password = isset($_POST["edit_password"]) ? $_POST["edit_password"] : "";
  
            $place = isset($_POST["place"]) ? $_POST["place"] : "";
            $lost_date = isset($_POST["lost_date"]) ? $_POST["lost_date"] : "";
            $address = isset($_POST["address"]) ? $_POST["address"] : "";
            
            //削除処理
            if(!empty($delete_id) &&!empty($delete_password)){
                 //入力したidと同じデータの取得
                $sql = "SELECT * FROM tbtest4 WHERE id=:id";   
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);
                $stmt->execute();
                $row = $stmt->fetch();
                
                //入力したパスワードがあっている場合の処理
                if($row && $row['password'] == $delete_password){
                    //入力したidと同じデータを削除
                    $sql = 'delete from tbtest4 where id=:id';
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);
                     
                    //実行
                    $stmt->execute();
                    
                }
                //入力したパスワードが間違っている場合の処理
                else{
                     echo '<p style="color:red;">パスワードが違います</p>';
                }
            }
            
            //編集処理
            if(!empty($edit_target_id) && !empty($name) && !empty($comment) ){
                    //入力したidと同じデータを取得
                $sql = "SELECT * FROM tbtest4 WHERE id=:id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $edit_target_id, PDO::PARAM_INT);
                $stmt->execute();
                $row = $stmt->fetch();
                $image=$row['image'];
                
                if(!empty($_FILES['image']['name']) && $_FILES['image']['error']==0){   //ファイル送信が空でなく、エラーがない場合
                    $upload_dir="uploads/"; //ファイルの保存先
                    if(!is_dir($upload_dir)){   //uploadsフォルダがが存在しない場合
                        mkdir($upload_dir,0777,true);   //新規作成
                    }
                    $filename = basename($_FILES['image']['name']);     //ファイル名を取得
                    $target_file = $upload_dir.time()."_".$filename;    //重複を防ぐためのtime関数を使用し、完全なパス名を取得
                    
                    
                    if(move_uploaded_file($_FILES['image']['tmp_name'],$target_file)){    //元のパスから編集後のパスに移動できた場合の処理
                        $image = $target_file;
                        
                        if(!empty($row['image']) && file_exists($row['image'])){    //古いデータが存在していた場合の処理
                            unlink($row['image']);  //古い写真データ削除
                        }
                    }
                    else{
                        echo '<p style="color:red;">画像の変更に失敗しました</p>';
                    }
                }
               //入力したidのデータ編集
                $sql = "UPDATE tbtest4 SET name=:name, comment=:comment,image=:image WHERE id=:id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt->bindParam(':image', $image, PDO::PARAM_STR);
                $stmt->bindParam(':id', $edit_target_id, PDO::PARAM_INT);
                $stmt->execute();
                $edit_target_id = "";   //１度編集した後に新規投稿できるようにする
            }
            
            //新規投稿処理
            else if(empty($edit_target_id) && !empty($name) && !empty($comment) && !empty($new_password)){
                $image = "";
                if(!empty($_FILES['image']) && $_FILES['image']['error'] == 0){
                    $upload_dir="uploads/";
                    if(!is_dir($upload_dir)){
                        mkdir($upload_dir,0777,true);
                    }
                    $filename = basename($_FILES['image']['name']);
                    $target_file = $upload_dir.time()."_".$filename;
                    if(move_uploaded_file($_FILES['image']['tmp_name'],$target_file)){
                        $image=$target_file;
                    }
                    else{
                         echo '<p style="color:red;">画像のアップロードに失敗しました</p>';
                    }
                }
                
                $sql = "INSERT INTO tbtest4 (name, comment,password,date,image,place,lost_date,address) VALUES (:name, :comment,:password,NOW(),:image,:place,:lost_date,:address)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt->bindParam(':password', $new_password, PDO::PARAM_STR);
                $stmt->bindParam(':image', $image, PDO::PARAM_STR);
                $stmt->bindParam(':place', $place, PDO::PARAM_STR);
                $stmt->bindParam(':lost_date', $lost_date, PDO::PARAM_STR);
                $stmt->bindParam(':address', $address, PDO::PARAM_STR);
                $stmt->execute();
            
            }  
        
            //編集対象取得
            if(!empty($edit_id) && !empty($edit_password)){
    
                $sql = 'SELECT * FROM tbtest4 WHERE id=:id'; //指定したidのデータのみ選択
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $edit_id,PDO::PARAM_INT);
                $stmt->execute();
                $edit_data = $stmt->fetch();    //対象の1件だけ取得
                $date = date("Y/m/d H:i:s");    //日付の取得
                
                //編集する際のパスワードが一致していた場合の処理
                if($edit_data["password"] == $edit_password){
                    $edit_name = $edit_data["name"];
                    $edit_comment = $edit_data["comment"];
                    $edit_target_id=$edit_data["id"];
                    $edit_place=$edit_data["place"];
                    $edit_lost_date=$edit_data["lost_date"];
                    $edit_address=$edit_data["address"];
                }
                //パスワードが不一致の場合の処理
                else{
                     echo '<p style="color:red;">パスワードが違います</p>';
                }
            }
            
    ?>
    <!--新規投稿、編集用フォーム-->
    <?php
        echo '<p style="color:red;">投稿者用フォーム↓</p>';
        echo '<p class="center-text">新規投稿、編集用フォーム（編集の場合はまず下の編集前入力をしてください</p>';
    ?>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="text" name="name" value="<?php if(isset($edit_name)) echo $edit_name; ?>" placeholder="飼い主の名前">
        <textarea name="comment" placeholder="コメント" rows="5" cols ="40"><?php if(isset($edit_comment))echo htmlspecialchars($edit_comment,ENT_QUOTES,'UTF-8');?></textarea>
        <input type="hidden" name="edit_target_id" value="<?php if(isset($edit_target_id)) echo $edit_target_id; ?>">
        
        <input type="file" name="image" placeholder="画像送信">    <!--画像アップロード-->
        <?php
            echo '<br>';
        ?>
        <input type = "text" name = "place" value="<?php if(isset($edit_place)) echo $edit_place; ?>" placeholder="見失った場所"> 
        <input type = "text" name = "lost_date" value="<?php if(isset($edit_lost_date)) echo $edit_lost_date; ?>" placeholder="見失った時間"> 
        <input type = "text" name = "address" value="<?php if(isset($edit_address)) echo $edit_address; ?>" placeholder="連絡先"> 
        
        <?php if(!empty($edit_target_id)) :?>
            <input type="password" name="edit_password" placeholder="パスワード">
        <?php else: ?>
            <input type="password" name="new_password" placeholder="パスワード(半角)">
        <?php endif; ?>
        <input type="submit" name="submit"><!--送信ボタンを表示-->
    </form>
    
    <?php
        echo '<p class="center-text">削除用フォーム</p>';
    ?>
    
    <!--削除用フォーム-->
    <form action="" method="post">
        <input type="text" name="delete_id" placeholder="削除対象番号(半角)">
        <input type="password" name="delete_password"  placeholder="パスワード(半角)">
        <input type="submit" name="submit" value="削除">
    </form>
    
    <?php
        echo '<p class="center-text">編集前入力フォーム(編集の場合はまずこちらに記入してください)</p>';
    ?>
    
    <!--編集用フォーム-->
    <form action="" method="post">
        <input type="text" name="edit_id" placeholder="編集対称番号(半角)">
        <input type="password" name="edit_password" placeholder="パスワード(半角)">
        <input type="submit" name="submit" value="編集">
    </form>
    
    <?php
        echo '<br>';
        echo '<p style="color:red;">閲覧者用フォーム↓</p>';
        echo '<p class="center-text">詳細な情報を知りたい、飼い主と相談したい場合はこちらにご記入ください</p>';
    ?>
    
    <form action="./detail.php">
        <input type="text" name="detail_id" placeholder="詳細id(半角)">
         <input type="submit" value=詳細を見る>
    </form>
    
    <?php
            //現在データベースに入っているデータを取得
            $sql = 'SELECT * FROM tbtest4';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            
            echo '<table border = "1">
                    <tr>
                    <th>ID</th>
                    <th>飼い主の名前</th>
                    <th>コメント</th>
                    <th>記入日</th>
                    <th>画像</th>
                    </tr>';
                    
            echo '<p style="color:red;">投稿一覧</p>';        
               
            foreach ($results as $row){
                    echo '<tr>';
                    echo '<td>'.$row['id'].'</td>';
                    echo '<td>'.$row['name'].'</td>';
                    echo '<td>'.nl2br(htmlspecialchars($row['comment'],ENT_QUOTES,'UTF-8')).'</td>';    //改行込みで出力
                    echo '<td>'.$row['date'].'</td>';
                    echo '<td>';
                    if(!empty($row['image'])){
                        echo '<img src="'.$row['image'].'"width="150">';
                    }
                    
                    echo '</td>';
                    echo '</tr>';
                }
            echo '</table>';
            
    ?>
</body>
</html>