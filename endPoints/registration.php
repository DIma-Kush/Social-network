<?php
    include ('../Config/config.php');
    include ('../Db/Db.php');
    $Db = new \Db\Db();
    $data = json_decode(file_get_contents('php://input'), true);
    ///////
    $users = $data["users"];
    $data_users = $data["data_users"];
    ///////
    $select = "SELECT count(*) as counter FROM users WHERE userEmail='$users[userEmail]'";
    $query = $Db->query($select);
    $query->execute();
    $num = $query->fetchAll(PDO::FETCH_ASSOC);

    $insertId = null;
    if (!empty($data)) {
        if($num[0]['counter'] > 0){
            $success = false;
            $userInfo = "Користувач з таким емейлом вже існує";
        }else {
           $insertId = $Db->addSql('users', $users); // add to users
           $data_users["userId"] = $insertId; // update field in users_data id
           $insertUserId = $Db->addSql('users_data', $data_users); // add to users_data
            if($insertId){
                mkdir("../src/img/users/user".$insertId, 0777, 'R');
                mkdir("../src/img/users/user".$insertId."/avatars", 0777, 'R');
                mkdir("../src/img/users/user".$insertId."/backgrounds", 0777, 'R');
                mkdir("../src/img/users/user".$insertId."/chat", 0777, 'R');
                mkdir("../src/img/users/user".$insertId."/friends", 0777, 'R');
                mkdir("../src/img/users/user".$insertId."/gallery", 0777, 'R');
                mkdir("../src/img/users/user".$insertId."/posts", 0777, 'R');
                $success = true;
                if (!is_dir("../src/img/users/user".$insertId)) {
                    //create the directory
                    $userInfo = "error";
                }  else{
                    $userInfo = "Ви зареєстровані";
                }
              
            }   
           else{
            $success = false;
               $userInfo = "Помилка";
           }
        }
    } 
    $result = [
        'success' =>  $success,        
        'id' => $insertId,
        'userInfo' => $userInfo,
        'errors' => []
    ];

    header('Content-Type: application/json');
    echo json_encode($result);
