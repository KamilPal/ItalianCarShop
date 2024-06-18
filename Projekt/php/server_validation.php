<?php
require 'config.php';

$errors = [];
$name = '';
$surname = '';
$email = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Walidacja po stronie serwera
    if (!preg_match("/^[a-zA-Z]{3,}$/", $name)) {
        $errors[] = "Imię powinno zawierać tylko litery i mieć co najmniej 3 znaki.";
    }

    if (!preg_match("/^[a-zA-Z]{3,}$/", $surname)) {
        $errors[] = "Nazwisko powinno zawierać tylko litery i mieć co najmniej 3 znaki.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email powinien być w prawidłowym formacie.";
    }

    if ($password != $confirm_password) {
        $errors[] = "Hasła nie są zgodne.";
    }

    if (empty($errors)) {
        // Hashowanie hasła
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Przygotowanie i wykonanie zapytania SQL
        $stmt = $conn->prepare("INSERT INTO users (name, surname, email, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $surname, $email, $hashed_password);

        if ($stmt->execute()) {
            header("Location: login.php");
            exit();
        } else {
            $errors[] = "Błąd podczas rejestracji: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>
<script>
    var errors = <?php echo json_encode($errors); ?>;
</script>
