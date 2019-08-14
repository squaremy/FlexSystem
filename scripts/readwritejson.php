<?php
//Reads file successfully!
/*

// This PHP script must be in "SOME_PATH/jsonFile/index.php"

$file = 'temp.json';

if($_SERVER['REQUEST_METHOD'] === 'POST')
// or if(!empty($_POST))
{
    file_put_contents($file, $_POST["jsonTxt"]);
    //may be some error handeling if you want
}
else if($_SERVER['REQUEST_METHOD'] === 'GET')
// or else if(!empty($_GET))
{
    echo file_get_contents($file);
    //may be some error handeling if you want
}
*/

$myFile2 = "sampleFile.txt";
$myFileLink2 = fopen($myFile2, 'a') or die("Can't open file."); //w+ to overwrite, a to append
$newContents = "You wrote on me...";
fwrite($myFileLink2, $newContents);
fclose($myFileLink2);

$myFile = "sampleFile.txt";
$fh = fopen($myFile, 'r');
$myFileContents = fread($fh, 1000);

echo $myFileContents;

fclose($fh);
?>
