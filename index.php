<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
// 1. Se l'utente apre la pagina in GET (senza inviare il modulo), rimandalo al form
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: form.html");
    exit;
}

// 2. Prendo i dati inviati dal form
$nome = trim($_POST['nome'] ?? '');
$peso = $_POST['peso'] ?? '';
$altezza = $_POST['altezza'] ?? '';
$ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
$data_ora = date("d/m/Y,H:i:s");

// 3. Valido i singoli dati
$log_nome = !empty($nome) ? htmlspecialchars($nome) : "NOT_VALID";
$log_peso = (is_numeric($peso) && $peso > 0) ? $peso : "NOT_VALID";
$log_altezza = (is_numeric($altezza) && $altezza > 0) ? $altezza : "NOT_VALID";

$log_bmi = "NOT_VALID";
$log_class = "NOT_VALID";

// 4. Se peso e altezza sono validi, calcolo il BMI
if ($log_peso != "NOT_VALID" && $log_altezza != "NOT_VALID") {
    $altezza_m = $altezza / 100; // converto cm in metri
    $bmi = $peso / ($altezza_m * $altezza_m);
    $log_bmi = number_format($bmi, 2, '.', '');

    // Decido la categoria
    if ($bmi < 18.5) {
        $fascia = "Sottopeso";
        $classe_card = "sottopeso";
    } elseif ($bmi <= 24.9) {
        $fascia = "Normopeso";
        $classe_card = "normopeso";
    } elseif ($bmi <= 29.9) {
        $fascia = "Sovrappeso";
        $classe_card = "sovrappeso";
    } else {
        $fascia = "Obesita";
        $classe_card = "obesita";
    }
    $log_class = $fascia;
}

// 5. Scrivo la riga nel file log.csv
$riga_log = "$data_ora,$ip,$log_nome,$log_altezza,$log_peso,$log_bmi,$log_class\n";
file_put_contents('log.csv', $riga_log, FILE_APPEND);

// Se i dati inseriti non erano validi, torna al form
if ($log_bmi == "NOT_VALID" || $log_nome == "NOT_VALID") {
    header("Location: form.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Il tuo BMI</title>
    <style>
        body { font-family: sans-serif; background: #F1F3F5; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .container { text-align: center; width: 300px; }
        .card { border: 3px solid; padding: 20px; border-radius: 10px; background: white; }
        .sottopeso { background: #E3EEF9; border-color: #2F6FB3; color: #2F6FB3; }
        .normopeso { background: #E3F4E6; border-color: #256D34; color: #256D34; }
        .sovrappeso { background: #FCEBD7; border-color: #9A4F06; color: #9A4F06; }
        .obesita { background: #FBE2E2; border-color: #B32D2D; color: #B32D2D; }
        a { display: inline-block; margin-top: 15px; color: #333; }
    </style>
</head>
<body>

    <div class="container">
        <h1>Il tuo BMI</h1>

        <div class="card <?php echo $classe_card; ?>">
            <h2><?php echo $log_nome; ?></h2>
            <p>Peso: <?php echo $log_peso; ?> kg | Altezza: <?php echo $log_altezza; ?> cm</p>
            <h1 style="font-size: 40px;"><?php echo $log_bmi; ?></h1>
            <h3><?php echo strtoupper($log_class); ?></h3>
        </div>

        <a href="form.html">Calcola di nuovo</a>
    </div>

</body>
</html>
</body>
</html>