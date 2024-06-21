<?php
session_start();

if (!isset($_SESSION['user_id']) || !$_SESSION['admin']) {
    header("Location: login.php");
    exit();
}

require 'config.php';

$errors = [];

// Pobieranie listy obrazów z folderu images
$images_dir = realpath(dirname(__FILE__) . '/../images') . '/';
$images = array_diff(scandir($images_dir), array('.', '..'));

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $year = $_POST['year'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $image = $_POST['image'];

    // Walidacja daty
    if (!is_numeric($year) || $year < 1886 || $year > intval(date("Y"))) { // Dodanie minimalnej daty, ponieważ wtedy zaczęto produkować pojazdy
        $errors[] = "Rok musi być liczbą z przedziału od 1886 do obecnego roku.";
    }

    // Walidacja ceny
    if (!preg_match('/^\d+(\.\d{1,2})?$/', $price)) {
        $errors[] = "Cena musi być liczbą z maksymalnie dwoma miejscami po przecinku.";
    }

    // Sprawdzanie marki
    $valid_brands = ['Abarth', 'Alfa Romeo', 'Ferrari', 'Fiat', 'Lamborghini', 'Lancia', 'Maserati', 'Pagani'];
    if (!in_array($brand, $valid_brands)) {
        $errors[] = "Wybrana marka nie jest prawidłowa.";
    }

    // Sprawdzenie, czy wybrany plik istnieje w katalogu images
    if (!in_array($image, $images)) {
        $errors[] = "Wybrany obraz nie jest prawidłowy.";
    }

    if (empty($errors)) {
        //Dodanie do bazy danych
        $sql = "INSERT INTO vehicles (brand, model, year, price, image, description) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        // Połączenie parametrów
        $stmt->bind_param("ssisss", $brand, $model, $year, $price, $image, $description);

        if ($stmt->execute()) {
            header("Location: vehicles.php");
        } else {
            $errors[] = "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Wprowadzanie nowego pojazdu</title>
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
            <a href="home.php">Strona główna</a>
        </div>
    </nav>
    <div class="italian-flag"></div>
    <h2>Wprowadzasz nowy pojazd</h2>
    <div class="container">
        <form action="add_vehicle.php" method="post" onsubmit="return validateForm()">
            <div class="form-group">
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
            </div>
            <div class="form-group">
                <label for="model">Model:</label>
                <input type="text" id="model" name="model" required>
            </div>
            <div class="form-group">
                <label for="year">Rok:</label>
                <input type="number" id="year" name="year" required>
            </div>
            <div class="form-group">
                <label for="price">Cena:</label>
                <input type="number" step="0.01" id="price" name="price" required>
            </div>
            <div class="form-group">
                <label for="image">Zdjęcie:</label>
                <input type="hidden" id="image" name="image" required>
                <button type="button" onclick="openModal()">Wybierz zdjęcie</button>
                <div id="selected-image"></div>
            </div>
            <div class="form-group">
                <label for="description">Opis:</label>
                <textarea id="description" name="description" required></textarea>
            </div>
            <div class="form-group button-group">
                <button type="submit">Dodaj pojazd</button>
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
            <a href="vehicles.php">Wróć do zarządzania pojazdami</a>
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
    <script>
        function validateForm() {
            const year = document.getElementById('year').value;
            const price = document.getElementById('price').value;
            const brand = document.getElementById('brand').value;
            const image = document.getElementById('image').value;
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

            if (image === "") {
                alert("Musisz wybrać zdjęcie.");
                return false;
            }

            return true;
        }

        // Modalna funkcjonalność wyboru obrazu
        function openModal() {
            document.getElementById('myModal').style.display = "block";
        }

        function closeModal() {
            document.getElementById('myModal').style.display = "none";
        }

        function selectImage(image) {
            document.getElementById('image').value = image;
            document.getElementById('selected-image').innerHTML = `<img src="../images/${image}" alt="${image}" style="max-width: 100px;">`;
            closeModal();
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('myModal')) {
                closeModal();
            }
        }
    </script>

    <!-- Modal do wyboru obrazu -->
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
</body>
</html>