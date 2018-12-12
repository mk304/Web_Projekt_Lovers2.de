<?php
include_once '../../userdata.php';
session_start();

$post = $_POST["post"];
$id = $_POST["post_id"];

$pdo = new PDO ($dsn, $dbuser, $dbpass, array('charset'=>'utf8'));

// Änderung des Textposts wird in der Datenbank verändert
$sql = "UPDATE posts SET post=:post WHERE posts_id=:id";
$stmt=$pdo->prepare($sql);
if (!$stmt){
    echo "Prepare Fehler.";
}

if (!$stmt->execute(array( ':post'=>$post, ':id'=>$id))) {
    echo "Query Fehler.";
}
?>
