<?php
require 'config.php';
session_start();

$is_activation_needed = false;
$email = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['activation_code'])) {
        // Obsługa aktywacji
        $email = $_POST['email'];
        $activation_code = $_POST['activation_code'];

        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND activation_code = ?");
        $stmt->bind_param("ss", $email, $activation_code);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id);
            $stmt->fetch();

            $update_stmt = $conn->prepare("UPDATE users SET is_active = 1 WHERE id = ?");
            $update_stmt->bind_param("i", $id);
            if ($update_stmt->execute()) {
                echo "<script>alert('Konto zostało aktywowane. Możesz się teraz zalogować.');</script>";
                // Automatyczne logowanie po aktywacji
                $_SESSION['user_id'] = $id;
                header("Location: home.php");
                exit();
            } else {
                echo "<script>alert('Błąd w aktywacji konta.');</script>";
            }
            $update_stmt->close();
        } else {
            echo "<script>alert('Nieprawidłowy kod aktywacyjny.');</script>";
            $is_activation_needed = true;
        }

        $stmt->close();
    } else {
        // Obsługa logowania
        $email = $_POST['email'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT id, password, admin, is_active FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $hashed_password, $admin, $is_active);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                if ($is_active) {
                    $_SESSION['user_id'] = $id;
                    $_SESSION['admin'] = $admin; // Ustawienie klucza 'admin' w sesji
                    if ($admin) {
                        header("Location: admin.php");
                    } else {
                        header("Location: home.php");
                    }
                    exit();
                } else {
                    $is_activation_needed = true;
                }
            } else {
                echo "<script>alert('Nieprawidłowy email lub hasło!');</script>";
            }
        } else {
            echo "<script>alert('Nieprawidłowy email lub hasło!');</script>";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Logowanie</title>
    <link rel="stylesheet" href="../css/uh.css">
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
                        <a href="register.php">Zarejestruj się</>
                <?php endif; ?>
            </div>
        </div>
        <a href="shop.php">Sklep</a>
        <a href="home.php">Strona główna</a>
    </div>
</nav>
<div class="italian-flag"></div>
<h2>Logowanie</h2>
<div class="container">
    <?php if ($is_activation_needed): ?>
        <form action="login.php" method="post">
            <div class="form-group">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                <label for="activation_code">Kod aktywacyjny:</label>
                <input type="text" id="activation_code" name="activation_code" required>
            </div>
            <button type="submit">Aktywuj Konto</button>
        </form>
    <?php else: ?>
        <form action="login.php" method="post">
        <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Hasło:</label>
                <input type="password" id="password" name="password" required>
            </div>
                <div class="form-group">
                <button type="submit">Zaloguj</button>
            </div>
        </form>
    <?php endif; ?>
    <div class="center-link">
        <p>Nie masz konta? <a href="register.php">Zarejestruj się</a></p>
    </div>
    </div>
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
