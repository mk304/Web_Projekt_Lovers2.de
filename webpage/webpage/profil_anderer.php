<!DOCTYPE html>
<html>
<head>
    <title>Profil</title>

<script src="src/jquery-3.3.1.min.js"></script>
<script src="src/fullclip.min.js"></script>
<script src="src/fullclip.js"></script>
</head>
<body>
<?php
include_once '../../userdata.php';
include_once '../ui/neuerheader.php';
session_start();

$profilname = $_GET["profilname"];
$kuerzel = $_SESSION["kuerzel"];
$_SESSION["profilname"] = $profilname;

//Ausgabe der Skills
$pdo = new PDO ($dsn, $dbuser, $dbpass, array('charset'=>'utf8'));
$sql_2 = "SELECT skill from skills WHERE id = ANY (SELECT skill FROM user_skills WHERE kuerzel=:profilname)";
$query_2 = $pdo->prepare($sql_2);

?>
<link rel="stylesheet" href="style.css"><link rel="stylesheet" href="posts.css">

<!-- Ausgabe des Titelbildes -->
<div class="container">
    <div class="avatar-upload">
        <form action="../register/profil_bild_header.php" method="post" enctype="multipart/form-data">
            <div class="avatar-edit">
                <input type='file' name="file" id="imageUpload" accept=".jpg, .jpeg"/>

            </div>
            <div class="avatar-preview">

                <?php
                $file_pointer = '../headerbilder/header'.$profilname.'.jpg';

                // Wenn der User ein eigenes Bild hochgeladen hat, wird dieses angezeigt.
                if (file_exists($file_pointer))
                {
                    echo "<div id=\"imagePreview\" style=\"background-image: url(../headerbilder/header".$profilname.".jpg);\">
                    </div>";
                }
                // Wenn er kein Bild hochgeladen hat, wird unser Standardbild angezeigt.
                else
                {
                    echo "<div id=\"imagePreview\" style=\"background-image: url(../headerbilder/header.jpg);\">
                    </div>";
                }
                ?>
            </div>

        </form>
    </div>
</div>

<!-- Ausgabe des Profilbildes -->
<div class="container2">
    <div class="avatar-upload2">
        <form action="../register/profil_bild.php" method="post" enctype="multipart/form-data">
            <div class="avatar-edit2">

                <label name="2" for="imageUpload2"></label>
            </div>
            <div class="avatar-preview2">
                <?php
                $file_pointer = '../profilbilder/profilbild'.$profilname.'.jpg';

                if (file_exists($file_pointer))
                {
                    echo "<div id=\"imagePreview2\" style=\"background-image: url(../profilbilder/profilbild".$profilname.".jpg);\">
                    </div>";
                }
                else
                {
                    echo "<div id=\"imagePreview2\" style=\"background-image: url(../profilbilder/profilbild.jpg);\">
                    </div>";
                }
                ?>

            </div>

        </form>
    </div>
</div>

