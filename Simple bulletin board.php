<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<head/>
<body>

<?php
$dsn='データベース名';
$user='ユーザー名';
$password='パスワード';
$pdo=new PDO($dsn,$user,$password);
$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

$sql="CREATE TABLE IF NOT EXISTS board3"//テーブル作成
."("
."id INT primary key auto_increment,"
."name char(32),"
."comment TEXT,"
."date TEXT,"
."password TEXT"
.");";
$stmt=$pdo->query($sql);

$name=$_POST['name'];
$comment=$_POST['comment'];
$password=$_POST['password'];//投稿用パスワード
$date=date("Y-m-d H:i:s");
$delete_number=$_POST['delete_number'];//削除番号
$edit_number=$_POST['edit_number'];//編集対象番号
$target_number=$_POST['target_number'];//編集対象の投稿番号
$delete_password=$_POST['delete_password'];//削除用パスワード
$edit_password=$_POST['edit_password'];//編集用パスワード

if(preg_match('/^[0-9]+$/',$edit_number) and $edit_number!=0 and !empty($edit_password))//編集対象の取得
{
	$select='SELECT*FROM board3';//フォームへの表示処理
	$results=$pdo->query($select);
	foreach($results as $row)
	{
		if($edit_number==$row['id']&&$edit_password==$row['password'])
		{
			$edit_num=$row['id'];
			$edit_name=$row['name'];
			$edit_comment=$row['comment'];
			$edit_pass=$row['password'];
		}
	}
}

if(!empty($target_number))//編集モードと投稿モードの切り替え
{
	//編集モード
	$sql=$pdo->prepare("update board3 set name=:name , comment=:comment , password=:password , date=:date where id=:edit_id and password=:edit_password");
	$sql->bindParam(':edit_id',$target_number,PDO::PARAM_INT);
	$sql->bindParam(':name',$name,PDO::PARAM_STR);
	$sql->bindparam(':comment',$comment,PDO::PARAM_STR);
	$sql->bindparam(':password',$password,PDO::PARAM_STR);
	$sql->bindParam(':date',$date,PDO::PARAM_STR);
	$sql->bindparam(':edit_password',$password,PDO::PARAM_STR);
	$sql->execute();
}
else
{
	//投稿モード
	if(!empty($name) and !empty($comment) and !empty($password))
	{
		$sql=$pdo->prepare("INSERT INTO board3(id,name,comment,date,password)VALUES(:id,:name,:comment,:date,:password)");
		$sql->bindParam(':id',$id,PDO::PARAM_INT);
		$sql->bindParam(':name',$name,PDO::PARAM_STR);
		$sql->bindparam(':comment',$comment,PDO::PARAM_STR);
		$sql->bindparam(':password',$password,PDO::PARAM_STR);
		$sql->bindParam(':date',$date,PDO::PARAM_STR);
		$id=NULL;
		$sql->execute();
	}
}

if(preg_match('/^[0-9]+$/',$delete_number) and $delete_number!=0 and !empty($delete_password))//削除処理
{
	$sql=$pdo->prepare("delete from board3 where id=:delete_id and password=:delete_password");
	$sql->bindparam(':delete_id',$delete_number,PDO::PARAM_INT);
	$sql->bindparam(':delete_password',$delete_password,PDO::PARAM_STR);
	$sql->execute();
}

?>

<form method="post" action="mission_4.php">
<input type="text" name="name" placeholder="投稿者名" value="<?php echo $edit_name; ?>"><br>
<input type="text" name="comment" placeholder="投稿内容" value="<?php echo $edit_comment; ?>">
<input type="hidden" name="target_number" value="<?php echo $edit_num; ?>"><br>
<input type="text" name="password" placeholder="パスワード" value="<?php echo $edit_pass; ?>">
<input type="submit" name="send_submit" value="送信"><br>
<br><input type="text" name="delete_number" placeholder="削除対象番号（半角）"><br>
<input type="text" name="delete_password" placeholder="パスワード">
<input type="submit" name="delete_submit" value="削除"><br>
<br><input type="text" name="edit_number" placeholder="編集対象番号（半角）"><br>
<input type="text" name="edit_password" placeholder="パスワード">
<input type="submit" name="edit_submit" value="編集"><br>
</form>

<?php
$select='SELECT*FROM board3 ORDER BY id ASC';//表示処理
$results=$pdo->query($select);
foreach($results as $row)
{
	echo $row['id'].',';
	echo $row['name'].',';
	echo $row['comment'].',';
	echo $row['date'].'<br>';
}
?>
<body/>