<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recepty</title>
    <style>
        /* Základní CSS pro lepší vizuální vzhled */
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
    </style>
</head>
<body>
<?php
require_once("SmartCookClient.php");

$request_data = [
    "attributes" => ["id", "name", "author"],
    "filter" => [
        "author" => ["Král Pavel"],
        "dish_category" => [4, 5],
    ]
];

try {
    // Vytvoření instance SmartCookClient
    $client = new SmartCookClient;
    $client->setRequestData($request_data);
    $response = $client->sendRequest("recipes")->getResponseData();

    // Zpracování a zobrazení receptů v tabulce
    if (!empty($response['data'])) {
        echo "<table>";
        echo "<tr><th>Název</th><th>Autor</th></tr>";
        foreach ($response['data'] as $recipe) {
            echo "<tr>";
            echo "<td>" . $recipe['name'] . "</td>";
            echo "<td>" . $recipe['author'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "Žádné recepty nebyly nalezeny.";
    }
} catch (Exception $e) {
    echo "Chyba při získávání dat: " . $e->getMessage();
}
?>
</body>
</html>
