<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Прогноз матча</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Прогноз матча</h1>

        <!-- Кнопка для первого запуска -->
        <?php if (!file_exists('teams.txt')): ?>
            <form id="init-form" method="POST" action="init.php">
                <button type="submit" id="init-button">Инициализировать список команд</button>
            </form>
        <?php endif; ?>

        <!-- Основная форма для прогноза -->
        <form id="prediction-form" method="POST" action="predict.php">
            <input type="text" id="team1" name="team1" placeholder="Команда 1" required>
            <input type="text" id="team2" name="team2" placeholder="Команда 2" required>
            <p class="hint">⚠️ Вводите названия команд на английском языке.</p>
            <button type="submit">Прогноз</button>
        </form>

        <!-- Тестовый анализ матча -->
        <div id="test-analysis">
            <h2>Тестовый анализ матча (TeamA vs TeamB)</h2>
            <?php
            include 'predict.php';
            $testPrediction = predictMatch('TeamA', 'TeamB', $matches);

            if ($testPrediction) {
                echo "
                    <div class='prediction-result'>
                        <p class='outcome'>{$testPrediction['outcome']}</p>
                        <p class='score'>Точный счет: <span class='score-value'>{$testPrediction['predictedScore'][0]} - {$testPrediction['predictedScore'][1]}</span></p>
                        <p class='total'>Общий тотал 1.5: <span class='total-value {$testPrediction['total1_5_class']}'>{$testPrediction['total1_5']}</span></p>
                        <p class='total'>Общий тотал 2.5: <span class='total-value {$testPrediction['total2_5_class']}'>{$testPrediction['total2_5']}</span></p>
                        <p class='total'>Индивидуальный тотал TeamA 0.5: <span class='total-value {$testPrediction['team1_total_0_5_class']}'>{$testPrediction['team1_total_0_5']}</span></p>
                        <p class='total'>Индивидуальный тотал TeamB 0.5: <span class='total-value {$testPrediction['team2_total_0_5_class']}'>{$testPrediction['team2_total_0_5']}</span></p>
                        <p class='commentator-phrase'>💬 {$testPrediction['commentatorPhrase']}</p>
                    </div>
                ";
            } else {
                echo "<p class='error'>Тестовый анализ не удался. Проверьте данные.</p>";
            }
            ?>
        </div>

        <!-- Блок для вывода результата -->
        <div id="result">
            <?php
            if (isset($prediction)) {
                echo "
                    <div class='prediction-result'>
                        <p class='outcome'>{$prediction['outcome']}</p>
                        <p class='score'>Точный счет: <span class='score-value'>{$prediction['predictedScore'][0]} - {$prediction['predictedScore'][1]}</span></p>
                        <p class='total'>Общий тотал 1.5: <span class='total-value {$prediction['total1_5_class']}'>{$prediction['total1_5']}</span></p>
                        <p class='total'>Общий тотал 2.5: <span class='total-value {$prediction['total2_5_class']}'>{$prediction['total2_5']}</span></p>
                        <p class='total'>Индивидуальный тотал {$_POST['team1']} 0.5: <span class='total-value {$prediction['team1_total_0_5_class']}'>{$prediction['team1_total_0_5']}</span></p>
                        <p class='total'>Индивидуальный тотал {$_POST['team2']} 0.5: <span class='total-value {$prediction['team2_total_0_5_class']}'>{$prediction['team2_total_0_5']}</span></p>
                        <p class='commentator-phrase'>💬 {$prediction['commentatorPhrase']}</p>
                    </div>
                ";
            } elseif (isset($error)) {
                echo "<p class='error'>{$error}</p>";
            }
            ?>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>