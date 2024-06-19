<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "Musisz być zalogowany, aby usunąć pojazd z koszyka.";
    exit();
}

$vehicle_id = $_POST['vehicle_id'];

if (isset($_SESSION['cart']) && in_array($vehicle_id, $_SESSION['cart'])) {
    $_SESSION['cart'] = array_diff($_SESSION['cart'], [$vehicle_id]);
    echo "Pojazd został usunięty z koszyka.";
} else {
    echo "Pojazd nie znajduje się w koszyku.";
}
