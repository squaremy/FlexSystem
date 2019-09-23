<?php
// This PHP script must be in "SOME_PATH/jsonFile/index.php"
$file = 'temp.json';
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    file_put_contents($file, $_POST["jsonTxt"]);
}
else if($_SERVER['REQUEST_METHOD'] === 'GET'){
    echo file_get_contents($file);
    $array_data = json_decode($file, true);
    $extra = array(
      'name'    => 'test'
    );
    $array_data = array_push($array_data, $extra);
    $final_data = json_encode($array_data);
    file_put_contents('temp.json', $final_data);
}
?>
