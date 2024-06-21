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
    $image = $_POST['image'];

    // Walidacja daty
    if (!is_numeric($year) || $year < 1886 || $year > intval(date("Y"))) { 
        $errors[] = "Rok musi być liczbą z przedziału od 1886 do obecnego roku.";
    }

    // Walidacja ceny
    if (!preg_match('/^\d+(\.\d{1,2})?$/', $price)) {
        $errors[] = "Cena musi być liczbą z maksymalnie dwoma miejscami po przecinku.";
    }

    // Walidacja marki
    $valid_brands = ['Abarth', 'Alfa Romeo', 'Ferrari', 'Fiat', 'Lamborghini', 'Lancia', 'Maserati', 'Pagani'];
    if (!in_array($brand, $valid_brands)) {
        $errors[] = "Wybrana marka nie jest prawidłowa.";
    }

    if (empty($errors)) {
        // Aktualizacja pojazdu
        $update_sql = "UPDATE vehicles SET brand=?, model=?, year=?, price=?, description=?, image=? WHERE id=?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssisssi", $brand, $model, $year, $price, $description, $image, $vehicle_id);

        if ($update_stmt->execute()) {
            header("Location: vehicles.php");
        } else {
            $errors[] = "Error: " . $update_stmt->error;
        }

        $update_stmt->close();
    }
}

// Pobranie danych pojazdu do edycji
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

$images_dir = realpath(dirname(__FILE__) . '/../images') . '/';
$images = array_diff(scandir($images_dir), array('.', '..'));

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edycja pojazdu</title>
    <link rel="stylesheet" href="../css/uh.css">
    <style>
        .modal {
            display: none; 
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .image-container {
            display: flex;
            flex-wrap: wrap;
        }

        .image-container img {
            max-width: 100px;
            margin: 5px;
            cursor: pointer;
        }
    </style>
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
        <form action="edit_vehicle.php" method="post" onsubmit="return validateForm()">
            <input type="hidden" name="vehicle_id" value="<?php echo htmlspecialchars($vehicle['id']); ?>">
            <div class="form-group">
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
            </div>
            <div class="form-group">
                <label for="model">Model:</label>
                <input type="text" id="model" name="model" value="<?php echo htmlspecialchars($vehicle['model']); ?>" required>
            </div>
            <div class="form-group">
                <label for="year">Rok:</label>
                <input type="number" id="year" name="year" value="<?php echo htmlspecialchars($vehicle['year']); ?>" required>
            </div>
            <div class="form-group">
                <label for="price">Cena:</label>
                <input type="number" step="0.01" id="price" name="price" value="<?php echo htmlspecialchars($vehicle['price']); ?>" required>
            </div>
            <div class="form-group">
                <label for="image">Zdjęcie:</label>
                <input type="hidden" id="image" name="image" value="<?php echo htmlspecialchars($vehicle['image']); ?>" required>
                <button type="button" onclick="openModal()">Wybierz zdjęcie</button>
                <div id="selected-image">
                    <img src="../<?php echo htmlspecialchars($vehicle['image']); ?>" alt="Selected Image" style="max-width: 100px;">
                </div>
            </div>
            <div class="form-group">
                <label for="description">Opis:</label>
                <textarea id="description" name="description" required><?php echo htmlspecialchars($vehicle['description']); ?></textarea>
            </div>
            <div class="form-group">
                <button type="submit">Zapisz zmiany</button>
            </div>
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
        <div class="center-link">
            <p><a href="vehicles.php">Powrót do zarządzania pojazdami</a></p>
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

    <!-- Modal -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <div class="image-container">
                <?php foreach ($images as $img): ?>
                    <img src="../images/<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($img); ?>" onclick="selectImage('<?php echo htmlspecialchars($img); ?>')">
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@latest/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@latest/dist/ionicons/ionicons.js"></script>
    <script src="../Js/menu.js"></script>
    <script>
        function openModal() {
            document.getElementById("myModal").style.display = "block";
        }

        function closeModal() {
            document.getElementById("myModal").style.display = "none";
        }

        function selectImage(image) {
            document.getElementById("image").value = "images/" + image;
            document.getElementById("selected-image").innerHTML = '<img src="../images/' + image + '" alt="Selected Image" style="max-width: 100px;">';
            closeModal();
        }

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
