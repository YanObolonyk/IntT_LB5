<?php
include 'connection.php';

// чи передані параметри через GET
if (isset($_GET['start_year']) && isset($_GET['end_year'])) {
    $start_year = (int) $_GET['start_year'];  
    $end_year = (int) $_GET['end_year'];      

    if ($start_year <= $end_year && $start_year >= 1900 && $end_year <= 2100) {

        try {
            // новий об'єкт PDO для з'єднання з базою даних
            $pdo = new PDO($dsn, $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // sql запит
            $sql = "SELECT l.YEAR, l.NAME, l.ISBN, l.QUANTITY, l.LITERATE, 
                         GROUP_CONCAT(a.NAME SEPARATOR ', ') AS AUTHORS
                  FROM literature l
                  LEFT JOIN book_authrs ba ON l.Id = ba.FID_BOOK
                  LEFT JOIN author a ON ba.FID_AUTH = a.Id
                  WHERE YEAR BETWEEN :start_year AND :end_year
                  GROUP BY l.Id
                  ORDER BY l.YEAR";

            
            $stmt = $pdo->prepare($sql);
            // прив'язка параметрів до запиту
            $stmt->bindParam(':start_year', $start_year, PDO::PARAM_INT);
            $stmt->bindParam(':end_year', $end_year, PDO::PARAM_INT);

            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                echo "<h3>Книжки, опубліковані з $start_year по $end_year:</h3>";
                echo "<table border='1'>
                <tr style='background-color: Blue; color: white;'><th>Рік</th><th>Назва</th><th>ISBN</th><th>Кількість</th><th>Жанр</th><th>Автор</th></tr>";

                // вивід
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['YEAR']) . "</td>";
                echo "<td>" . htmlspecialchars($row['NAME']) . "</td>";
                echo "<td>" . htmlspecialchars($row['ISBN']) . "</td>";
                echo "<td>" . htmlspecialchars($row['QUANTITY']) . "</td>";
                echo "<td>" . htmlspecialchars($row['LITERATE']) . "</td>";
                echo "<td>" . htmlspecialchars($row['AUTHORS']) . "</td>";
                echo "</tr>";
                }

                echo "</table>";
            } else {
                echo "Книжок, опублікованих у вказаному періоді, не знайдено.";
            }

        } catch (PDOException $e) {
            
            echo "Помилка запиту: " . $e->getMessage();
        }
    } else {
        echo "Невірний діапазон років. Перевірте введені дані.";
    }
} else {
    echo "Будь ласка, введіть початковий та кінцевий рік.";
}
?>
