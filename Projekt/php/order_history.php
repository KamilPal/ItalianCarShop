<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT orders.id, vehicles.brand, vehicles.model, orders.purchase_date, orders.name, orders.surname, orders.email, orders.country, orders.city, orders.address, orders.payment 
        FROM orders 
        JOIN vehicles ON orders.vehicle_id = vehicles.id 
        WHERE orders.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historia Zamówień</title>
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
<div class="container">
<a href="profile.php">Powrót do profilu</a>
    <h2>Historia Zamówień</h2>
    <table>
        <tr>
            <th>Marka</th>
            <th>Model</th>
            <th>Data Zakupu</th>
            <th>Imię</th>
            <th>Nazwisko</th>
            <th>Email</th>
            <th>Kraj</th>
            <th>Miasto</th>
            <th>Adres</th>
            <th>Płatność</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['brand']) . "</td>";
                echo "<td>" . htmlspecialchars($row['model']) . "</td>";
                echo "<td>" . htmlspecialchars($row['purchase_date']) . "</td>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['surname']) . "</td>";
                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                echo "<td>" . htmlspecialchars($row['country']) . "</td>";
                echo "<td>" . htmlspecialchars($row['city']) . "</td>";
                echo "<td>" . htmlspecialchars($row['address']) . "</td>";
                echo "<td>" . htmlspecialchars($row['payment']) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='10'>Brak zamówień</td></tr>";
        }
        ?>
    </table>
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
<script src="../Js/menu.js"></script>
</body>
</html>
