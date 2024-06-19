<?php
session_start();

if (!isset($_SESSION['user_id']) || !$_SESSION['admin']) {
    header("Location: login.php");
    exit();
}

require 'config.php';

// Filtrowanie
$filter_name = isset($_GET['filter_name']) ? $_GET['filter_name'] : '';
$filter_surname = isset($_GET['filter_surname']) ? $_GET['filter_surname'] : '';
$filter_email = isset($_GET['filter_email']) ? $_GET['filter_email'] : '';

$sql = "SELECT * FROM users WHERE 1=1";

if ($filter_name !== '') {
    $sql .= " AND name LIKE '%" . $conn->real_escape_string($filter_name) . "%'";
}
if ($filter_surname !== '') {
    $sql .= " AND surname LIKE '%" . $conn->real_escape_string($filter_surname) . "%'";
}
if ($filter_email !== '') {
    $sql .= " AND email LIKE '%" . $conn->real_escape_string($filter_email) . "%'";
}

// Sortowanie
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'id';
$sort_order = isset($_GET['sort_order']) && $_GET['sort_order'] === 'desc' ? 'DESC' : 'ASC';

$valid_sort_columns = ['id', 'name', 'surname', 'email', 'admin', 'is_active'];
if (!in_array($sort_by, $valid_sort_columns)) {
    $sort_by = 'id';
}

$sql .= " ORDER BY $sort_by $sort_order";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Zarządzanie użytkownikami</title>
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
<h2>Zarządzasz użytkownikami</h2>
<div class="center-link">
    <div class="button-group">
        <a href="create_user.php" class="button">Dodaj nowego użytkownika</a>
    </div>
</div>
<div class="container">
    <div class="button-group">
        <form method="get" action="" class="filter-form">
            <div class="form-group">
                <label for="filter_name">Imię:</label>
                <input type="text" id="filter_name" name="filter_name" value="<?php echo htmlspecialchars($filter_name); ?>">
                
                <label for="filter_surname">Nazwisko:</label>
                <input type="text" id="filter_surname" name="filter_surname" value="<?php echo htmlspecialchars($filter_surname); ?>">
                
                <label for="filter_email">E-mail:</label>
                <input type="text" id="filter_email" name="filter_email" value="<?php echo htmlspecialchars($filter_email); ?>">
                
                <button type="submit">Filtruj</button>
            </div>
        </form>
    </div>
    <table>
        <tr>
            <th>ID</th>
            <th>Imię</th>
            <th>Nazwisko</th>
            <th>E-mail</th>
            <th>Admin</th>
            <th>Czy aktywny</th>
            <th>Akcje</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['surname']; ?></td>
            <td><?php echo $row['email']; ?></td>
            <td><?php echo $row['admin'] ? 'Tak' : 'Nie'; ?></td>
            <td><?php echo $row['is_active'] ? 'Tak' : 'Nie'; ?></td>
            <td>
            <div class="button-group">
            <button><a href="edit_user.php?id=<?php echo $row['id']; ?>">Edytuj</a></button>
            </div>
            </td>
        </tr>
        <?php endwhile; ?>
    </table><br>
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
</body>
</html>

<?php
$conn->close();
?>
