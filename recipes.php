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
        .recipe-list {
            list-style-type: none;
            padding: 0;
        }
        .recipe-item {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ccc;
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
        "dish_category" => [],
    ]
];

try {
    
    $client = new SmartCookClient;
    $client->setRequestData($request_data);
    $response = $client->sendRequest("recipes")->getResponseData();

    
    if (!empty($response['data'])) {
        echo "<ul class='recipe-list'>";
        foreach ($response['data'] as $recipe) {
            echo "<li class='recipe-item'>";
            echo "<strong>Název:</strong> " . $recipe['name'] . "<br>";
            echo "<strong>Autor:</strong> " . $recipe['author'];
            echo "</li>";
        }
        echo "</ul>";
    } else {
        echo "Žádné recepty nebyly nalezeny.";
    }
} catch (Exception $e) {
    echo "Chyba při získávání dat: " . $e->getMessage();
}
?>
</body>
</html>
