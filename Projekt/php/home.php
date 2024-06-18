<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Strona główna</title>
    <link rel="stylesheet" href="../css/general.css">
    <link rel="stylesheet" href="../css/home.css">
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
            <a href="#" class="scroll-to-about">O nas</a>
            <a href="#" class="scroll-to-contact">Kontakt</a>
        </div>
    </nav>
    <div class="italian-flag"></div>
    <div class="Baner">
        <img src="../Addons/enzo.jpg" alt="Banner Image 1" class="left-image">
        <img src="../Addons/f1-2023-ferrari.jpg" alt="Banner Image 2" class="right-image">
    </div>                
    <div id="about-section" class="about-section">
        <h2>O nas</h2>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
    </div>
    <div id='contact-info'class="contact-info-map">
        <div class="contact-info">
            <h2>Skontaktuj się z nami</h2>
            <p>Email: kontakt@przyklad.com</p>
            <p>Telefon: +48 123 456 789</p>
            <p>Gdzie się znajdujemy: <br>
            Viale di Vedano, 5, 20900 Monza MB, Włochy</p>
        </div>
        <div class="map-container">
        <iframe 
    src=https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d10682.432350141706!2d9.278797!3d45.620431!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4786ba360e48bd7d%3A0x645e7ef5a9d3a632!2sAutodromo%20Nazionale%20di%20Monza!5e1!3m2!1spl!2sus!4v1718473560009!5m2!1spl!2sus"
    width="100%"
    height="100%"
    style="border:0;"
    allowfullscreen=""
    loading="lazy">
</iframe>
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
    <script>
    document.addEventListener("DOMContentLoaded", function() {
    var links = document.querySelectorAll('.scroll-to-about');
    links.forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            var target = document.querySelector('.about-section');
            target.scrollIntoView({ behavior: 'smooth' });
        });
    });
    });

    document.addEventListener("DOMContentLoaded", function() {
    var links = document.querySelectorAll('.scroll-to-contact');
    links.forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            var target = document.querySelector('.contact-info-map');
            target.scrollIntoView({ behavior: 'smooth' });
        });
    });
    });
    </script>
    <script src="../Js/menu.js"></script>
    <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@latest/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@latest/dist/ionicons/ionicons.js"></script>
</body>
</html>
