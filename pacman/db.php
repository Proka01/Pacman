<?php
$db = mysqli_connect("localhost", "root", "", "pacman_baza");
$query = "SELECT * FROM korisnici ORDER BY score DESC";
$tbl = mysqli_query($db, $query);
$niz = [];
while ($row = mysqli_fetch_assoc($tbl))
    array_push($niz,
               Array("nickname" => $row["Nickname"],
                     "score" => $row["score"]
                    )
              );

echo json_encode($niz);
?>