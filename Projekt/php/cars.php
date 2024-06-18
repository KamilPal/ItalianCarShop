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
$sql = "SELECT id, brand, model, year, price, image, description FROM vehicles WHERE brand=?";
$params = [$brand];
$types = 's';

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
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pojazdy <?php echo htmlspecialchars($brand); ?></title>
    <link rel="stylesheet" href="../css/general.css">
    <link rel="stylesheet" href="../css/cars.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-left">
            <a href="shop.php"><img src="../CarEmblems/<?php echo htmlspecialchars($brand); ?>.png" alt="Logo" class="navbar-logo"></a>
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
    <h2 style="text-align: center;">Wybierasz pojazdy <?php echo htmlspecialchars(ucfirst($brand)); ?>:</h2>
    <div style="text-align: center; margin-bottom: 5px;">
        <button onclick="window.location.href='shop.php'" style="color: white; background-color:#1f1f1f; padding: 10px 20px;">Wróć do producentów</button>
        <button onclick="window.location.href='all_cars.php'" style="color: white; background-color:#1f1f1f; padding: 10px 20px;">Wyświetl wszystkie pojazdy</button>
    </div>
    <div class="container">
        <form action="all_cars.php" method="GET" class="search-form">
            <input type="hidden" name="brand" value="<?php echo htmlspecialchars($brand); ?>">
            <label for="model">Model:</label>
            <input type="text" id="model" name="model" value="<?php echo htmlspecialchars($model); ?>">
            <label for="year">Rok:</label>
            <input type="number" id="year" name="year" value="<?php echo htmlspecialchars($year); ?>">
            <label for="price_from">Cena od:</label>
            <input type="number" id="price_from" name="price_from" value="<?php echo htmlspecialchars($price_from); ?>">
            <label for="price_to">Cena do:</label>
            <input type="number" id="price_to" name="price_to" value="<?php echo htmlspecialchars($price_to); ?>">
            <button type="submit">Szukaj</button>
        </form>
        <div class="vehicles">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<div class='vehicle' onclick='openModal(" . $row["id"] . ")'>";
                    echo "<img src='" . $row["image"] . "' alt='" . $row["model"] . "'>";
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
                        echo "<form action='purchase.php' method='get'>";
                        echo "<input type='hidden' name='vehicle_id' value='" . $row["id"] . "'>";
                        echo "<button type='submit' class='purchase-button'>Kup</button>";
                        echo "</form>";
                    } else {
                        echo "<p>Musisz być zalogowany, aby kupić ten pojazd.</p>";
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
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                if (event.target == modal) {
                    modal.style.display = "none";
                    document.body.classList.remove('modal-open');
                }
            });
        }
    </script>
    <script src="../Js/menu.js"></script>
</body>
</html>
