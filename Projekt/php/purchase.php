<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$cart = $_SESSION['cart'];

// Pobierz dane użytkownika
$sql_user = "SELECT name, surname, email FROM users WHERE id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();

// Pobierz dane pojazdów
$vehicles = [];
$total_price = 0;
if (!empty($cart)) {
    $ids = implode(',', array_map('intval', $cart));
    $sql_vehicles = "SELECT id, brand, model, year, price, image, description FROM vehicles WHERE id IN ($ids)";
    $result_vehicles = $conn->query($sql_vehicles);
    while ($row = $result_vehicles->fetch_assoc()) {
        $vehicles[] = $row;
        $total_price += $row['price'];
    }
}

$stmt_user->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formularz Zakupu</title>
    <link rel="stylesheet" href="../css/uh.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-left">
            <a href="shop.php"><img src="../Addons/logo.png" alt="Logo" class="navbar-logo"></a>
        </div>
        <div class="navbar-right">
            <a href="shop.php">Sklep</a>
            <a href="home.php">Strona główna</a>
        </div>
    </nav>
    <div class="italian-flag"></div>
    <h2 style="text-align: center;">Formularz Zakupu</h2>
    <div class="center-link">
    <a href="cart.php">Powrót do koszyka</a>
    </div>
    <div class="container">
        <div class="purchase-form">
            <form action="save_order.php" method="post" onsubmit="return validateForm()">
                <input type="hidden" name="total_price" value="<?php echo $total_price; ?>">
                <div class="form-group">
                    <label for="name">Imię:</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="surname">Nazwisko:</label>
                    <input type="text" id="surname" name="surname" value="<?php echo htmlspecialchars($user['surname']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="country">Kraj:</label>
                    <input type="text" id="country" name="country" required>
                </div>
                <div class="form-group">
                    <label for="city">Miasto:</label>
                    <input type="text" id="city" name="city" required>
                </div>
                <div class="form-group">
                    <label for="address">Adres:</label>
                    <input type="text" id="address" name="address" required>
                </div>
                <div class="form-group">
                    <label for="payment">Metoda płatności:</label>
                    <select id="payment" name="payment" required>
                        <option value="karta">Karta</option>
                        <option value="przelew">Przelew</option>
                        <option value="gotowka">Gotówka</option>
                    </select>
                </div>
                <div class="purchase-items">
                    <h3>Twoje zakupy:</h3>
                    <?php foreach ($vehicles as $vehicle): ?>
                        <p><?php echo htmlspecialchars($vehicle['brand'] . ' ' . $vehicle['model'] . ' - ' . $vehicle['price'] . 'zł'); ?></p>
                        <input type="hidden" name="vehicle_ids[]" value="<?php echo $vehicle['id']; ?>">
                    <?php endforeach; ?>
                    <p>Łączna kwota: <?php echo $total_price; ?>zł</p>
                </div>
                <button type="submit" class="purchase-button">Złóż zamówienie</button>
            </form>
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
        function validateForm() {
            var name = document.getElementById("name").value;
            var surname = document.getElementById("surname").value;
            var email = document.getElementById("email").value;
            var country = document.getElementById("country").value;
            var city = document.getElementById("city").value;
            var address = document.getElementById("address").value;
            var payment = document.getElementById("payment").value;
            var errors = [];

            if (!name.match(/^[a-zA-Z]{3,}$/)) {
                errors.push("Imię powinno zawierać tylko litery i mieć co najmniej 3 znaki.");
            }

            if (!surname.match(/^[a-zA-Z]{3,}$/)) {
                errors.push("Nazwisko powinno zawierać tylko litery i mieć co najmniej 3 znaki.");
            }

            if (!email.match(/^\S+@\S+\.\S+$/)) {
                errors.push("Email powinien być w prawidłowym formacie.");
            }

            if (country.length < 3) {
                errors.push("Kraj powinien mieć co najmniej 3 znaki.");
            }

            if (city.length < 2) {
                errors.push("Miasto powinno mieć co najmniej 2 znaki.");
            }

            if (address.length < 5) {
                errors.push("Adres powinien mieć co najmniej 5 znaków.");
            }

            if (!payment) {
                errors.push("Proszę wybrać metodę płatności.");
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
