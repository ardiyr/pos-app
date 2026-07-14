<?php
try {
    $pdo = new PDO('pgsql:host=localhost;port=5432;dbname=pos_db', 'postgres', 'password');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = "kopi";
    $stmt = $pdo->prepare("SELECT * FROM products WHERE LOWER(name) LIKE :q OR LOWER(sku) LIKE :q");
    $stmt->execute(['q' => '%' . strtolower($query) . '%']);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    print_r($results);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

