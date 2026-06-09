<?php
// Konfiguracja połączenia z Twoją bazą danych OpenStack
$servername = "10.0.0.28";  // IP Twojego itoma-db-server
$username = "serverwatch";  // Użytkownik bazy danych
$password = "ala";          // Hasło z walidacji MySQL
$dbname = "serverwatch_db"; // Nazwa bazy danych

// Flagi statusu
$db_status_msg = "POŁĄCZENIE Z BAZĄ: OK";
$db_error = false;

// Próba połączenia
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    $db_status_msg = "BŁĄD BAZY: " . $conn->connect_error;
    $db_error = true;
} else {
    // Połączono pomyślnie! Tworzymy prostą tabelę na logi, jeśli nie istnieje
    $conn->query("CREATE TABLE IF NOT EXISTS system_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        log_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        message VARCHAR(255)
    )");

    // Zapisujemy fakt, że użytkownik wszedł na stronę statusu
    $conn->query("INSERT INTO system_logs (message) VALUES ('Odwiedziny podstrony Status przez serwer WWW')");
    
    // Pobieramy liczbę wszystkich zapisanych logów z bazy danych
    $result = $conn->query("SELECT COUNT(*) as total FROM system_logs");
    $row = $result->fetch_assoc();
    $total_logs = $row['total'];
    
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CyberNet - Status</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <nav>
        <a href="index.php">💻 Główna</a>
        <a href="services.php">🛠️ Usługi</a>
        <a href="status.php" class="active">📡 Status</a>
    </nav>

    <div class="terminal">
        <h2>[ MONITOR SYGNAŁU BAZY DANYCH ]</h2>
        <p>Bieżący stan integracji chmurowej (WWW <-> DB):</p>
        
        <div class="status-box" style="color: <?php echo $db_error ? '#ff0055' : '#55ff55'; ?>; border-color: <?php echo $db_error ? '#ff0055' : '#55ff55'; ?>; background: <?php echo $db_error ? '#221122' : '#112211'; ?>;">
            <?php echo $db_status_msg; ?>
        </div>

        <?php if (!$db_error): ?>
            <p style="color: #00ffcc;">📊 Komunikacja aktywna! W bazie danych znajduje się już <strong><?php echo $total_logs; ?></strong> wpisów monitoringu.</p>
        <?php endif; ?>

        <p class="timestamp">Ostatnia synchronizacja węzłów: <?php echo date('Y-m-d H:i:s'); ?></p>
    </div>

</body>
</html>