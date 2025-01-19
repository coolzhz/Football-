<?php
$matches = json_decode(file_get_contents('merged.json'), true)['matches'];

$team1 = $_POST['team1'];
$team2 = $_POST['team2'];

if (empty($team1) || empty($team2)) {
    $error = "Пожалуйста, введите названия обеих команд.";
    include 'index.php';
    exit;
}

$teams = file('teams.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

if (!in_array($team1, $teams) || !in_array($team2, $teams)) {
    $error = "Одна или обе команды не найдены в списке.";
    include 'index.php';
    exit;
}

$commentatorPhrases = [
    "Это будет захватывающий матч! Обе команды показывают отличную форму.",
    "Судя по статистике, нас ждет напряженная игра.",
    "Ожидаем много голов и ярких моментов!",
    "Команды подходят к матчу с разными тактиками, что делает его еще интереснее.",
    "Этот матч может стать решающим для обеих команд.",
    "Зрителей ждет настоящая футбольная битва!",
    "Обе команды готовы показать все, на что способны.",
    "Матч обещает быть зрелищным и непредсказуемым.",
    "Судя по последним играм, нас ждет много сюрпризов.",
    "Команды настроены только на победу!"
];

function getRandomCommentatorPhrase($phrases) {
    return $phrases[array_rand($phrases)];
}

function predictMatch($team1, $team2, $matches) {
    $matchResults = analyzeMatchResultsByHalf($matches);
    $teamStats = calculateTeamStatsByHalf($matchResults);

    if (isset($teamStats[$team1]) && isset($teamStats[$team2])) {
        $stats1 = $teamStats[$team1];
        $stats2 = $teamStats[$team2];

        $team1AvgGoals = $stats1['avgTh'] + $stats1['avgTf'];
        $team2AvgGoals = $stats2['avgTh'] + $stats2['avgTf'];

        $predictedScore = [
            round($team1AvgGoals),
            round($team2AvgGoals)
        ];

        $outcome = ($predictedScore[0] > $predictedScore[1]) ? "Победа {$team1} 🏆" :
                   (($predictedScore[0] < $predictedScore[1]) ? "Победа {$team2} 🏆" : "Ничья 🤝");

        $totalGoals = $predictedScore[0] + $predictedScore[1];

        $total1_5 = ($totalGoals > 1.5) ? "Больше 1.5 ✅" : "Меньше 1.5 ❌";
        $total1_5_class = ($totalGoals > 1.5) ? "success" : "danger";

        $total2_5 = ($totalGoals > 2.5) ? "Больше 2.5 ✅" : "Меньше 2.5 ❌";
        $total2_5_class = ($totalGoals > 2.5) ? "success" : "danger";

        $team1_total_0_5 = ($predictedScore[0] > 0.5) ? "Больше 0.5 ✅" : "Меньше 0.5 ❌";
        $team1_total_0_5_class = ($predictedScore[0] > 0.5) ? "success" : "danger";

        $team2_total_0_5 = ($predictedScore[1] > 0.5) ? "Больше 0.5 ✅" : "Меньше 0.5 ❌";
        $team2_total_0_5_class = ($predictedScore[1] > 0.5) ? "success" : "danger";

        return [
            'predictedScore' => $predictedScore,
            'outcome' => $outcome,
            'total1_5' => $total1_5,
            'total2_5' => $total2_5,
            'team1_total_0_5' => $team1_total_0_5,
            'team2_total_0_5' => $team2_total_0_5,
            'total1_5_class' => $total1_5_class,
            'total2_5_class' => $total2_5_class,
            'team1_total_0_5_class' => $team1_total_0_5_class,
            'team2_total_0_5_class' => $team2_total_0_5_class,
            'commentatorPhrase' => getRandomCommentatorPhrase($commentatorPhrases)
        ];
    } else {
        return null;
    }
}

function analyzeMatchResultsByHalf($matches) {
    return array_map(function($match) {
        $scoreHt = $match['score']['ht'] ?? [0, 0];
        $scoreFt = $match['score']['ft'] ?? [0, 0];

        return [
            'team1' => $match['team1'],
            'team2' => $match['team2'],
            'th' => $scoreHt[0],
            'tf' => $scoreFt[0] - $scoreHt[0]
        ];
    }, $matches);
}

function calculateTeamStatsByHalf($matches) {
    $teamStats = [];

    foreach ($matches as $match) {
        $team1 = $match['team1'];
        $team2 = $match['team2'];
        $th = $match['th'];
        $tf = $match['tf'];

        if (!isset($teamStats[$team1])) {
            $teamStats[$team1] = ['totalTh' => 0, 'totalTf' => 0, 'matchesPlayed' => 0];
        }
        $teamStats[$team1]['totalTh'] += $th;
        $teamStats[$team1]['totalTf'] += $tf;
        $teamStats[$team1]['matchesPlayed'] += 1;

        if (!isset($teamStats[$team2])) {
            $teamStats[$team2] = ['totalTh' => 0, 'totalTf' => 0, 'matchesPlayed' => 0];
        }
        $teamStats[$team2]['totalTh'] += $th;
        $teamStats[$team2]['totalTf'] += $tf;
        $teamStats[$team2]['matchesPlayed'] += 1;
    }

    foreach ($teamStats as &$stats) {
        $stats['avgTh'] = $stats['totalTh'] / $stats['matchesPlayed'];
        $stats['avgTf'] = $stats['totalTf'] / $stats['matchesPlayed'];
    }

    return $teamStats;
}

$prediction = predictMatch($team1, $team2, $matches);

if ($prediction) {
    include 'index.php';
} else {
    $error = "Одна или обе команды не найдены в данных.";
    include 'index.php';
}