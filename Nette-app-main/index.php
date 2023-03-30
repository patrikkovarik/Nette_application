<?php
session_start();

$dbHost = 'localhost';
$dbName = 'doodle';
$dbUser = 'root';
$dbPass = 'student';

// Připojení k databázi
try {
    $db = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo 'Chyba při připojení k databázi: ' . $e->getMessage();
}


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

    $query = $db->prepare('INSERT INTO events (title, description, created_by) VALUES (:title, :description, :created_by)');
    $query->bindParam(':title', $title);
    $query->bindParam(':description', $description);
    $query->bindParam(':created_by', $created_by);

    if ($query->execute()) {
        $event_id = $db->lastInsertId();
        $options = $_POST['options'];

        foreach ($options as $option) {
            $datetime = date('Y-m-d H:i:s', strtotime($option));
            $query = $db->prepare('INSERT INTO options (event_id, date_time) VALUES (:event_id, :date_time)');
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



spl_autoload_register(function ($class) {
  $file = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
  if (file_exists($file)) {
      require_once $file;
      return true;
  }
  return false;
});
use Nette\Application\UI;
// vytvoření objektu šablony
$template = new UI\ITemplate;
$template->setFile(__DIR__ . '/templates/index.latte');
// Načtení dat z databáze
$query = $db->query('SELECT * FROM events');
$events = $query->fetchAll();
// Předání dat do šablony
$template->events = $events;
// Vypsání šablony
$template->render();



?>



<!DOCTYPE html>
<html>
<head>
    <title>Vytvořit novou akci</title>
</head>
<body>
    <h1>Vytvořit novou akci</h1>
    <form method="POST">
        <label>Název akce:</label>
        <input type="text" name="title" required>
        <br>
        <label>Popis akce:</label>
        <textarea name="description"></textarea>
        <br>
        <label>Termíny:</label>
        <input type="datetime-local" name="options[]" required>
        <input type="datetime-local" name="options[]">
        <!-- můžete přidat více polí pro termíny, pokud chcete -->
        <br>
        <br>
        <br>
        <input type="submit" name="create_event" value="Vytvořit">
    </form>
<style>
    /* Všechny prvky na stránce */
* {
  box-sizing: border-box;
  font-family: Arial, sans-serif;
  margin: 0;
  padding: 0;
}

/* Hlavička stránky */
header {
  background-color: #333;
  color: #fff;
  padding: 20px;
}

header h1 {
  margin: 0;
}

/* Navigační menu */
nav {
  background-color: #f2f2f2;
  border-bottom: 1px solid #ccc;
  padding: 10px 20px;
}

nav ul {
  list-style: none;
  margin: 0;
  padding: 0;
}

nav li {
  display: inline-block;
  margin-right: 20px;
}

nav a {
  color: #333;
  text-decoration: none;
  font-weight: bold;
}

nav a:hover {
  text-decoration: underline;
}

/* Hlavní obsah stránky */
main {
  padding: 20px;
}

h1 {
  margin-bottom: 20px;
}

form {
  margin-bottom: 20px;
}

label {
  display: block;
  margin-bottom: 5px;
}

input[type="text"],
input[type="email"],
input[type="password"],
textarea {
  display: block;
  width: 100%;
  padding: 10px;
  margin-bottom: 20px;
  border: 1px solid #ccc;
  border-radius: 3px;
  box-shadow: inset 0 1px 2px rgba(0,0,0,.1);
  font-size: 16px;
  box-sizing: border-box;
}

input[type="submit"] {
  background-color: #008CBA;
  border: none;
  color: white;
  padding: 10px 20px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin: 4px 2px;
  cursor: pointer;
  border-radius: 4px;
}

/* Kalendář pro input s typem datetime-local */
input[type="datetime-local"]::-webkit-calendar-picker-indicator {
  color: #008CBA;
  font-size: 16px;
  margin: 4px 2px;
  padding: 10px 20px;
}

.error-message {
  color: red;
  margin-bottom: 10px;
}

</style>
</body>
</html>
