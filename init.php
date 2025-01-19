<?php
if (!file_exists('merged.json')) {
    die("Файл merged.json не найден.");
}

$data = json_decode(file_get_contents('merged.json'), true);
$matches = $data['matches'];

$teams = [];
foreach ($matches as $match) {
    $teams[] = $match['team1'];
    $teams[] = $match['team2'];
}

$teams = array_unique($teams);
file_put_contents('teams.txt', implode("\n", $teams));

header("Location: index.php");
exit;