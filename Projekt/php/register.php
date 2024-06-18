<?php
require 'config.php';
session_start();

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $activation_code = bin2hex(random_bytes(16)); // Generate a random activation code

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $errors[] = "Email jest już zajęty.";
    } else {
        // Validate form inputs
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
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert user into database
            $stmt = $conn->prepare("INSERT INTO users (name, surname, email, password, activation_code, is_active, created_at, updated_at, admin) VALUES (?, ?, ?, ?, ?, 0, NOW(), NOW(), 0)");
            $stmt->bind_param("sssss", $name, $surname, $email, $hashed_password, $activation_code);

            if ($stmt->execute()) {
                // Send activation email
                $subject = "Aktywacja konta";
                $message = "Witaj $name,\n\nDziękujemy za rejestrację. Kliknij poniższy link, aby aktywować swoje konto:\n\n";
                $message .= "Poniższego kodu musisz użyć do aktywacji:\n\nKod aktywacyjny: $activation_code\n\n";
                $headers = "From: noreply@yourdomain.com";

                if (mail($email, $subject, $message, $headers)) {
                    header("Location: login.php?registered=true");
                } else {
                    $errors[] = "Nie udało się wysłać emaila aktywacyjnego.";
                }
            } else {
                $errors[] = "Błąd podczas rejestracji. Spróbuj ponownie.";
            }

            $stmt->close();
        }
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Rejestracja użytkownika</title>
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
<h2>Rejestracja użytkownika</h2>
<div class="container">
    <form action="register.php" method="post" onsubmit="return validateForm()">
        <?php if (!empty($errors)): ?>
            <div class="errors">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <label for="name">Imię:</label>
        <input type="text" id="name" name="name" value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" required>
        <br>
        <label for="surname">Nazwisko:</label>
        <input type="text" id="surname" name="surname" value="<?php echo isset($surname) ? htmlspecialchars($surname) : ''; ?>" required>
        <br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
        <br>
        <label for="password">Hasło:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <label for="confirm_password">Powtórz hasło:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
        <br>
        <button type="submit">Zarejestruj</button>
    </form>
</div>
<p>Masz już konto? <a href="login.php">Zaloguj się</a></p>

<script src="walidacja.js"></script>
<script>
    if (typeof errors !== 'undefined' && errors.length > 0) {
        alert(errors.join("\n"));
    }
</script>
<script src="../Js/menu.js"></script>
<script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@latest/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@latest/dist/ionicons/ionicons.js"></script>
</body>
</html>
