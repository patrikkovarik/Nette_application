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

// pokud je uživatel již přihlášen, přesměrujeme ho na úvodní stránku
if (isset($_SESSION['user_id'])) {
  header('Location: index.php');
  exit;
}

// zpracování formuláře pro registraci
if (isset($_POST['register'])) {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $query = $db->prepare('INSERT INTO users (email, password) VALUES (:email, :password)');
    $query->bindParam(':email', $email);
    $query->bindParam(':password', $password);

    if ($query->execute()) {
        $_SESSION['user_id'] = $db->lastInsertId();
        header('Location: index.php');
        exit;
    } else {
        echo 'Chyba při registraci uživatele';
    }
}


// zpracování formuláře pro přihlášení
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = $db->prepare('SELECT id, password FROM users WHERE email = :email');
    $query->bindParam(':email', $email);
    $query->execute();
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: index.php');
        exit;
    } else {
        echo 'Nesprávné jméno nebo heslo';
    }
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
  <meta charset="UTF-8">
  <title>Registrace a přihlášení</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h1>Registrace a přihlášení</h1>

  <div class="register">
    <h2>Registrace</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
      <div class="form-group">
        <label for="email">E-mail:</label>
        <input type="email" name="email" id="email" required>
      </div>
      <div class="form-group">
        <label for="password">Heslo:</label>
        <input type="password" name="password" id="password" required>
      </div>
      <div class="form-group">
        <button type="submit" name="register">Registrovat se</button>
      </div>
    </form>
  </div>
<style>/* formuláře */
form {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  margin: 20px auto;
  max-width: 400px;
  border: 1px solid #ccc;
  padding: 20px;
}

/* nadpis formuláře */
form h2 {
  margin: 0 0 20px;
  font-size: 24px;
  font-weight: bold;
  text-align: center;
}

/* vstupní pole */
form input[type="text"],
form input[type="email"],
form input[type="password"] {
  width: 100%;
  padding: 10px;
  margin-bottom: 10px;
  border: 1px solid #ccc;
  border-radius: 3px;
}

/* tlačítka */
form input[type="submit"],
form button {
  display: inline-block;
  background-color: #007bff;
  color: #fff;
  padding: 10px 20px;
  border: none;
  border-radius: 3px;
  cursor: pointer;
}

form input[type="submit"]:hover,
form button:hover {
  background-color: #0056b3;
}

/* odkazy */
form a {
  color: #007bff;
  text-decoration: none;
}

form a:hover {
  text-decoration: underline;
}</style>
  <div class="login">
    <h2>Přihlášení</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
      <div class="form-group">
        <label for="email">E-mail:</label>
        <input type="email" name="email" id="email" required>
      </div>
      <div class="form-group">
        <label for="password">Heslo:</label>
        <input type="password" name="password" id="password" required>
      </div>
      <div class="form-group">
        <button type="submit"name="login">Prihlasit se</button>
      </div>
    </form>
  </div>

