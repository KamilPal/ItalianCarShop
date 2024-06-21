<?php
session_start();

if (!isset($_SESSION['user_id']) || !$_SESSION['admin']) {
    header("Location: login.php");
    exit();
}

require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vehicle_id = $_POST['vehicle_id'];

    $stmt = $conn->prepare("DELETE FROM vehicles WHERE id = ?");
    $stmt->bind_param("i", $vehicle_id);

    if ($stmt->execute()) {
        header("Location: vehicles.php");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();

