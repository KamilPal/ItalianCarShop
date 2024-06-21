<?php
session_start();

if (!isset($_SESSION['user_id']) || !$_SESSION['admin']) {
    header("Location: login.php");
    exit();
}

require 'config.php';

$brand = isset($_GET['brand']) ? $_GET['brand'] : '';
$model = isset($_GET['model']) ? $_GET['model'] : '';
$min_price = isset($_GET['min_price']) ? $_GET['min_price'] : '';
$max_price = isset($_GET['max_price']) ? $_GET['max_price'] : '';

$sql = "SELECT * FROM vehicles WHERE 1=1";

if ($brand) {
    $sql .= " AND brand LIKE '%" . $conn->real_escape_string($brand) . "%'";
}
if ($model) {
    $sql .= " AND model LIKE '%" . $conn->real_escape_string($model) . "%'";
}
if ($min_price) {
    $sql .= " AND price >= " . $conn->real_escape_string($min_price);
}
if ($max_price) {
    $sql .= " AND price <= " . $conn->real_escape_string($max_price);
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Zarządzanie pojazdami</title>
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
    <h2>Zarządzasz pojazdami</h2>
    <div class="center-link">
    <div class="button-group">
        <a href="add_vehicle.php">Dodaj nowy pojazd</a>
    </div>
    </div>
    <div class="container">
        <div class="button-group">
            <form method="get" action="">
                <div class="form-group">
                    <label for="brand">Marka:</label>
                    <input type="text" id="brand" name="brand" value="<?php echo htmlspecialchars($brand); ?>">
                    
                    <label for="model">Model:</label>
                    <input type="text" id="model" name="model" value="<?php echo htmlspecialchars($model); ?>">
                    
                    <label for="min_price">Cena od:</label>
                    <input type="number" id="min_price" name="min_price" value="<?php echo htmlspecialchars($min_price); ?>">
                    
                    <label for="max_price">Cena do:</label>
                    <input type="number" id="max_price" name="max_price" value="<?php echo htmlspecialchars($max_price); ?>">
                    
                    <button type="submit">Filtruj</button>
                </div>
            </form>
        </div>
        <div class="table-container">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Marka</th>
                    <th>Model</th>
                    <th>Rok</th>
                    <th>Cena</th>
                    <th>Obraz</th>
                    <th>Opis</th>
                    <th>Akcje</th>
                </tr>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['brand']; ?></td>
                    <td><?php echo $row['model']; ?></td>
                    <td><?php echo $row['year']; ?></td>
                    <td><?php echo $row['price']; ?></td>
                    <td><img src="../images/<?php echo $row['image']; ?>" alt="<?php echo $row['brand'] . ' ' . $row['model']; ?>" width="100"></td>
                    <td><?php echo $row['description']; ?></td>
                    <td>
                    <div class="form-group">
                    <a href="edit_vehicle.php?id=<?php echo $row['id']; ?>"><button>Edycja</button></a>
                    </div>
                        <form action="delete_vehicle.php" method="post" style="display:inline;" onsubmit="return confirmDelete()">
                            <input type="hidden" name="vehicle_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="delete_vehicle">Usuń</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table><br>
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
    <script>
        function confirmDelete() {
            return confirm('Czy na pewno chcesz usunąć ten pojazd?');
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
