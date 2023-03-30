<?php

class EventController
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function create()
    {
        // Kontrola, zda je uživatel přihlášen
        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php');
            exit;
        }

        // Zpracování formuláře pro vytvoření nové akce
        if (isset($_POST['create_event'])) {
            $title = $_POST['title'];
            $description = $_POST['description'];
            $created_by = $_SESSION['user_id'];

            $query = $this->db->prepare('INSERT INTO events (title, description, created_by) VALUES (:title, :description, :created_by)');
            $query->bindParam(':title', $title);
            $query->bindParam(':description', $description);
            $query->bindParam(':created_by', $created_by);

            if ($query->execute()) {
                $event_id = $this->db->lastInsertId();
                $options = $_POST['options'];

                foreach ($options as $option) {
                    $datetime = date('Y-m-d H:i:s', strtotime($option));
                    $query = $this->db->prepare('INSERT INTO options (event_id, date_time) VALUES (:event_id, :date_time)');
                    $query->bindParam(':event_id', $event_id);
                    $query->bindParam(':date_time', $datetime);
                $query->execute();
            }

            header('Location: index.php');
            exit;
        } else {
            echo 'Chyba při vytváření akce';
        }
    }

    require 'views/create_event.php';
}

public function show($id)
{
    $query = $this->db->prepare('SELECT * FROM events WHERE id = :id');
    $query->bindParam(':id', $id);
    $query->execute();
    $event = $query->fetch(PDO::FETCH_ASSOC);

    $query = $this->db->prepare('SELECT * FROM options WHERE event_id = :event_id');
    $query->bindParam(':event_id', $id);
    $query->execute();
    $options = $query->fetchAll(PDO::FETCH_ASSOC);

    require 'views/show_event.php';
}

public function vote($id)
{
    // Kontrola, zda je uživatel přihlášen
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }

    $query = $this->db->prepare('SELECT * FROM events WHERE id = :id');
    $query->bindParam(':id', $id);
    $query->execute();
    $event = $query->fetch(PDO::FETCH_ASSOC);

    $query = $this->db->prepare('SELECT * FROM options WHERE event_id = :event_id');
    $query->bindParam(':event_id', $id);
    $query->execute();
    $options = $query->fetchAll(PDO::FETCH_ASSOC);

    // Zpracování formuláře pro hlasování
    if (isset($_POST['vote'])) {
        $option_id = $_POST['option_id'];
        $user_id = $_SESSION['user_id'];

        $query = $this->db->prepare('INSERT INTO votes (option_id, user_id) VALUES (:option_id, :user_id)');
        $query->bindParam(':option_id', $option_id);
        $query->bindParam(':user_id', $user_id);

        if ($query->execute()) {
            header('Location: index.php');
            exit;
        } else {
            echo 'Chyba při hlasování';
        }
    }

    require 'views/vote.php';
}

public function changeVote($id)
{
    // Kontrola, zda je uživatel přihlášen
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }

    $query = $this->db->prepare('SELECT * FROM events WHERE id = :id');
    $query->bindParam(':id', $id);
    $query->execute();
    $event = $query->fetch(PDO::FETCH_ASSOC);

    $query = $this->db->prepare('SELECT * FROM options WHERE event_id = :event_id');
    $query->bindParam(':event_id', $id);
    $query->execute();
    $options = $query->fetchAll(PDO::FETCH_ASSOC);

    // Zpracování formuláře pro změnu hlasu
    if (isset($_POST['change_vote'])) {
        $option_id = $_POST['option_id'];
        $user_id = $_SESSION['user_id'];

        $query = $this->db->prepare('UPDATE votes SET option_id = :option_id WHERE user_id = :user_id');
        $query->bindParam(':option_id', $option_id);
        $query->bindParam(':user_id', $user_id);
        if ($query->execute()) {
            header('Location: index.php');
            exit;
        } else {
            echo 'Chyba při změně hlasu';
        }
    }

    require 'views/change_vote.php';
}
}
?>