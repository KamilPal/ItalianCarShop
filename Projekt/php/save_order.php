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
    $address = trim($_POST['address']);
    $payment = $_POST['payment'];

    $errors = [];

    // Walidacja danych
    if (empty($name)) {
        $errors[] = "Imię jest wymagane.";
    }
    if (empty($surname)) {
        $errors[] = "Nazwisko jest wymagane.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Nieprawidłowy adres email.";
    }
    if (empty($country)) {
        $errors[] = "Kraj jest wymagany.";
    }
    if (empty($city)) {
        $errors[] = "Miasto jest wymagane.";
    }
    if (empty($address)) {
        $errors[] = "Adres dostawy jest wymagany.";
    }
    if (!in_array($payment, ['karta', 'przelew', 'gotowka'])) {
        $errors[] = "Nieprawidłowa opcja płatności.";
    }

    // Sprawdzenie, czy pojazdy istnieją w bazie danych
    $placeholders = implode(',', array_fill(0, count($vehicle_ids), '?'));
    $sql_vehicle = "SELECT id FROM vehicles WHERE id IN ($placeholders)";
    $stmt_vehicle = $conn->prepare($sql_vehicle);
    $stmt_vehicle->bind_param(str_repeat('i', count($vehicle_ids)), ...$vehicle_ids);
    $stmt_vehicle->execute();
    $result_vehicle = $stmt_vehicle->get_result();

    $existing_vehicle_ids = [];
    while ($row = $result_vehicle->fetch_assoc()) {
        $existing_vehicle_ids[] = $row['id'];
    }

    if (count($existing_vehicle_ids) !== count($vehicle_ids)) {
        $errors[] = "Niektóre pojazdy nie istnieją.";
    }

    if (empty($errors)) {
        $conn->begin_transaction();

        try {
            foreach ($existing_vehicle_ids as $vehicle_id) {
                // Zapisz zamówienie w bazie danych
                $sql = "INSERT INTO orders (user_id, vehicle_id, name, surname, email, country, city, address, payment, purchase_date) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iisssssss", $user_id, $vehicle_id, $name, $surname, $email, $country, $city, $address, $payment);
                $stmt->execute();
            }

            $conn->commit();

            // Usunięcie zamówionych przedmiotów z koszyka
            $_SESSION['cart'] = array_diff($_SESSION['cart'], $existing_vehicle_ids);

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
