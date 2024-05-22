<?php
namespace yahya\Models;
session_start();
class User extends \yahya\Database\Database{
    
    public $idUser;
    public $name;
    public $lastName;
    public $email;
    public $pass;
    public $profileImg =  "/opt/lampp/htdocs/jomoaa/public/assets/imgs/imgsmom";

    public function signup(){
        if (isset($_POST['signup'])) {
            
            if ($_POST['password'] === $_POST['comfirmationPassword']) {
                $this->colectDataUser();
                if ($this->checkUser()) {
                    $this->setProfileImg();
                    $this->register();
                    
                    $_SESSION['USER'] = $this->idUser;
                    header("Location: http://localhost/jomoaa");
                }
                else {
                    echo "already exist";
                }
            }
        }
    }


    public function login(){
        if (isset($_POST['login'])) {

            $this->email = $_POST["EMAIL"];
            if (is_array($this->checkUser())) {

                if ($_POST['PASSWORD'] == $this->checkUser()['PASSWORD']) {
                   
                    $_SESSION['USER'] = $this->checkUser()['ID_USER'];
                    header("Location: http://localhost/jomoaa");
                }
                else {
                    echo "wrong password";
                }
            }
        }
    }

    public function checkUser(){

        $sql = "select * FROM user WHERE EMAIL = :email;";
        $this->query($sql);
        $this->bind(":email" , $this->email);
        $this->execute();
        $this->rowcount();
        $result = $this->get();
        
        if ($this->rowsNum == 0 ) {
            return true;
        }
        else {
            return $result;
        }
    } 

    public function register(){
        $sqli = "insert into user (NOM, PRENOM, EMAIL, PASSWORD, IMG)values(:name , :lastname , :email , :password, :img);";
        $this->query($sqli);
        $this->bind(":name",$this->name);
        $this->bind(":lastname",$this->lastName);
        $this->bind(":email",$this->email);
        $this->bind(":password",$this->pass);
        $this->bind(":img",$this->profileImg);
        $this->execute();
    }

    public function setProfileImg(){
        $fileName = basename($_FILES["file"]["name"]);
        $targetFilePath = "public/assets/imgs/" . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
                        
        $allowedTypes = array('jpg', 'png', 'jpeg', 'gif', 'bmp');
        if (in_array(strtolower($fileType), $allowedTypes)) {        
            if ($_FILES["file"]["error"] == 0) {
                
                if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {

                    echo "The file ". htmlspecialchars($fileName). " has been uploaded.";
                    $this->profileImg = $targetFilePath;
                } else {
                    echo "Sorry, there was an error uploading your file.";
                }
            }else {
                echo "Error: " . $_FILES["file"]["error"];
            }
        } else {
            echo "Sorry, only JPG, JPEG, PNG, GIF, & BMP files are allowed.";
        }
    }



    public function colectDataUser(){
        
        $this->name = $_POST['name'];
        $this->lastName = $_POST['lastName'];
        $this->email = $_POST['email'];
        $this->pass = $_POST['password'];
    
    }

    public function getDataUser(){
        if (isset($_SESSION['USER'])) {
            $sql = "select * from user where ID_USER = :id";
            $this->query($sql);
            $this->bind(":id" , $_SESSION['USER']);
            $this->execute();
            $result = $this->get();
            return $result;
        }
    }

    public function logout(){
        if (isset($_POST['logout'])) {
            session_destroy();
            header("Location: http://localhost/jomoaa/");
            exit();
        }
    }

}
?>