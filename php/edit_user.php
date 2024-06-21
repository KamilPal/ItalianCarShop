<?php
session_start();

require 'config.php';

$errors = [];
$user = ['id' => '', 'name' => '', 'surname' => '', 'email' => '', 'admin' => 0, 'is_active' => 1];

// Sprawdzanie czy user jest zalogowany
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Sprawdzenie kto zmienia swoje konto
$isAdmin = $_SESSION['admin'];
$currentUserId = $_SESSION['user_id'];
$editingUserId = isset($_GET['id']) ? $_GET['id'] : $currentUserId;

if (!$isAdmin && $editingUserId != $currentUserId) {
    header("Location: home.php");
    exit();
}

// Pobranie danych do edycji
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($editingUserId)) {
    $stmt = $conn->prepare("SELECT id, name, surname, email, admin, is_active FROM users WHERE id = ?");
    $stmt->bind_param("i", $editingUserId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        header("Location: manage_users.php");
        exit();
    }
    $stmt->close();
}

// Obsługa przesyłania formularzy w celu aktualizacji danych użytkownika
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    // Admin może zmienić status admina tylko innym
    if ($isAdmin && $currentUserId != $user_id) {
        $admin = isset($_POST['admin']) ? 1 : 0;
    } else {
        $admin = $user['admin'];
    }

    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Walidacja po stronie serwera
    if (!preg_match("/^[a-zA-ZąęćńłóśżźĄĘĆŃŁÓŚŻŹ]{3,}$/u", $name)) {
        $errors[] = "Imię powinno zawierać tylko litery i mieć co najmniej 3 znaki.";
    }

    if (!preg_match("/^[a-zA-ZąęćńłóśżźĄĘĆŃŁÓŚŻŹ]{3,}$/u", $surname)) {
        $errors[] = "Nazwisko powinno zawierać tylko litery i mieć co najmniej 3 znaki.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email powinien być w prawidłowym formacie.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Hasła nie są zgodne.";
    }

    if (!empty($password) && strlen($password) < 4) {
        $errors[] = "Hasło powinno mieć co najmniej 4 znaki.";
    }

    if (empty($errors)) {
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET name = ?, surname = ?, email = ?, password = ?, admin = ?, is_active = ? WHERE id = ?");
            $stmt->bind_param("ssssiii", $name, $surname, $email, $hashed_password, $admin, $is_active, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET name = ?, surname = ?, email = ?, admin = ?, is_active = ? WHERE id = ?");
            $stmt->bind_param("sssiii", $name, $surname, $email, $admin, $is_active, $user_id);
        }

        if ($stmt->execute()) {
            header("Location: manage_users.php");
            exit();
        } else {
            $errors[] = "Błąd: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $user = ['id' => $user_id, 'name' => $name, 'surname' => $surname, 'email' => $email, 'admin' => $admin, 'is_active' => $is_active];
    }
}

// Obsługa przesyłania formularzy w celu usunięcia użytkowników
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_user_id'])) {
    $delete_user_id = $_POST['delete_user_id'];

    if ($isAdmin && $delete_user_id != $currentUserId) {
        // Start transakcji
        $conn->begin_transaction();

        try {
            // Usuwanie połaczonych zamówień
            $stmt = $conn->prepare("DELETE FROM orders WHERE user_id = ?");
            $stmt->bind_param("i", $delete_user_id);
            $stmt->execute();
            $stmt->close();

            // Usunięcie użykownika
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("i", $delete_user_id);
            $stmt->execute();
            $stmt->close();

            $conn->commit();

            header("Location: manage_users.php");
            exit();
        } catch (Exception $e) {
            // Wycofywanie transakcji
            $conn->rollback();
            $errors[] = "Błąd: " . $e->getMessage();
        }
    } else {
        $errors[] = "Nie można usunąć własnego konta.";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edytuj użytkownika</title>
    <link rel="stylesheet" href="../css/uh.css">
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
                        <?php if ($_SESSION['admin']): ?>
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
            <a href="admin.php">Powrót do Panelu Admina</a>
        </div>
    </nav>
    <div class="italian-flag"></div>
    <h2>Edytuj użytkownika</h2>
    <div class="container">
    <div class="button-group">
    <form action="edit_user.php" method="post" onsubmit="return validateForm()">
    <div class="form-group">
        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
        <label for="name">Imię:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required><br>
        <label for="surname">Nazwisko:</label>
        <input type="text" id="surname" name="surname" value="<?php echo htmlspecialchars($user['surname']); ?>" required><br>
        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required><br>
        <?php if ($isAdmin || $currentUserId == $user['id']) : ?>
            <label for="password">Hasło:</label>
            <input type="password" id="password" name="password"><br>
            <label for="confirm_password">Potwierdź hasło:</label>
            <input type="password" id="confirm_password" name="confirm_password"><br>
        <?php endif; ?>

        <?php if ($isAdmin && $currentUserId != $user['id']) : ?>
            <label for="admin">Admin:</label>
            <input type="checkbox" id="admin" name="admin" <?php echo $user['admin'] ? 'checked' : ''; ?>><br>
        <?php endif; ?>

        <?php if ($isAdmin && $currentUserId != $user['id']) : ?>
            <label for="is_active">Aktywne:</label>
            <input type="checkbox" id="is_active" name="is_active" <?php echo $user['is_active'] ? 'checked' : ''; ?>><br>
        <?php endif; ?>
        <button type="submit">Zaktualizuj użytkownika</button>
    </form>

    <?php if ($isAdmin && $currentUserId != $user['id']) : ?>
        <form action="edit_user.php" method="post">
            <input type="hidden" name="delete_user_id" value="<?php echo $user['id']; ?>">
            <button type="submit" onclick="return confirm('Czy na pewno chcesz usunąć tego użytkownika?');">Usuń użytkownika</button>
        </form>
    <?php endif; ?>
    </div>
    </div>
    <div class="center-link">
        <p><a href="manage_users.php">Powrót do zarządzania użytkownikami</a></p>
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
<script src="../Js/menu.js"></script>
<script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@latest/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@latest/dist/ionicons/ionicons.js"></script>
<script src="../Js/walidacja.js"></script>
<script>
    var errors = <?php echo json_encode($errors); ?>;
    if (errors.length > 0) {
        alert(errors.join("\n"));
    }
</script>
</body>
</html>

