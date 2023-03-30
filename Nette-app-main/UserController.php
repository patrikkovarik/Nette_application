<?php

class UserController
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function register()
    {
        if (isset($_POST['register'])) {
            $username = $_POST['username'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $email = $_POST['email'];

            $query = $this->db->prepare('INSERT INTO users (username, password, email) VALUES (:username, :password, :email)');
            $query->bindParam(':username', $username);
            $query->bindParam(':password', $password);
            $query->bindParam(':email', $email);

            if ($query->execute()) {
                header('Location: login.php');
                exit;
            } else {
                echo 'Chyba při registraci';
            }
        }

        require 'views/register.php';
    }

    public function login()
    {
        if (isset($_POST['login'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $query = $this->db->prepare('SELECT * FROM users WHERE username = :username');
            $query->bindParam(':username', $username);
            $query->execute();
            $user = $query->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                session_start();
                $_SESSION['user_id'] = $user['id'];
                header('Location: index.php');
                exit;
            } else {
                echo 'Chybné přihlašovací údaje';
            }
        }

        require 'views/login.php';
    }

    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();
        header('Location: login.php');
        exit;
    }
}
