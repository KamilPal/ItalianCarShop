<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';
$errors = [];
$user = ['name' => '', 'surname' => '', 'email' => ''];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['name'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $password = $_POST['password'] ? password_hash($_POST['password'], PASSWORD_BCRYPT) : null;

    // Walidacja po stronie serwera
    if (!preg_match("/^[a-zA-Z]{3,}$/", $username)) {
        $errors[] = "Imię powinno zawierać tylko litery i mieć co najmniej 3 znaki.";
    }

    if (!preg_match("/^[a-zA-Z]{3,}$/", $surname)) {
        $errors[] = "Nazwisko powinno zawierać tylko litery i mieć co najmniej 3 znaki.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email powinien być w prawidłowym formacie.";
    }

    if (empty($errors)) {
        $sql = "UPDATE users SET name = ?, surname = ?, email = ?";
        $params = [$username, $surname, $email];

        if ($password) {
            $sql .= ", password = ?";
            $params[] = $password;
        }

        $sql .= " WHERE id = ?";
        $params[] = $user_id;

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", ...$params);

        if ($stmt->execute()) {
            $message = "Profil zaktualizowany pomyślnie.";
        } else {
            $message = "Błąd podczas aktualizacji profilu.";
        }
    } else {
        $user = ['name' => $username, 'surname' => $surname, 'email' => $email];
    }
}

$sql = "SELECT name, surname, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if (!$user = $result->fetch_assoc()) {
    $user = ['name' => '', 'surname' => '', 'email' => ''];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edytuj Profil</title>
    <link rel="stylesheet" href="../css/general.css">
    <link rel="stylesheet" href="../css/miscellaneous.css">
</head>
<body>
<nav class="navbar">
    <div class="navbar-left">
        <a href="home.php"><img src="../Addons/logo.png" alt="Logo" class="navbar-logo"></a>
    </div>
    <div class="navbar-right">
        <div class="login-icon">
            <a href="#" id="login-trigger"><ion-icon name="person-outline"></ion-icon></a>
            <div class="login-dropdown" id="login-dropdown" style="display: none;">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if (isset($_SESSION['admin']) && $_SESSION['admin']): ?>
                        <a href="admin.php">Panel Admina</a>
                    <?php else: ?>
                        <a href="profile.php">Profil</a>
                    <?php endif; ?>
                    <a href="logout.php">Wyloguj się</a>
                <?php else: ?>
                    <a href="login.php">Zaloguj się</a>
                    <a href="register.php">Zarejestruj się</a>
                <?php endif; ?>
            </div>
        </div>
        <a href="shop.php">Sklep</a>
        <a href="home.php">Strona główna</a>
    </div>
</nav>
<div class="italian-flag"></div> 
<div class="container">
    <h2>Edytuj Profil</h2>
    <?php if ($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>
    <form action="edit_profile.php" method="post" onsubmit="return validateForm()">
        <label for="name">Imie:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required><br>

        <label for="surname">Nazwisko:</label>
        <input type="text" id="surname" name="surname" value="<?php echo htmlspecialchars($user['surname']); ?>" required><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required><br>

        <label for="password">Nowe hasło (opcjonalnie):</label>
        <input type="password" id="password" name="password"><br>

        <button type="submit">Zapisz zmiany</button>
    </form>
    <a href="profile.php">Powrót do profilu</a>
</div>
<div class="italian-flag"></div> 
<footer class="footer">
    <div class="social-media">
        <a href="#"><ion-icon name="logo-facebook"></ion-icon></a>
        <a href="#"><ion-icon name="logo-twitter"></ion-icon></a>
        <a href="#"><ion-icon name="logo-instagram"></ion-icon></a>
    </div>
</footer>
<script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@latest/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@latest/dist/ionicons/ionicons.js"></script>
<script src="../Js/menu.js"></script>
<script>
    var errors = <?php echo json_encode($errors); ?>;
    if (errors.length > 0) {
        alert(errors.join("\n"));
    }
</script>
</body>
</html>
