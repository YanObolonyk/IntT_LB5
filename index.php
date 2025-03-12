<?php
include 'connection.php';

try {
    $pdo = new PDO($dsn, $user, $pass); // php data object для підключення до бд
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // режим обробки помилок
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="ua">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LB5</title>
    <style>
    select {
        background-color: lightgray;
        border-radius: 5px;
    }
    </style>

</head>
<body>
    <form action="get1.php" method="GET">
        <label for="publisher">Оберіть назву видавництва:</label>
        <select name="publisher" id="publisher">
            <?php 
            // унікальні назви видавництв
            $query = "SELECT DISTINCT PUBLISHER FROM literature ORDER BY PUBLISHER";
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            $publishers = $stmt->fetchAll(PDO::FETCH_COLUMN); // масив зі значеннями

            
            foreach ($publishers as $publisher): ?>
                <option value="<?= htmlspecialchars($publisher) ?>"> <?= htmlspecialchars($publisher) ?> </option> 
            <?php endforeach; ?>
        </select>
        <br><br>
        <button type="submit">Результати пошуку</button>
    </form>

    <form action="get2.php" method="GET">
    <br><br>    

    <label for="start_year">Початковий рік:</label>
    <input type="number" name="start_year" id="start_year" min="1900" max="2100">
    <br>

    <label for="end_year">Кінцевий рік:</label>
    <input type="number" name="end_year" id="end_year" min="1900" max="2100">
    <br><br>

    <button type="submit">Результати пошуку</button>
    <br><br><br>
    </form>

    <form action="get3.php" method="GET">
    <label for="author">Оберіть автора:</label>
    <select name="author_id" id="author">
        <?php
        $stmt = $pdo->query("SELECT Id, NAME FROM author ORDER BY NAME ASC");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { // наступний рядок результату у вигляді асоціативного масиву ключ-значення
            echo '<option value="' . htmlspecialchars($row['Id']) . '">' . htmlspecialchars($row['NAME']) . '</option>'; // запобігання XSS
        }
        ?>
    </select>
    <br><br>
    <button type="submit">Результати пошуку</button>
    </form>
</body>
</html>