<?php
session_start();

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "Twój koszyk jest pusty.";
    exit();
}

include('config.php');

$cart = $_SESSION['cart'];
$placeholders = implode(',', array_fill(0, count($cart), '?'));
$sql = "SELECT id, brand, model, year, price, image FROM vehicles WHERE id IN ($placeholders)";

$stmt = $conn->prepare($sql);
$stmt->bind_param(str_repeat('i', count($cart)), ...$cart);
$stmt->execute();
$result = $stmt->get_result();

echo "<h2>Twój koszyk</h2>";
echo "<div class='cart-items'>";
while ($row = $result->fetch_assoc()) {
    echo "<div class='cart-item'>";
    echo "<img src='" . $row['image'] . "' alt='" . $row['model'] . "'>";
    echo "<div class='cart-info'>";
    echo "<h3>" . $row['brand'] . " " . $row['model'] . "</h3>";
    echo "<p>Cena: " . $row['price'] . "zł</p>";
    echo "</div>";
    echo "</div>";
}
echo "</div>";

$stmt->close();
$conn->close();

