<?php
$raw_post_data = file_get_contents('php://input');
$myfile = fopen($_SERVER['DOCUMENT_ROOT'] . "/subscription/newfile.txt", "w") or die("Unable to open file!");
$txt = json_encode($raw_post_data);
fwrite($myfile, $txt);
fclose($myfile);