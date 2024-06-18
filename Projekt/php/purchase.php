<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['vehicle_id'])) {
    header("Location: shop.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$vehicle_id = $_GET['vehicle_id'];

// Pobierz dane pojazdu
$sql = "SELECT brand, model, year, price, image, description FROM vehicles WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $vehicle_id);
$stmt->execute();
$result = $stmt->get_result();
$vehicle = $result->fetch_assoc();

if (!$vehicle) {
    header("Location: shop.php");
    exit();
}

// Pobierz dane użytkownika
$sql_user = "SELECT name, surname, email FROM users WHERE id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kup Pojazd</title>
    <link rel="stylesheet" href="../css/general.css">
    <link rel="stylesheet" href="../css/cars.css">
    <style>
        .container {
            display: flex;
            flex-direction: row;
            align-items: flex-start;
        }
        .vehicle-details {
            flex: 1;
            padding-right: 20px;
        }
        .vehicle-details img {
            width: 100%;
            height: auto;
        }
        .purchase-form {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .purchase-form form {
            display: flex;
            flex-direction: column;
            width: 80%;
        }
        .purchase-form label,
        .purchase-form input,
        .purchase-form select,
        .purchase-form button {
            margin-bottom: 10px;
            width: 100%;
        }
    </style>
</head>
<body>
<nav class="navbar">
    <!-- (kod nawigacji) -->
</nav>
<div class="italian-flag"></div>
<div class="container">
    <div class="vehicle-details">
        <img src="<?php echo htmlspecialchars($vehicle['image']); ?>" alt="<?php echo htmlspecialchars($vehicle['model']); ?>">
        <h2><?php echo htmlspecialchars($vehicle['brand']) . " " . htmlspecialchars($vehicle['model']); ?></h2>
        <p>Cena: <?php echo htmlspecialchars($vehicle['price']); ?>zł</p>
        <p>Rok: <?php echo htmlspecialchars($vehicle['year']); ?></p>
        <p>Opis: <?php echo htmlspecialchars($vehicle['description']); ?></p>
    </div>
    <div class="purchase-form">
        <h2>Formularz Zakupu</h2>
        <form action="save_order.php" method="post" onsubmit="return validateForm()">
            <input type="hidden" name="vehicle_id" value="<?php echo htmlspecialchars($vehicle_id); ?>">
            <label for="name">Imię:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            <label for="surname">Nazwisko:</label>
            <input type="text" id="surname" name="surname" value="<?php echo htmlspecialchars($user['surname']); ?>" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            <label for="country">Kraj:</label>
            <input type="text" id="country" name="country" required>
            <label for="city">Miasto:</label>
            <input type="text" id="city" name="city" required>
            <label for="address">Adres dostawy:</label>
            <input type="text" id="address" name="address" required>
            <label for="payment">Opcja płatności:</label>
            <select id="payment" name="payment" required>
                <option value="karta">Karta kredytowa</option>
                <option value="przelew">Przelew bankowy</option>
                <option value="gotowka">Gotówka przy odbiorze</option>
            </select>
            <button type="submit">Potwierdź zakup</button>
        </form>
    </div>
</div>
<div class="italian-flag"></div>
<footer class="footer">
    <!-- (kod stopki) -->
</footer>
<script src="../Js/menu.js"></script>
<script>
    function validateForm() {
        const name = document.getElementById('name').value.trim();
        const surname = document.getElementById('surname').value.trim();
        const email = document.getElementById('email').value.trim();
        const country = document.getElementById('country').value.trim();
        const city = document.getElementById('city').value.trim();
        const address = document.getElementById('address').value.trim();
        const payment = document.getElementById('payment').value;

        if (!name) {
            alert("Imię jest wymagane.");
            return false;
        }

        if (!surname) {
            alert("Nazwisko jest wymagane.");
            return false;
        }

        const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
        if (!emailPattern.test(email)) {
            alert("Nieprawidłowy adres email.");
            return false;
        }

        if (!country) {
            alert("Kraj jest wymagany.");
            return false;
        }

        if (!city) {
            alert("Miasto jest wymagane.");
            return false;
        }

        if (!address) {
            alert("Adres dostawy jest wymagany.");
            return false;
        }

        const validPayments = ['karta', 'przelew', 'gotowka'];
        if (!validPayments.includes(payment)) {
            alert("Nieprawidłowa opcja płatności.");
            return false;
        }

        return true;
    }
</script>
</body>
</html>