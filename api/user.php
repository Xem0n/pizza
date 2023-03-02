<?php

require_once "db.php";
require_once "ceaser_cipher.php";

class User {
    public $id = -1;

    public $email = "";
    public $password = "";
    public $username = "";
    public $hash = "";
    public $role = "";

    public static function get_all() {
        global $mysqli;

        $query = $mysqli->prepare("SELECT id, username, email, role FROM user");
        $query->execute();

        $usersData = $query->get_result()->fetch_all(MYSQLI_ASSOC);
        $users = array();

        foreach ($usersData as $data) {
            $user = new User();
            $user->id = $data["id"];
            $user->username = $data["username"];
            $user->email = $data["email"];
            $user->role = $data["role"];

            array_push($users, $user);
        }

        return $users;
    }

    public static function register($email, $username, $password) {
        $user = new User();
        $user->email = trim($email);
        $user->username = trim($username);
        $user->password = trim($password);
        $user->role = "user";

        return $user;
    }
    
    public function is_valid(): bool {
        if (strlen($this->email) < 3 || strlen($this->email) > 320)
            return false;
        
        if (strlen($this->username) < 6 || strlen($this->username) > 24)
            return false;
            
        if (strlen($this->password) < 8)
            return false;

        if (preg_match("/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/", $this->email) >= 1)
            return false;

        return true;
    }

    public static function encryptPass($password) {
        $hash = crypt($password, "lolxd");

        $cipher = new CaesarCipher();
        $hash = $cipher->encrypt($hash, 3);

        return $hash;
    }

    public function encrypt() {
        $this->hash = User::encryptPass($this->password);
    }

    public function save() {
        global $mysqli;

        $stmt = $mysqli->prepare("INSERT INTO user(email, username, password) VALUES (?, ?, ?)");
        $stmt->bind_param(
            "sss",
            $this->email,
            $this->username,
            $this->hash
        );
        $stmt->execute();
    }

    public function update() {
        global $mysqli;

        $stmt = $mysqli->prepare("
            UPDATE user SET
                email = ?,
                password = ?,
                username = ?
            WHERE id = ?
        ");
        $stmt->bind_param(
            "sssi",
            $this->email,
            $this->hash,
            $this->username,
            $this->id
        );
        $stmt->execute();
        echo mysqli_stmt_error($stmt);
    }

    public static function login($email, $password) {
        global $mysqli;

        $user = new User();
        $user->email = $email;
        $user->password = $password;

        $stmt = $mysqli->prepare("SELECT * FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_assoc();

        if (!$data)
            return NULL;

        $user->id = $data["id"];
        $user->username = $data["username"];
        $user->hash = $data["password"];
        $user->role = $data["role"];

        return $user;
    }

    public function check_password(): bool {
        if ($this->hash == "" && $this->password == $this->hash) {
            return true;
        }

        $cipher = new CaesarCipher();
        $this->password = $cipher->encrypt(crypt($this->password, "lolxd"), 3);

        return $this->password == $this->hash;
    }

    public function make_secure() {
        $this->password = "";
    }
}