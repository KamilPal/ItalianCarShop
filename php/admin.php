<?php
session_start();

if (!isset($_SESSION['user_id']) || !$_SESSION['admin']) {
    header("Location: login.php");
    exit();
}

require 'config.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
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
                        <a href="register.php">Zarejestruj się</a>
                    <?php endif; ?>
                </div>
            </div>
            <a href="shop.php">Sklep</a>
            <a href="home.php">Strona główna</a>
        </div>
    </nav>
    <div class="italian-flag"></div>
    <h2>Admin Panel</h2>
    <div class="container">
        <div class="button-group">
            <div class="horizontal">
            <div class="form-group">
                <a href='manage_users.php'class="button">Zarządzaj użytkownikami</a>
            </div>
            <div class="form-group">
                <a href='vehicles.php' class="button">Zarządzaj pojadami</a>
            </div>
            <div class="form-group">
                <a href='home.php' class="button">Strona główna</a>
            </div>    
            <div class="form-group">
                <a href='logout.php' class="button">Wyloguj</a>
            </div>
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

<?php
$conn->close();
?>
