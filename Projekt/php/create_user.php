<?php
session_start();

if (!isset($_SESSION['user_id']) || !$_SESSION['admin']) {
    header("Location: login.php");
    exit();
}

require 'config.php';

$errors = [];
$name = '';
$surname = '';
$email = '';
$admin = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $admin = isset($_POST['admin']) ? 1 : 0;

    // Walidacja po stronie serwera
    if (!preg_match("/^[a-zA-Z]{3,}$/", $name)) {
        $errors[] = "Imię powinno zawierać tylko litery i mieć co najmniej 3 znaki.";
    }

    if (!preg_match("/^[a-zA-Z]{3,}$/", $surname)) {
        $errors[] = "Nazwisko powinno zawierać tylko litery i mieć co najmniej 3 znaki.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email powinien być w prawidłowym formacie.";
    }

    if (strlen($password) < 6) {
        $errors[] = "Hasło powinno mieć co najmniej 6 znaków.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Hasła nie są zgodne.";
    }

    if (empty($errors)) {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = "Email jest już zajęty.";
        } else {
            // Hashowanie hasła
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Przygotowanie i wykonanie zapytania SQL
            $stmt = $conn->prepare("INSERT INTO users (name, surname, email, password, activation_code, is_active, created_at, updated_at, admin) VALUES (?, ?, ?, ?, '', 1, NOW(), NOW(), ?)");
            $stmt->bind_param("ssssi", $name, $surname, $email, $hashed_password, $admin);
            if ($stmt->execute()) {
                header("Location: manage_users.php");
                exit();
            } else {
                $errors[] = "Błąd: " . $stmt->error;
            }
            $stmt->close();
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dodaj użytkownika</title>
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
                        <?php if ($_SESSION['admin']): ?>
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
    <h2>Dodaj nowego użytkownika</h2>
    <div class="container">
    <form action="create_user.php" method="post" onsubmit="return validateForm()">
        <label for="name">Imię:</label>
        <input type="text" id="name" name="name" value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" required><br>
        <label for="surname">Nazwisko:</label>
        <input type="text" id="surname" name="surname" value="<?php echo isset($surname) ? htmlspecialchars($surname) : ''; ?>" required><br>
        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required><br>
        <label for="password">Hasło:</label>
        <input type="password" id="password" name="password" required><br>
        <label for="confirm_password">Potwierdź hasło:</label>
        <input type="password" id="confirm_password" name="confirm_password" required><br>
        <label for="admin">Admin:</label>
        <input type="checkbox" id="admin" name="admin" <?php echo $admin ? 'checked' : ''; ?>><br>
        <button type="submit">Dodaj użytkownika</button>
    </form>
    </div>
    <div class="italian-flag"></div> 
    <footer class="footer">
        <div class="social-media">
            <a href="#"><ion-icon name="logo-facebook"></ion-icon></a>
            <a href="#"><ion-icon name="logo-twitter"></ion-icon></a>
            <a href="#"><ion-icon name="logo-instagram"></ion-icon></a>
        </div>
    </footer>
    <script src="../Js/menu.js"></script>
    <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@latest/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@latest/dist/ionicons/ionicons.js"></script>
</body>
</html>
    <script src="../Js/walidacja.js"></script>
    <script>
        var errors = <?php echo json_encode($errors); ?>;
        if (errors.length > 0) {
            alert(errors.join("\n"));
        }
    </script>
</body>
</html>
