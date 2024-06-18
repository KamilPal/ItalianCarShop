<?php
session_start();

if (!isset($_SESSION['user_id']) || !$_SESSION['admin']) {
    header("Location: login.php");
    exit();
}

require 'config.php';

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $year = $_POST['year'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $image = $_FILES['image']['name'];

    // Validate year and price
    if (!is_numeric($year) || !is_numeric($price)) {
        $errors[] = "Rok i cena muszą być liczbami.";
    }

    // Validate brand
    $valid_brands = ['Abarth', 'Alfa Romeo', 'Ferrari', 'Fiat', 'Lamborghini', 'Lancia', 'Maserati', 'Pagani'];
    if (!in_array($brand, $valid_brands)) {
        $errors[] = "Wybrana marka nie jest prawidłowa.";
    }

    // Validate image path
    $target = "../images/" . basename($image);
    if (strpos($target, '../images/') !== 0) {
        $errors[] = "Nieprawidłowa ścieżka do przesyłanego pliku.";
    }

    if (empty($errors)) {
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            // Build SQL query
            $sql = "INSERT INTO vehicles (brand, model, year, price, image, description) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            // Bind parameters
            $stmt->bind_param("ssisss", $brand, $model, $year, $price, $target, $description);

            if ($stmt->execute()) {
                header("Location: vehicles.php");
            } else {
                $errors[] = "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $errors[] = "Failed to upload image.";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Wprowadzanie nowego pojazdu</title>
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
    <h2>Wprowadzasz nowy pojazd</h2>
    <div class="container">
        <form action="add_vehicle.php" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
            <label for="brand">Marka:</label>
            <select id="brand" name="brand" required>
                <option value="">Wybierz markę</option>
                <option value="Abarth">Abarth</option>
                <option value="Alfa Romeo">Alfa Romeo</option>
                <option value="Ferrari">Ferrari</option>
                <option value="Fiat">Fiat</option>
                <option value="Lamborghini">Lamborghini</option>
                <option value="Lancia">Lancia</option>
                <option value="Maserati">Maserati</option>
                <option value="Pagani">Pagani</option>
            </select>
            <br>
            <label for="model">Model:</label>
            <input type="text" id="model" name="model" required>
            <br>
            <label for="year">Rok:</label>
            <input type="number" id="year" name="year" required>
            <br>
            <label for="price">Cena:</label>
            <input type="number" id="price" name="price" required>
            <br>
            <label for="image">Zdjęcie:</label>
            <input type="file" id="image" name="image" required>
            <br>
            <label for="description">Opis:</label>
            <textarea id="description" name="description" required></textarea>
            <br>
            <button type="submit">Dodaj pojazd</button>
        </form>
        <?php
        if (!empty($errors)) {
            echo '<div class="errors">';
            foreach ($errors as $error) {
                echo '<p>' . htmlspecialchars($error) . '</p>';
            }
            echo '</div>';
        }
        ?>
    </div>
    <a href="vehicles.php">Wróć do zarządzania pojazdami</a>
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
    <script>
        function validateForm() {
            var year = document.getElementById("year").value;
            var price = document.getElementById("price").value;
            var image = document.getElementById("image").value;
            var errors = [];

            if (isNaN(year) || isNaN(price)) {
                errors.push("Rok i cena muszą być liczbami.");
            }

            if (!image.startsWith('../images/')) {
                errors.push("Zdjęcie musi znajdować się w katalogu ../images/");
            }

            if (errors.length > 0) {
                alert(errors.join("\n"));
                return false;
            }

            return true;
        }
    </script>
</body>
</html>
