<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "Musisz być zalogowany, aby dodać pojazd do koszyka.";
    exit();
}

$vehicle_id = $_POST['vehicle_id'];

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (!in_array($vehicle_id, $_SESSION['cart'])) {
    $_SESSION['cart'][] = $vehicle_id;
    echo "Pojazd został dodany do koszyka.";
} else {
    echo "Pojazd jest już w koszyku.";
}
