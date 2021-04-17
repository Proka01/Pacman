<?php
    $nickname = $_POST["nickname"];
    $score = $_POST["score"];

    $db = mysqli_connect('localhost','root','','pacman_baza');

    $query = "INSERT INTO korisnici (Nickname,score) VALUES ('$nickname','$score')";
    mysqli_query($db,$query);
    header("Location: pacman.php");
?>