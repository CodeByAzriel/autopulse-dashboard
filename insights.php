<?php
include 'db_connect.php';

// === Chart 1: Most Considered Cars ===
$consideredCars = $conn->query("
    SELECT c.brand, c.model, COUNT(s.selection_id) AS times_considered
    FROM cars c
    JOIN selections s ON c.car_id = s.car_id
    WHERE s.status = 'considered'
    GROUP BY c.car_id
    ORDER BY times_considered DESC
");

$carLabels = [];
$carData = [];
while ($row = $consideredCars->fetch_assoc()) {
    $carLabels[] = $row['brand'].' '.$row['model'];
    $carData[] = $row['times_considered'];
}

// === Chart 2: Most Requested Features ===
$featuresResult = $conn->query("
    SELECT selected_features FROM selections WHERE selected_features IS NOT NULL
");

$featureCounts = [];
while ($row = $featuresResult->fetch_assoc()) {
    $features = explode(',', $row['selected_features']);
    foreach ($features as $feature) {
        $f = trim($feature);
        if (!empty($f)) {
            if (isset($featureCounts[$f])) {
                $featureCounts[$f]++;
            } else {
                $featureCounts[$f] = 1;
            }
        }
    }
}

$featureLabels = array_keys($featureCounts);
$featureData = array_values($featureCounts);

// === Chart 3: Abandoned Selections ===
$abandonedCars = $conn->query("
    SELECT c.brand, c.model, COUNT(*) AS abandoned_count
    FROM cars c
    JOIN selections s ON c.car_id = s.car_id
    WHERE s.status = 'abandoned'
    GROUP BY c.car_id
    ORDER BY abandoned_count DESC
");

$abandonedLabels = [];
$abandonedData = [];
while ($row = $abandonedCars->fetch_assoc()) {
    $abandonedLabels[] = $row['brand'].' '.$row['model'];
    $abandonedData[] = $row['abandoned_count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Insights - AutoPulse</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <header class="header">
        <div class="logo">AutoPulse</div>
        <nav class="nav">
            <a href="index.php">Home</a>
            <a href="insights.php">Insights</a>
            <a href="features.php">Features</a>
            <a href="contact.php">Contact</a>
        </nav>
    </header>

    <section class="dashboard">
        <h2>Most Considered Cars</h2>
        <canvas id="consideredChart"></canvas>

        <h2>Most Requested Features</h2>
        <canvas id="featuresChart"></canvas>

        <h2>Abandoned Selections</h2>
        <canvas id="abandonedChart"></canvas>
    </section>

    <footer class="footer">
        &copy; 2026 AutoPulse. All Rights Reserved.
    </footer>

    <script>
        // === Chart 1: Most Considered Cars ===
        new Chart(document.getElementById('consideredChart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($carLabels); ?>,
                datasets: [{
                    label: 'Times Considered',
                    data: <?php echo json_encode($carData); ?>,
                    backgroundColor: 'rgba(0, 123, 255, 0.7)',
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true, precision:0 } } }
        });

        // === Chart 2: Most Requested Features ===
        new Chart(document.getElementById('featuresChart'), {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($featureLabels); ?>,
                datasets: [{
                    label: 'Feature Count',
                    data: <?php echo json_encode($featureData); ?>,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
                        'rgba(255, 159, 64, 0.7)'
                    ]
                }]
            },
            options: { responsive: true }
        });

        // === Chart 3: Abandoned Selections ===
        new Chart(document.getElementById('abandonedChart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($abandonedLabels); ?>,
                datasets: [{
                    label: 'Abandoned Count',
                    data: <?php echo json_encode($abandonedData); ?>,
                    backgroundColor: 'rgba(255, 0, 0, 0.7)',
                    borderColor: 'rgba(255, 0, 0, 1)',
                    borderWidth: 1
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true, precision:0 } } }
        });
    </script>
</body>
</html>
