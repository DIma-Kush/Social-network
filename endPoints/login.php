<?php
include ('../Config/config.php');
include ('../Db/Db.php');
$Db = new \Db\Db();
$data = json_decode(file_get_contents('php://input'), true);
$select = "SELECT userId FROM users WHERE userEmail = '$data[userEmail]' AND userPassword = '$data[userPassword]'";
$query = $Db->query($select);
$query->execute();
$num = $query->fetchAll(PDO::FETCH_ASSOC);
$userInfo = null;
$id = null;
$cipherId = null;

if (!empty($data))
{
    if (!empty($num))
    {
        $id = (int)$num[0]["userId"];
        $success = true;
        $cipherId = $Db->encryptText($id);
    }
    else
    {
        $userInfo = "Користувача з таким емейлом, або паролем не існує";
        $success= false;
    }
}
$result = [
    'success' => $success,
    'id' => $cipherId,
    'userInfo'=> $userInfo,
    'errors' => []
];

header('Content-Type: application/json');
echo json_encode($result);

