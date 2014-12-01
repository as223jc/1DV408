<?php

/**
 * Class AccountDAL
 */
class AccountDAL {
    /**
     * @var mysqli
     */
    private $mysqli;
    /**
     * @var string
     */
    private static $accountTable = "accounts";
    /**
     * @var string
     */
    private static $characterTable = "characters";
    /**
     * @var string
     */
    private static $postTable = "posts";

    /**
     * @param mysqli $mysqli
     */
    function __construct(mysqli $mysqli){
         $this->mysqli = $mysqli;
    }

    /**
     * @return bool
     * @throws Exception
     */
    function createAccountTable(){
        $q = "CREATE TABLE IF NOT EXISTS `".self::$accountTable."`
        (
            `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY  ,
            `username` VARCHAR(25),
            `password` VARCHAR(35),
            `email` VARCHAR(40),
            `loginid` INT,
            `lastlogin` INT,
            `created` INT,
            `accounttype` TINYINT(1)
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
        if(!$this->mysqli->query($q)){
            throw new Exception("'$q' failed. error: ". $this->mysqli->error);
        }else
        return true;
    }

    /**
     * @return bool
     * @throws Exception
     */
    function createCharacterTable(){
        $q = "CREATE TABLE IF NOT EXISTS `".self::$characterTable."`
        (
            `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `account_id` INT NOT NULL,
            `playername` VARCHAR(25),
            `level` int(5),
            `sex` TINYINT(1),
            `playerposition` VARCHAR(25),
            `backpack` VARCHAR(500),
            `equipped` VARCHAR(500),
            `created` INT,
            `lastlogin` INT
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
        if(!$this->mysqli->query($q)){
            throw new Exception("'$q' failed. error: ". $this->mysqli->error);
        }else
            return true;
    }

    /**
     * @return bool
     * @throws Exception
     */
    function createPostTable(){
        $q = "CREATE TABLE IF NOT EXISTS `".self::$postTable."`
        (
            `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(50),
            `text` VARCHAR(500),
            `author` VARCHAR(25),
            `created` INT,
            `edited` INT
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
        if(!$this->mysqli->query($q)){
            throw new Exception("'$q' failed. error: ". $this->mysqli->error);
        }else
            return true;
    }

    /**
     * @param $newAccount
     * @return bool
     * @throws Exception
     */
    function createAccount($newAccount){
        $q = "INSERT INTO ". self::$accountTable ."
        (
            username,
            password,
            email
        )
        VALUES(?, ?, ?)";

        $hashedPass = password_hash($newAccount["password"], PASSWORD_BCRYPT);


        $statement = $this->mysqli->prepare($q);
        if(!$statement)
            throw new Exception("Kunde inte preparera sql-satsen:<br> $q.<br><br> Felmeddelande:<br>". $this->mysqli->error);
        if(!$statement->bind_param("sss", $newAccount["username"],
                                   $hashedPass,
                                   $newAccount["email"]))
            throw new Exception("Kunde inte bind_param:<br> $q.<br><br> Felmeddelande:<br>". $statement->error);
        if(!$statement->execute())
            throw new Exception("Kunde inte exekvera sql-satsen:<br> $q.<br><br> Felmeddelande:<br>". $statement->error);
        else
            return true;
    }

    /**
     * @param $account
     * @return bool
     * @throws Exception
     */
    function deleteAccount($account){
        $q = 'DELETE FROM `'.self::$accountTable.'` WHERE `username` = (?)';

        $statement = $this->mysqli->prepare($q);
        if(!$statement)
            throw new Exception("Kunde inte preparera sql-satsen:<br> $q.<br><br> Felmeddelande:<br>". $this->mysqli->error);
        if(!$statement->bind_param("s", $account["username"]))
            throw new Exception("Kunde inte bind_param:<br> $q.<br><br> Felmeddelande:<br>". $statement->error);
        if(!$statement->execute())
            throw new Exception("Kunde inte exekvera sql-satsen:<br> $q.<br><br> Felmeddelande:<br>". $statement->error);
        else
            return true;
    }

    /**
     * @param $account
     * @param string $pass
     * @return bool
     * @throws Exception
     */
    function accountExists($account, $pass = ""){
//        if($pass === "")
//            $q = 'SELECT username = (?) FROM `'.self::$accountTable.'`';
//        else {
            $q = 'SELECT username, password FROM `' . self::$accountTable . '` WHERE `username` = (?)';//AND BINARY `password` = (?)';
//        }

        $statement = $this->mysqli->prepare($q);
        if(!$statement)
            throw new Exception("Kunde inte preparera sql-satsen:<br> $q.<br><br> Felmeddelande:<br>". $this->mysqli->error);
        if($pass === "") {
            if (!$statement->bind_param("s", $account["username"]))
                throw new Exception("Kunde inte bind_param:<br> $q.<br><br> Felmeddelande:<br>" . $statement->error);
        }else{
            if (!$statement->bind_param("s", $account["username"]))//, $pass))
                throw new Exception("Kunde inte bind_param:<br> $q.<br><br> Felmeddelande:<br>" . $statement->error);
        }
        if(!$statement->execute())
            throw new Exception("Kunde inte exekvera sql-satsen:<br> $q.<br><br> Felmeddelande:<br>". $statement->error);
        else {
            $result = $statement->get_result();
            $resArr = [];
            $passer = password_hash($pass, PASSWORD_BCRYPT);

            while ($data = $result->fetch_assoc()) {
                $resArr[] = $data;
            }

            if(@$resArr[0]["username"]) {
                if ($pass) {
                    if ($correctPass = password_verify($pass, $resArr[0]['password'])) {
//                        ("true and correct pass".$resArr[0]["password"]);
                        return true;
                    }else{
                        //wrong password
                        return false;
                    }
                }else {
                    //user found WITHOUT password
                    return true;
                }
            } else {
                return false;
            }
//
//
//
//            if($statement->num_rows === 1 && $pass === "")
//                return true;
//            if($statement->num_rows === 1 && $pass !== "")
//                return true;
//            return false;
        }
    }

    /**
     * @param $character
     * @return bool
     * @throws Exception
     */
    function characterExists($character){
        $q = 'SELECT `playername` FROM  `'.self::$characterTable.'` WHERE `playername` = (?) LIMIT 1';

        $statement = $this->mysqli->prepare($q);
        if(!$statement)
            throw new Exception("Kunde inte preparera sql-satsen:<br> $q.<br><br> Felmeddelande:<br>". $this->mysqli->error);
        if(!$statement->bind_param("s", $character["playername"]))
            throw new Exception("Kunde inte bind_param:<br> $q.<br><br> Felmeddelande:<br>". $statement->error);
        if(!$statement->execute())
            throw new Exception("Kunde inte exekvera sql-satsen:<br> $q.<br><br> Felmeddelande:<br>". $statement->error);
        else {
            $statement->store_result();
            $qc = "";
            $statement->bind_result($qc);
            $statement->fetch();

            if($statement->num_rows === 1){
                return true;
            }
            return false;
        }

    }

    /**
     * @param $character
     * @return bool
     * @throws Exception
     */
    function createCharacter($character){
        $q = "INSERT INTO ". self::$characterTable ."
        (
            account_id,
            playername,
            sex,
            playerposition
        )
        VALUES(?, ?, ?, ?)";

        $statement = $this->mysqli->prepare($q);
        if(!$statement)
            throw new Exception("Kunde inte preparera sql-satsen:<br> $q.<br><br> Felmeddelande:<br>". $this->mysqli->error);
        if(!$statement->bind_param("isis",
            $character["accountid"],
            $character["playername"],
            $character["sex"],
            $character["playerposition"]))
            throw new Exception("Kunde inte bind_param:<br> $q.<br><br> Felmeddelande:<br>". $statement->error);
        if(!$statement->execute())
            throw new Exception("Kunde inte exekvera sql-satsen:<br> $q.<br><br> Felmeddelande:<br>". $statement->error);
        else
            return true;
    }

    /**
     * @param $character
     * @return bool
     * @throws Exception
     */
    function deleteCharacter($character){
        $q = 'DELETE FROM `'.self::$characterTable.'` WHERE `playername` = (?)';

        $statement = $this->mysqli->prepare($q);
        if(!$statement)
            throw new Exception("Kunde inte preparera sql-satsen:<br> $q.<br><br> Felmeddelande:<br>". $this->mysqli->error);
        if(!$statement->bind_param("s", $character["playername"]))
            throw new Exception("Kunde inte bind_param:<br> $q.<br><br> Felmeddelande:<br>". $statement->error);
        if(!$statement->execute())
            throw new Exception("Kunde inte exekvera sql-satsen:<br> $q.<br><br> Felmeddelande:<br>". $statement->error);
        else{
//            $statement->store_result();
//            $qc = null;
//            $statement->bind_result($qc);
//            $statement->fetch();

            if($statement->affected_rows >= 1){
                return true;
            }
            return false;
        }
    }

    /**
     * @param $account
     * @return array
     * @throws Exception
     */
    function getAccount($account){
        $q = 'SELECT id,username,email,loginid,lastlogin,accounttype,created FROM `'.self::$accountTable.'` WHERE `username` = (?)';

        $statement = $this->mysqli->prepare($q);
        if(!$statement)
            throw new Exception("Kunde inte preparera sql-satsen:<br> $q.<br><br> Felmeddelande:<br>". $this->mysqli->error);
        if(!$statement->bind_param("s", $account["username"]))
            throw new Exception("Kunde inte bind_param:<br> $q.<br><br> Felmeddelande:<br>". $statement->error);
        if(!$statement->execute())
            throw new Exception("Kunde inte exekvera sql-satsen:<br> $q.<br><br> Felmeddelande:<br>". $statement->error);
        else {
            $result = [];
            $statement->store_result();
            $statement->bind_result(
                $result["id"],
                $result["username"],
                $result["email"],
                $result["loginid"],
                $result["lastlogin"],
                $result["accounttype"],
                $result["created"]);
            $statement->fetch();
                return $result;
        }
    }

    /**
     * @param $character
     * @return array
     * @throws Exception
     */
    function getCharacterWithName($character){
        $q = 'SELECT id,account_id,playername,level,sex,playerposition,backpack,equipped,created,lastlogin FROM `'.self::$characterTable.'` WHERE `playername` = (?)';

        $statement = $this->mysqli->prepare($q);
        if(!$statement)
            throw new Exception("Kunde inte preparera sql-satsen:<br> $q.<br><br> Felmeddelande:<br>". $this->mysqli->error);
        if(!$statement->bind_param("s", $character["playername"]))
            throw new Exception("Kunde inte bind_param:<br> $q.<br><br> Felmeddelande:<br>". $statement->error);
        if(!$statement->execute())
            throw new Exception("Kunde inte exekvera sql-satsen:<br> $q.<br><br> Felmeddelande:<br>". $statement->error);
        else {
            $result = [];
            $statement->store_result();
            $statement->bind_result(
                $result["id"],
                $result["account_id"],
                $result["playername"],
                $result["level"],
                $result["sex"],
                $result["playerposition"],
                $result["backpack"],
                $result["equipped"],
                $result["created"],
                $result["lastlogin"]);
            $statement->fetch();
            return $result;
        }
    }

    /**
     * @param $account
     * @return array
     * @throws Exception
     */
    function getCharactersWithAccountId($account){
        $q = 'SELECT id,account_id,playername,level,sex,playerposition,backpack,equipped,created,lastlogin FROM `'.self::$characterTable.'` WHERE `account_id` = (?) ORDER BY `id`';

        $statement = $this->mysqli->prepare($q);
        if(!$statement)
            throw new Exception("Kunde inte preparera sql-satsen:<br> $q.<br><br> Felmeddelande:<br>". $this->mysqli->error);
        if(!$statement->bind_param("i", $account["account_id"]))
            throw new Exception("Kunde inte bind_param:<br> $q.<br><br> Felmeddelande:<br>". $statement->error);
        if(!$statement->execute())
            throw new Exception("Kunde inte exekvera sql-satsen:<br> $q.<br><br> Felmeddelande:<br>". $statement->error);
        else {
            $resultTot = [];
            $result = [];
            $statement->store_result();
            $statement->bind_result(
                $id,
                $account_id,
                $playername,
                $level,
                $sex,
                $playerposition,
                $backpack,
                $equipped,
                $created,
                $lastlogin);
            while($statement->fetch()){
                $resultTot[]= [
                    "id"=>$id,
                    "account_id"=>$account_id,
                    "playername"=>$playername,
                    "level"=>$level,
                    "sex"=>$sex,
                    "playerposition"=>$playerposition,
                    "backpack"=>$backpack,
                    "equipped"=>$equipped,
                    "created"=>$created,
                    "lastlogin"=>$lastlogin
                ];
            };
            $statement->close();
            return $resultTot;
        }
    }

    /**
     * @param $account
     * @return bool
     * @throws Exception
     */
    function authLogin($account){
        $verifyAccount = $this->getAccount($account);

        $now = date("YmdGis");
        $token = password_hash(session_id().$now, PASSWORD_BCRYPT);

        if(password_verify($verifyAccount["loginid"], $token))
            return $this->createAccAuth($account["username"], $token, $now);
        return false;
    }

    function createAccAuth($username, $token, $date){
        $q = "UPDATE ".self::$accountTable."
              SET loginid = '".$token."', lastlogin = '".$date."'
              WHERE username = (?)";

        $statement = $this->mysqli->prepare($q);
        if(!$statement)
            throw new Exception("Kunde inte preparera sql-satsen:<br> $q.<br><br> Felmeddelande:<br>". $this->mysqli->error);
        if(!$statement->bind_param("s", $username))
            throw new Exception("Kunde inte bind_param:<br> $q.<br><br> Felmeddelande:<br>". $statement->error);
        if(!$statement->execute())
            throw new Exception("Kunde inte exekvera sql-satsen:<br> $q.<br><br> Felmeddelande:<br>". $statement->error);
        else
            return true;
    }

    function getPosts(){
        $q = 'SELECT id,title,text,author,created,edited FROM `'.self::$postTable.'`';

        $statement = $this->mysqli->prepare($q);
        if(!$statement)
            throw new Exception("Kunde inte preparera sql-satsen:<br> $q.<br><br> Felmeddelande:<br>". $this->mysqli->error);
        if(!$statement->execute())
            throw new Exception("Kunde inte exekvera sql-satsen:<br> $q.<br><br> Felmeddelande:<br>". $statement->error);
        else {
            $resultTot = [];
            $result = [];
            $statement->store_result();
            $statement->bind_result(
                $id,
                $title,
                $text,
                $author,
                $created,
                $edited);
            while($statement->fetch()){
                $resultTot[]= [
                    "id"=>$id,
                    "title"=>$title,
                    "text"=>$text,
                    "author"=>$author,
                    "created"=>$created,
                    "edited"=>$edited
                ];
            };
            $statement->close();
            return $resultTot;
        }
    }

    function createPost($newpost){
        $q = "INSERT INTO ". self::$postTable ."
        (
            title,
            text,
            author,
            created
        )
        VALUES(?, ?, ?, ".date("YmdGis").")";

        $statement = $this->mysqli->prepare($q);
        if(!$statement)
            throw new Exception("Kunde inte preparera sql-satsen:<br> $q.<br><br> Felmeddelande:<br>". $this->mysqli->error);
        if(!$statement->bind_param("sss",
            $newpost["title"],
            $newpost["text"],
            $newpost["author"]))
            throw new Exception("Kunde inte bind_param:<br> $q.<br><br> Felmeddelande:<br>". $statement->error);
        if(!$statement->execute())
            throw new Exception("Kunde inte exekvera sql-satsen:<br> $q.<br><br> Felmeddelande:<br>". $statement->error);
        else
            return true;
    }

    function editPost($post){
        $q = "UPDATE
              ".self::$postTable."
              SET
              title = '".$post["title"]."',
              text = '".$post["text"]."'
              WHERE
              id = (?)";

        $statement = $this->mysqli->prepare($q);
        if(!$statement)
            throw new Exception("Kunde inte preparera sql-satsen:<br> $q.<br><br> Felmeddelande:<br>". $this->mysqli->error);
        if(!$statement->bind_param("s", $post["id"]))
            throw new Exception("Kunde inte bind_param:<br> $q.<br><br> Felmeddelande:<br>". $statement->error);
        if(!$statement->execute())
            throw new Exception("Kunde inte exekvera sql-satsen:<br> $q.<br><br> Felmeddelande:<br>". $statement->error);
        else
            return true;
    }

    function deletePostDB($postid){
        $q = "DELETE FROM ".self::$postTable." WHERE id = (?)";

        $statement = $this->mysqli->prepare($q);
        if(!$statement)
            throw new Exception("Kunde inte preparera sql-satsen:<br> $q.<br><br> Felmeddelande:<br>". $this->mysqli->error);
        if(!$statement->bind_param("i", $postid))
            throw new Exception("Kunde inte bind_param:<br> $q.<br><br> Felmeddelande:<br>". $statement->error);
        if(!$statement->execute())
            throw new Exception("Kunde inte exekvera sql-satsen:<br> $q.<br><br> Felmeddelande:<br>". $statement->error);
        else{
            if($statement->affected_rows >= 1){
                return true;
            }
            return false;
        }
    }
}