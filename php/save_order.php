<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $vehicle_ids = $_POST['vehicle_ids'];
    $name = trim($_POST['name']);
    $surname = trim($_POST['surname']);
    $email = trim($_POST['email']);
    $country = trim($_POST['country']);
    $city = trim($_POST['city']);
    $street = trim($_POST['street']);
    $house_number = trim($_POST['house_number']);
    $payment = $_POST['payment'];

    $errors = [];

    // Walidacja danych
    if (empty($name) || !preg_match('/^[a-zA-ZąęćńłóśżźĄĘĆŃŁÓŚŻŹ\s]+$/', $name) || strlen($name) < 3) {
        $errors[] = "Imię powinno zawierać tylko litery i mieć co najmniej 3 znaki.";
    }
    if (empty($surname) || !preg_match('/^[a-zA-ZąęćńłóśżźĄĘĆŃŁÓŚŻŹ\s]+$/', $surname) || strlen($surname) < 3) {
        $errors[] = "Nazwisko powinno zawierać tylko litery i mieć co najmniej 3 znaki.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Nieprawidłowy adres email.";
    }
    if (empty($country) || !preg_match('/^[a-zA-ZąęćńłóśżźĄĘĆŃŁÓŚŻŹ\s]+$/', $country) || strlen($country) < 3) {
        $errors[] = "Kraj powinien mieć co najmniej 3 znaki i zawierać tylko litery.";
    }
    if (empty($city) || !preg_match('/^[a-zA-ZąęćńłóśżźĄĘĆŃŁÓŚŻŹ\s]+$/', $city) || strlen($city) < 2) {
        $errors[] = "Miasto powinno mieć co najmniej 2 znaki i zawierać tylko litery.";
    }
    if (empty($street) || !preg_match('/^[a-zA-ZąęćńłóśżźĄĘĆŃŁÓŚŻŹ\s]+$/', $street) || strlen($street) < 3) {
        $errors[] = "Ulica powinna mieć co najmniej 3 znaki i zawierać tylko litery.";
    }
    if (empty($house_number) || !preg_match('/^[0-9]+$/', $house_number)) {
        $errors[] = "Numer domu powinien zawierać tylko cyfry.";
    }
    if (!in_array($payment, ['karta', 'przelew', 'gotowka'])) {
        $errors[] = "Nieprawidłowa opcja płatności.";
    }

    // Sprawdzenie, czy pojazdy istnieją w bazie danych
    $placeholders = implode(',', array_fill(0, count($vehicle_ids), '?'));
    $sql_vehicle = "SELECT id, brand, model, price FROM vehicles WHERE id IN ($placeholders)";
    $stmt_vehicle = $conn->prepare($sql_vehicle);
    $stmt_vehicle->bind_param(str_repeat('i', count($vehicle_ids)), ...$vehicle_ids);
    $stmt_vehicle->execute();
    $result_vehicle = $stmt_vehicle->get_result();

    $existing_vehicle_ids = [];
    $total_price = 0;
    while ($row = $result_vehicle->fetch_assoc()) {
        $existing_vehicle_ids[] = $row['id'];
        $total_price += $row['price'];
    }

    if (count($existing_vehicle_ids) !== count($vehicle_ids)) {
        $errors[] = "Niektóre pojazdy nie istnieją.";
    }

    if (empty($errors)) {
        $conn->begin_transaction();

        try {
            foreach ($existing_vehicle_ids as $vehicle_id) {
                // Zapisanie zamówienie w bazie danych
                $sql = "INSERT INTO orders (user_id, vehicle_id, name, surname, email, country, city, street, house_number, payment, purchase_date) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iissssssss", $user_id, $vehicle_id, $name, $surname, $email, $country, $city, $street, $house_number, $payment);
                $stmt->execute();
            }

            $conn->commit();

            // Usunięcie zamówionych przedmiotów z koszyka
            $_SESSION['cart'] = array_diff($_SESSION['cart'], $existing_vehicle_ids);

            // Wysyłanie emaila
            $to = $email;
            $subject = "Potwierdzenie zamówienia";
            $message = "
<html>
<head>
<title>Potwierdzenie zamówienia</title>
</head>
<body>
<h2>Dziękujemy za zakupy w naszym sklepie, $name!</h2>
<p>Twoje zamówienie zostało złożone pomyślnie. Szczegóły zamówienia:</p>
<ul>";

            foreach ($existing_vehicle_ids as $vehicle_id) {
                $sql_vehicle_details = "SELECT brand, model, price FROM vehicles WHERE id = ?";
                $stmt_vehicle_details = $conn->prepare($sql_vehicle_details);
                $stmt_vehicle_details->bind_param("i", $vehicle_id);
                $stmt_vehicle_details->execute();
                $result_vehicle_details = $stmt_vehicle_details->get_result();
                $vehicle = $result_vehicle_details->fetch_assoc();

                $message .= "<li>{$vehicle['brand']} {$vehicle['model']} - {$vehicle['price']}zł</li>";
            }

            $message .= "
</ul>
<p>Łączna kwota: {$total_price} zł</p>
<p>Adres dostawy:</p>
<ul>
<li>Kraj: {$country}</li>
<li>Miasto: {$city}</li>
<li>Ulica: {$street}</li>
<li>Numer domu: {$house_number}</li>
</ul>
</body>
</html>
";

            // Nagłówki emaila
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= 'From: <no-reply@twojsklep.pl>' . "\r\n";

            mail($to, $subject, $message, $headers);

            echo "<script>alert('Zakup został pomyślnie zrealizowany!');</script>";
            echo "<script>
                    setTimeout(function(){
                        window.location.href = 'order_history.php';
                    }, 2500);
                  </script>";
        } catch (Exception $e) {
            $conn->rollback();
            echo "Błąd przy składaniu zamówienia: " . $e->getMessage();
        }
    } else {
        // Wyświetlanie błędów
        echo '<div class="errors">';
        foreach ($errors as $error) {
            echo '<p>' . htmlspecialchars($error) . '</p>';
        }
        echo '</div>';
    }

    $stmt_vehicle->close();
    $conn->close();
} else {
    header("Location: shop.php");
    exit();
}