<!-- Informationen über die Person, auf dessen Profil man sich befindet -->
<div class="wrapper1">
    <div class="one" style="overflow: scroll"; >
        <div class="infobox">
            <?php
            $pdo = new PDO ($dsn, $dbuser, $dbpass, array('charset' => 'utf8'));
            $sql_5 = "SELECT vorname, nachname, email, kuerzel from user WHERE kuerzel=:profilname ";
            $query_5 = $pdo->prepare($sql_5);
            $query_5->execute(array(":profilname"=>"$profilname"));

            while ($row = $query_5->fetchObject()) {
$name= $row->vorname." " .$row->nachname;
                echo ("<div class='name'>".$row->vorname." " .$row->nachname."</div>");


            }

            // Wenn auf das eigene profil geklickt wird, wird auf die profil.php umgeleitet
            if ($profilname == $kuerzel) {
            header ("Location: profil.php");
            }

            // Überprüfung, ob man dieser Person folgt oder nicht
            $pdo = new PDO ($dsn, $dbuser, $dbpass, array('charset'=>'utf8'));
            $sql = "SELECT folgt FROM abonnenten WHERE (kuerzel=:kuerzel AND folgt=:profilname)";

            $statement = $pdo->prepare($sql);
            $statement->execute(array(":kuerzel"=>"$kuerzel", ":profilname"=>"$profilname"));

            $row = $statement->fetchObject();



            if ($profilname == $row->folgt)
            {  // Button wird zu "Freunde" -> keine Weiterleitung hinterlegt
                echo '<button id="entfolgen" onclick="location.href=\'profil_anderer_entfolgen.php\'" type="button" class="btn btn-outline-secondary">';
                echo " Freunde</button>";
            }

            else //Button wird zu "Folgen" und Weiterleitung zum Datenbankeintrag
            { ?>
            <button type="button" class="btn btn-light" onclick="location.href='profil_anderer_folgen.php'">Folgen</button>
            <?php
}
?>
            </div>

        <!-- Button Send Mail -->
        <div class="aboutme">
            <?php
            $query_5 = $pdo->prepare($sql_5);
            $query_5->execute(array(":profilname"=>"$profilname"));
            while ($row = $query_5->fetchObject()) {
                echo("<div class='email'> E-Mail-Adresse: " .$row->email . "</div>");
                echo ("<a class=\"btn btn-light\"href=\"mailto:$row->email?Subject=Hallo%20$row->vorname $row->nachname! \" target=\"_top\">Send Mail</a>");


            }?>

        <!-- Ausgabe der Skills der Person, auf dessen profil man sich befindet -->
        </div><div class="skills"> <h4> Meine Skills: </h4>
            <div class="row">
        <?php
        if (!$query_2->execute(array(":profilname"=>"$profilname")));
        while ($row = $query_2->fetchObject()) {
            $skill = $row->skill;


            echo "<a href='../register/do_search.php?search=".$row->skill."'  class=\"column\"><img src=\"../skills/" . $skill . ".png\"> </a>";
        }
        if (!$query) {
            echo "Prepare Fehler.";
        }
        ?></div></div>
       </div>

    <!-- Ausgabe der Beiträge der Person, dessen Profil man sich befindet -->
    <div class="two" style="overflow: scroll; height: 100%; width: 100%">
        <?php
        $pdo = new PDO ($dsn, $dbuser, $dbpass, array('charset' => 'utf8'));
        $sql_3 = "SELECT post, kuerzel, date, posts_id, bild_id from posts WHERE kuerzel=:kuerzel ORDER BY posts.date DESC";
        $query_3 = $pdo->prepare($sql_3);
        $query_3->execute(array(":kuerzel" => "$profilname"));

        while ($row = $query_3->fetchObject()) {

            if ($row->bild_id == NULL){
                echo "<div class='inhalt'>";

                echo "<div class='text'>";

                echo "<a ><h3>" . ($row->post) . "</h3><br><h4>" . " schrieb <a   href='../webpage/profil_check.php?profilname=$profilname'>" . ($profilname) . "</a> um " . date('g:i a, F j, Y', strtotime($row->date));
                echo "</h4>";

            }


            if ($row->post == NULL) {

                echo "<div class='inhalt'>";

                echo "<div class='text' >";
                $bildlink= $row-> bild_id;


                echo "<a href='../bildupload/$bildlink'><img class='bild' src='../bildupload/$bildlink'>";


            }

            $file_pointer = '../profilbilder/profilbild' . ($profilname) . '.jpg';
            echo "</div><div class='profil_bild_post' ><a class='atag' href='../webpage/profil_check.php?profilname=$profilname'>";



            if (file_exists($file_pointer)) {
                echo "<img src=\"$file_pointer\">";
            } else {
                echo "<img src=\"../profilbilder/profilbild.jpg\">";
            }
            echo "</div></div>";
        }
        ?>
    </div>
</div>

<script>
    $(document).ready(function () {
        $("#entfolgen").mouseover(function () {
            $(this).html("als Freund entfernen");
        });
        $("#entfolgen").mouseout(function () {
            $(this).html("Freunde");
        });
    });

</script>
<?php
include_once '../ui/footer.php';
?>
</body>
</html>

