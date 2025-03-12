<?php
include 'connection.php';

try {
    // новий об'єкт PDO для з'єднання з базою даних
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['author_id'])) {
        $author_id = (int) $_GET['author_id'];

        // sql запит для отримання імені автора
        $author_sql = "SELECT NAME FROM author WHERE Id = :author_id";
        $author_stmt = $pdo->prepare($author_sql);
        $author_stmt->bindParam(':author_id', $author_id, PDO::PARAM_INT);
        $author_stmt->execute();

        $author = $author_stmt->fetch(PDO::FETCH_ASSOC);
        if ($author) {
            $author_name = $author['NAME'];
        } else {
            echo "<p>Автор не знайдений.</p>";
            exit;
        }

        // sql запит для книг
        $sql = "SELECT l.NAME, l.YEAR, l.ISBN, l.QUANTITY, l.LITERATE
        FROM literature l
        LEFT JOIN book_authrs ba ON l.Id = ba.FID_BOOK
        LEFT JOIN author a ON ba.FID_AUTH = a.Id
        WHERE a.Id = :author_id
        GROUP BY l.Id
        ORDER BY l.NAME";

        $stmt = $pdo->prepare($sql);
        // прив'язка параметрів до запиту
        $stmt->bindParam(':author_id', $author_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($books) {
            echo "<h2>Книги автора: " . htmlspecialchars($author_name) . "</h2>";
            echo "<table border='1'>";
            echo "<tr style='background-color: Blue; color: white;'><th>Назва</th><th>Рік</th><th>ISBN</th><th>Кількість</th><th>Жанр</th></tr>";
            foreach ($books as $book) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($book['NAME']) . "</td>";
                echo "<td>" . htmlspecialchars($book['YEAR']) . "</td>";
                echo "<td>" . htmlspecialchars($book['ISBN']) . "</td>";
                echo "<td>" . htmlspecialchars($book['QUANTITY']) . "</td>";
                echo "<td>" . htmlspecialchars($book['LITERATE']) . "</td>";                
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>Книг цього автора не знайдено.</p>";
        }
    } else {
        echo "<p>Будь ласка, виберіть автора.</p>";
    }
} 
catch (PDOException $e) {
    echo "Помилка запиту: " . $e->getMessage();
}
?>