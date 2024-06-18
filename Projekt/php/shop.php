<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sklep</title>
    <link rel="stylesheet" href="../css/general.css">
    <link rel="stylesheet" href="../css/shop.css">
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
    <h2 style="text-align: center;">Wybierz producenta:</h2>
    <div style="text-align: center; margin-bottom: 5px;">
        <button onclick="window.location.href='all_cars.php'" style="color: white; background-color:#1f1f1f; padding: 10px 20px;">Wyświetl wszystkie pojazdy</button>
    </div>
    <div class="container">
        <div id="abarth" class="tile" style="--tile-color: #d81517;">
            <img src="../CarEmblems/abarth.png" alt="Logo Abarth">
            <div class="color-indicator"></div>
            <div class="brand-name">Abarth</div>
        </div>
        <div id="alfa romeo" class="tile" style="--tile-color: #9b1b2a;">
            <img src="../CarEmblems/alfa romeo.png" alt="Logo Alfa Romeo">
            <div class="color-indicator"></div>
            <div class="brand-name">Alfa Romeo</div>
        </div>
        <div id="ferrari" class="tile" style="--tile-color: #e72b36;">
            <img src="../CarEmblems/Ferrari.png" alt="Logo Ferrari">
            <div class="color-indicator"></div>
            <div class="brand-name">Ferrari</div>
        </div>
        <div id="fiat" class="tile" style="--tile-color: #032985;">
            <img src="../CarEmblems/fiat.png" alt="Logo Fiat">
            <div class="color-indicator"></div>
            <div class="brand-name">Fiat</div>
        </div>
        <div id="lamborghini" class="tile" style="--tile-color: #FFD700;">
            <img src="../CarEmblems/lamborghini.png" alt="Logo Lamborghini">
            <div class="color-indicator"></div>
            <div class="brand-name">Lamborghini</div>
        </div>
        <div id="lancia" class="tile" style="--tile-color: #001854;">
            <img src="../CarEmblems/lancia.png" alt="Logo Lancia">
            <div class="color-indicator"></div>
            <div class="brand-name">Lancia</div>
        </div>
        <div id="maserati" class="tile" style="--tile-color: #0c2e59">
            <img src="../CarEmblems/maserati.png" alt="Logo Maserati">
            <div class="color-indicator"></div>
            <div class="brand-name">Maserati</div>
        </div>
        <div id="pagani" class="tile" style="--tile-color: #bfc2c5;">
            <img src="../CarEmblems/pagani.png" alt="Logo Pagani">
            <div class="color-indicator"></div>
            <div class="brand-name">Pagani</div>
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
    <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@latest/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@latest/dist/ionicons/ionicons.js"></script>
    <script>
        document.querySelectorAll('.tile').forEach(tile => {
            tile.addEventListener('click', function() {
                const brand = this.id;
                window.location.href = `cars.php?brand=${brand}`; // Redirect to a specific brand page
            });
        });
    </script>
    <script src="../Js/menu.js"></script>
</body>
</html>
