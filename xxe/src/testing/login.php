<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $xml = '<user><username>' . $username . '</username><password>' . $password . '</password></user>';
    
    $doc = new DOMDocument();
    $doc->loadXML($xml);
    
    $result = $doc->saveXML();
    echo $result;
}
?>



