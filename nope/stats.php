<?php

$statURL = $_POST['stat'];

if ($statURL == 'true') { $file = '../stats/victoires';}
elseif ($statURL == 'false') { $file = '../stats/defaites';}
elseif ($statURL == 'null') { $file = '../stats/nuls';}

$statFile = fopen($file, 'r') or die('Erreur');
$chiffre = (int) fread($statFile, filesize($file));
fclose($statFile);

$chiffre++;

$statFile = fopen($file, 'w') or die('Erreur');
fwrite($statFile, $chiffre);
fclose($statFile);

echo $chiffre;

 ?>
