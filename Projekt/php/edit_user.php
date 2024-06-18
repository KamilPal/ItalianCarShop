<?php
session_start();

require 'config.php';

$errors = [];
$user = ['id' => '', 'name' => '', 'surname' => '', 'email' => '', 'admin' => 0];

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if the user is admin or editing their own profile
$isAdmin = $_SESSION['admin'];
$currentUserId = $_SESSION['user_id'];
$editingUserId = isset($_GET['id']) ? $_GET['id'] : $currentUserId;

if (!$isAdmin && $editingUserId != $currentUserId) {
    header("Location: home.php");
    exit();
}

// Fetch user data for editing
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($editingUserId)) {
    $stmt = $conn->prepare("SELECT id, name, surname, email, admin FROM users WHERE id = ?");
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

// Handle form submission for updating user data
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $admin = isset($_POST['admin']) ? 1 : 0;
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Server-side validation
    if (!preg_match("/^[a-zA-Z]{3,}$/", $name)) {
        $errors[] = "Imię powinno zawierać tylko litery i mieć co najmniej 3 znaki.";
    }

    if (!preg_match("/^[a-zA-Z]{3,}$/", $surname)) {
        $errors[] = "Nazwisko powinno zawierać tylko litery i mieć co najmniej 3 znaki.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email powinien być w prawidłowym formacie.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Hasła nie są zgodne.";
    }

    if (!empty($password) && strlen($password) < 6) {
        $errors[] = "Hasło powinno mieć co najmniej 6 znaków.";
    }

    if (empty($errors)) {
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET name = ?, surname = ?, email = ?, password = ?, admin = ? WHERE id = ?");
            $stmt->bind_param("ssssii", $name, $surname, $email, $hashed_password, $admin, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET name = ?, surname = ?, email = ?, admin = ? WHERE id = ?");
            $stmt->bind_param("sssii", $name, $surname, $email, $admin, $user_id);
        }

        if ($stmt->execute()) {
            header("Location: manage_users.php");
            exit();
        } else {
            $errors[] = "Błąd: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $user = ['id' => $user_id, 'name' => $name, 'surname' => $surname, 'email' => $email, 'admin' => $admin];
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edytuj użytkownika</title>
    <link rel="stylesheet" href="../css/general.css">
    <link rel="stylesheet" href="../css/miscellaneous.css">
</head>
<body>
    <h2>Edytuj użytkownika</h2>
    <form action="edit_user.php" method="post" onsubmit="return validateForm()">
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

        <?php if ($isAdmin) : ?>
            <label for="admin">Admin:</label>
            <input type="checkbox" id="admin" name="admin" <?php echo $user['admin'] ? 'checked' : ''; ?>><br>
        <?php endif; ?>

        <button type="submit">Zaktualizuj użytkownika</button>
    </form>
    <script src="../Js/walidacja.js"></script>
    <script>
        var errors = <?php echo json_encode($errors); ?>;
        if (errors.length > 0) {
            alert(errors.join("\n"));
        }
    </script>
</body>
</html>
