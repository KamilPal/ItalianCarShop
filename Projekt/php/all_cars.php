<?php
session_start();
include('config.php');

// Pobierz dane z formularza wyszukiwania, jeśli zostały przesłane
$brand = isset($_GET['brand']) ? $_GET['brand'] : '';
$model = isset($_GET['model']) ? $_GET['model'] : '';
$year = isset($_GET['year']) ? $_GET['year'] : '';
$price_from = isset($_GET['price_from']) ? $_GET['price_from'] : '';
$price_to = isset($_GET['price_to']) ? $_GET['price_to'] : '';

// Przygotuj zapytanie SQL z parametrami wyszukiwania
$sql = "SELECT id, brand, model, year, price, image, description FROM vehicles WHERE 1=1";

$params = [];
$types = '';

if ($brand) {
    $sql .= " AND brand LIKE ?";
    $params[] = "%" . $brand . "%";
    $types .= 's';
}
if ($model) {
    $sql .= " AND model LIKE ?";
    $params[] = "%" . $model . "%";
    $types .= 's';
}
if ($year) {
    $sql .= " AND year = ?";
    $params[] = $year;
    $types .= 'i';
}
if ($price_from) {
    $sql .= " AND price >= ?";
    $params[] = $price_from;
    $types .= 'd';
}
if ($price_to) {
    $sql .= " AND price <= ?";
    $params[] = $price_to;
    $types .= 'd';
}

// Przygotuj i wykonaj zapytanie
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wszystkie Pojazdy</title>
    <link rel="stylesheet" href="../css/general.css">
    <link rel="stylesheet" href="../css/cars.css">
    <link rel="stylesheet" href="../css/button.css">
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
    <h2 style="text-align: center;">Wszystkie Pojazdy</h2>
    <div style="text-align: center; margin-bottom: 5px;">
        <button onclick="window.location.href='shop.php'" style="background-color:#1f1f1f;">Wróć do producentów</button>
        <button onclick="window.location.href='all_cars.php'" style="background-color:#1f1f1f;">Wyświetl wszystkie pojazdy</button>
    </div>
    <div class="container">
        <form action="all_cars.php" method="GET" class="search-form">
        <label for="brand">Marka:</label>
            <input type="text" id="brand" name="brand" value="<?php echo htmlspecialchars($brand); ?>">
            <label for="model">Model:</label>
            <input type="text" id="model" name="model" value="<?php echo htmlspecialchars($model); ?>">
            <label for="year">Rok:</label>
            <input type="number" id="year" name="year" value="<?php echo htmlspecialchars($year); ?>">
            <label for="price_from">Cena od:</label>
            <input type="number" id="price_from" name="price_from" value="<?php echo isset($_GET['price_from']) ? htmlspecialchars($_GET['price_from']) : ''; ?>">
            <label for="price_to">Cena do:</label>
            <input type="number" id="price_to" name="price_to" value="<?php echo isset($_GET['price_to']) ? htmlspecialchars($_GET['price_to']) : ''; ?>">
            <button type="submit">Szukaj</button>
        </form>
        <div class="vehicles">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<div class='vehicle' onclick='openModal(" . $row["id"] . ")'>";
                    echo "<img src='" . $row["image"] . "' alt='" . $row["model"] . "' onclick='openModal(" . $row["id"] . ")'>";
                    echo "<div class='vehicle-info'>";
                    echo "<div class='left-info'>";
                    echo "<h2>" . $row["model"] . "</h2>";
                    echo "</div>";
                    echo "<div class='right-info'>";
                    echo "<p>Cena: " . $row["price"] . "zł</p>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                
                    // Modal content
                    echo "<div id='modal-" . $row["id"] . "' class='modal'>";
                    echo "<div class='modal-content'>";
                    echo "<span class='close' onclick='closeModal(" . $row["id"] . ")'>&times;</span>";
                    echo "<img src='" . $row["image"] . "' alt='" . $row["model"] . "'>";
                    echo "<div class='details'>";
                    echo "<h2>" . $row["brand"] . " " . $row["model"] . "</h2>";
                    echo "<p>Rok: " . $row["year"] . "</p>";
                    echo "<p>Cena: " . $row["price"] . "zł</p>";
                    echo "<p>Opis: " . $row["description"] . "</p>";
                    if (isset($_SESSION['user_id'])) {
                        if (!in_array($row['id'], $_SESSION['cart'] ?? [])) {
                            echo "<button class='cart-button' onclick='addToCart(" . $row["id"] . ")'>Dodaj do koszyka</button>";
                        } else {
                            echo "<button class='cart-button' onclick='removeFromCart(" . $row["id"] . ")'>Usuń z koszyka</button>";
                        }
                    } else {
                        echo "<p>Musisz być zalogowany, aby dodać ten pojazd do koszyka.</p>";
                    }
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "Nie znaleziono pojazdów dla marki: " . htmlspecialchars($brand);
            }
            $stmt->close();
            $conn->close();
        ?>
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
        function openModal(id) {
            document.getElementById('modal-' + id).style.display = 'block';
            document.body.classList.add('modal-open');
        }

        function closeModal(id) {
            document.getElementById('modal-' + id).style.display = 'none';
            document.body.classList.remove('modal-open');
        }

        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
                document.body.classList.remove('modal-open');
            }
        }

        function addToCart(vehicleId) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'add_to_cart.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    alert('Pojazd został dodany do koszyka.');
                    window.location.reload();
                } else {
                    alert('Wystąpił błąd przy dodawaniu pojazdu do koszyka.');
                }
            };
            xhr.send('vehicle_id=' + vehicleId);
        }

        function removeFromCart(vehicleId) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'remove_from_cart.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    alert('Pojazd został usunięty z koszyka.');
                    window.location.reload();
                } else {
                    alert('Wystąpił błąd przy usuwaniu pojazdu z koszyka.');
                }
            };
            xhr.send('vehicle_id=' + vehicleId);
        }
    </script>
        <script src="../Js/menu.js"></script>
</body>
</html>
