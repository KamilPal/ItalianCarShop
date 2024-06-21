<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");  
    exit(); 
}

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];  // Przypisanie zawartości koszyka z sesji do zmiennej $cart, jeśli istnieje

$vehicles = [];  // Inicjalizacja pustej tablicy na przechowywanie pojazdów z koszyka
if (!empty($cart)) {
    $ids = implode(',', array_map('intval', $cart));  // Przygotowanie listy ID pojazdów jako ciągu liczb całkowitych
    $sql = "SELECT id, brand, model, year, price, image, description FROM vehicles WHERE id IN ($ids)";  // Zapytanie SQL wybierające pojazdy z bazy danych na podstawie ID
    $result = $conn->query($sql);  // Wykonanie zapytania SQL
    while ($row = $result->fetch_assoc()) {
        $vehicles[] = $row;  // Dodanie wyników zapytania do tablicy $vehicles
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_id'])) {
    $remove_id = intval($_POST['remove_id']);  // Pobranie ID pojazdu do usunięcia z formularza POST i rzutowanie na liczbę całkowitą
    if (($key = array_search($remove_id, $_SESSION['cart'])) !== false) {
        unset($_SESSION['cart'][$key]);  // Usunięcie ID pojazdu z tablicy koszyka w sesji
    }
    header("Location: cart.php");  // Przekierowanie użytkownika z powrotem do strony koszyka
    exit();  // Zakończenie wykonywania skryptu
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koszyk</title>
    <link rel="stylesheet" href="../css/general.css">
    <link rel="stylesheet" href="../css/cars.css">
    <style>
        .close-icon {
            position: absolute;
            top: 5px;
            right: 5px;
            cursor: pointer;
            color: black;
            font-size: 20px;
        }
        .close-icon:hover {
            color: red;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-left">
            <a href="shop.php"><img src="../Addons/logo.png" alt="Logo" class="navbar-logo"></a>
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
            <a href="cart.php">Koszyk</a>
        </div>
    </nav>
    <div class="italian-flag"></div>
    <h2 style="text-align: center;">Koszyk</h2>
    <form action="purchase.php" method="post" style="text-align: center;">
        <button type="submit" class="purchase-button">Przejdź do zakupu</button>
    </form>
    <div class="container">
        <div class="vehicles">
            <?php if (!empty($vehicles)): ?>
                <?php foreach ($vehicles as $vehicle): ?>
                    <div class="vehicle">
                        <div class="close-icon" onclick="removeFromCart(<?php echo $vehicle['id']; ?>)">&times;</div>
                        <img src="<?php echo $vehicle['image']; ?>" alt="<?php echo $vehicle['model']; ?>">
                        <div class="vehicle-info">
                            <div class="left-info">
                                <h2><?php echo $vehicle['model']; ?></h2>
                            </div>
                            <div class="right-info">
                                <p>Cena: <?php echo $vehicle['price']; ?>zł</p>
                                <form action="cart.php" method="post" id="removeForm<?php echo $vehicle['id']; ?>">
                                    <input type="hidden" name="remove_id" value="<?php echo $vehicle['id']; ?>">
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Twój koszyk jest pusty.</p>
            <?php endif; ?>
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
        document.getElementById('login-trigger').addEventListener('click', function() {
            var loginDropdown = document.getElementById('login-dropdown');
            loginDropdown.style.display = loginDropdown.style.display === 'none' ? 'block' : 'none';
        });

        function removeFromCart(vehicleId) {
            const form = document.getElementById('removeForm' + vehicleId);
            form.submit();
        }
    </script>
</body>
</html>
