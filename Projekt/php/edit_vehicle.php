<?php
session_start();

if (!isset($_SESSION['user_id']) || !$_SESSION['admin']) {
    header("Location: login.php");
    exit();
}

require 'config.php';

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vehicle_id = $_POST['vehicle_id'];
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $year = $_POST['year'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    // Validate year
    if (!is_numeric($year) || $year < 1886 || $year > intval(date("Y"))) { // Added realistic range for car manufacturing years
        $errors[] = "Rok musi być liczbą z przedziału od 1886 do obecnego roku.";
    }

    // Validate price
    if (!preg_match('/^\d+(\.\d{1,2})?$/', $price)) {
        $errors[] = "Cena musi być liczbą z maksymalnie dwoma miejscami po przecinku.";
    }

    // Validate brand
    $valid_brands = ['Abarth', 'Alfa Romeo', 'Ferrari', 'Fiat', 'Lamborghini', 'Lancia', 'Maserati', 'Pagani'];
    if (!in_array($brand, $valid_brands)) {
        $errors[] = "Wybrana marka nie jest prawidłowa.";
    }

    // If a new image has been uploaded, update the image path
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $target = "../images/" . basename($image);

        if (strpos($target, '../images/') !== 0) {
            $errors[] = "Nieprawidłowa ścieżka do przesyłanego pliku.";
        }

        if (empty($errors)) {
            // Get the old image path
            $old_image_path_query = "SELECT image FROM vehicles WHERE id=?";
            $old_stmt = $conn->prepare($old_image_path_query);
            $old_stmt->bind_param("i", $vehicle_id);
            $old_stmt->execute();
            $old_stmt->bind_result($old_image_path);
            $old_stmt->fetch();
            $old_stmt->close();

            // Delete the old image if it exists
            if (file_exists($old_image_path)) {
                unlink($old_image_path);
            }

            // Move the new file
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $errors[] = "Nie udało się przesłać zdjęcia.";
            }
        }
    }

    if (empty($errors)) {
        // Update vehicle data in the database
        $update_sql = "UPDATE vehicles SET brand=?, model=?, year=?, price=?, description=?";
        if (!empty($_FILES['image']['name'])) {
            $update_sql .= ", image=?";
        }
        $update_sql .= " WHERE id=?";
        $update_stmt = $conn->prepare($update_sql);

        if (!empty($_FILES['image']['name'])) {
            $update_stmt->bind_param("ssisssi", $brand, $model, $year, $price, $description, $target, $vehicle_id);
        } else {
            $update_stmt->bind_param("ssissi", $brand, $model, $year, $price, $description, $vehicle_id);
        }

        if ($update_stmt->execute()) {
            header("Location: vehicles.php");
        } else {
            $errors[] = "Error: " . $update_stmt->error;
        }

        $update_stmt->close();
    }
}

// Get vehicle data for editing
if (isset($_GET['id'])) {
    $vehicle_id = $_GET['id'];
    $select_sql = "SELECT * FROM vehicles WHERE id=?";
    $select_stmt = $conn->prepare($select_sql);
    $select_stmt->bind_param("i", $vehicle_id);
    $select_stmt->execute();
    $result = $select_stmt->get_result();
    $vehicle = $result->fetch_assoc();
    $select_stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edycja pojazdu</title>
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
            <a href="#" class="scroll-to-about">O nas</a>
            <a href="#" class="scroll-to-contact">Kontakt</a>
        </div>
    </nav>
    <div class="italian-flag"></div> 
    <h2>Edytujesz pojazd</h2>
    <div class="container">
        <form action="edit_vehicle.php" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
            <input type="hidden" name="vehicle_id" value="<?php echo htmlspecialchars($vehicle['id']); ?>">
            <label for="brand">Marka:</label>
            <select id="brand" name="brand" required>
                <option value="">Wybierz markę</option>
                <option value="Abarth" <?php if ($vehicle['brand'] === 'Abarth') echo 'selected'; ?>>Abarth</option>
                <option value="Alfa Romeo" <?php if ($vehicle['brand'] === 'Alfa Romeo') echo 'selected'; ?>>Alfa Romeo</option>
                <option value="Ferrari" <?php if ($vehicle['brand'] === 'Ferrari') echo 'selected'; ?>>Ferrari</option>
                <option value="Fiat" <?php if ($vehicle['brand'] === 'Fiat') echo 'selected'; ?>>Fiat</option>
                <option value="Lamborghini" <?php if ($vehicle['brand'] === 'Lamborghini') echo 'selected'; ?>>Lamborghini</option>
                <option value="Lancia" <?php if ($vehicle['brand'] === 'Lancia') echo 'selected'; ?>>Lancia</option>
                <option value="Maserati" <?php if ($vehicle['brand'] === 'Maserati') echo 'selected'; ?>>Maserati</option>
                <option value="Pagani" <?php if ($vehicle['brand'] === 'Pagani') echo 'selected'; ?>>Pagani</option>
            </select>
            <br>
            <label for="model">Model:</label>
            <input type="text" id="model" name="model" value="<?php echo htmlspecialchars($vehicle['model']); ?>" required>
            <br>
            <label for="year">Rok:</label>
            <input type="number" id="year" name="year" value="<?php echo htmlspecialchars($vehicle['year']); ?>" required>
            <br>
            <label for="price">Cena:</label>
            <input type="number" step="0.01" id="price" name="price" value="<?php echo htmlspecialchars($vehicle['price']); ?>" required>
            <br>
            <label for="image">Zdjęcie:</label>
            <input type="file" id="image" name="image">
            <br>
            <label for="description">Opis:</label>
            <textarea id="description" name="description" required><?php echo htmlspecialchars($vehicle['description']); ?></textarea>
            <br>
            <button type="submit">Zapisz zmiany</button>
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
    <p><a href="vehicles.php">Powrót do zarządzania pojazdami</a></p>
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
    <script src="../Js/menu.js"></script>
    <script>
        function validateForm() {
            const year = document.getElementById('year').value;
            const price = document.getElementById('price').value;
            const brand = document.getElementById('brand').value;
            const validBrands = ["Abarth", "Alfa Romeo", "Ferrari", "Fiat", "Lamborghini", "Lancia", "Maserati", "Pagani"];

            if (!/^\d+$/.test(year) || year < 1886 || year > new Date().getFullYear()) {
                alert("Rok musi być liczbą z przedziału od 1886 do obecnego roku.");
                return false;
            }

            if (!/^\d+(\.\d{1,2})?$/.test(price)) {
                alert("Cena musi być liczbą z maksymalnie dwoma miejscami po przecinku.");
                return false;
            }

            if (!validBrands.includes(brand)) {
                alert("Wybrana marka nie jest prawidłowa.");
                return false;
            }

            return true;
        }
    </script>
</body>
</html>
