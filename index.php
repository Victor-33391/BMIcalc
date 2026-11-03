<?php
// Controlliamo se l'utente ha inviato il form tramite il metodo POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Data e ora dinamiche al momento dell'invio
    $data_ora = date("d/m/Y,H:i:s");
    
    // Indirizzo IP del client
    $ip = $_SERVER['REMOTE_ADDR'];

    
    if (isset($_POST['nome'])) {
        $nome = trim($_POST['nome']);
    } else {
        $nome = "";
    }

    if (isset($_POST['peso'])) {
        $peso = $_POST['peso'];
    } else {
        $peso = "";
    }

    if (isset($_POST['altezza'])) {
        $altezza = $_POST['altezza'];
    } else {
        $altezza = "";
    }

    // 3. Validazione del NOME
    if ($nome != "") {
        $log_nome = $nome;
    } else {
        $log_nome = "NOT_VALID";
    }

    // 4. Validazione del PESO
    if (is_numeric($peso) && $peso > 0) {
        $log_peso = $peso;
    } else {
        $log_peso = "NOT_VALID";
    }

    // 5. Validazione dell'ALTEZZA
    if (is_numeric($altezza) && $altezza > 0) {
        $log_altezza = $altezza;
    } else {
        $log_altezza = "NOT_VALID";
    }

    // 6. Calcolo del BMI solo se peso e altezza sono corretti
    if ($log_peso != "NOT_VALID" && $log_altezza != "NOT_VALID") {
        
        $altezza_m = $altezza / 100; // Converte i centimetri in metri
        $bmi = $peso / ($altezza_m * $altezza_m); // Formula BMI
        $log_bmi = number_format($bmi, 2); // Arrotonda a 2 cifre decimali

        // Scelta della categoria e della classe di colore
        if ($bmi < 18.5) {
            $log_class = "Sottopeso";
            $classe_css = "sottopeso";
        } elseif ($bmi <= 24.9) {
            $log_class = "Normopeso";
            $classe_css = "normopeso";
        } elseif ($bmi <= 29.9) {
            $log_class = "Sovrappeso";
            $classe_css = "sovrappeso";
        } else {
            $log_class = "Obesità";
            $classe_css = "obesita";
        }

    } else {
        // Se c'è un errore nei dati
        $log_bmi = "NOT_VALID";
        $log_class = "NOT_VALID";
    }

    // 7. Scrittura della riga nel file log.csv
    $riga = "$data_ora,$ip,$log_nome,$log_altezza,$log_peso,$log_bmi,$log_class\n";
    file_put_contents("log.csv", $riga, FILE_APPEND);
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Calcolo BMI</title>
    <style>
        body { font-family: sans-serif; background: #f4f4f4; text-align: center; padding: 20px; }
        .box { background: white; max-width: 300px; margin: 0 auto; padding: 20px; border-radius: 8px; }
        input { width: 100%; padding: 8px; margin: 5px 0; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #28a745; color: white; border: none; margin-top: 10px; }
        
        /* Stili della scheda risultato */
        .card { padding: 15px; margin-bottom: 15px; border-radius: 5px; border: 2px solid; }
        .sottopeso { background: #e3eef9; border-color: #2f6fb3; color: #2f6fb3; }
        .normopeso { background: #e3f4e6; border-color: #256d34; color: #256d34; }
        .sovrappeso { background: #fcebd7; border-color: #9a4f06; color: #9a4f06; }
        .obesita { background: #fbe2e2; border-color: #b32d2d; color: #b32d2d; }
    </style>
</head>
<body>

    <div class="box">
        <h1>Calcolatore BMI</h1>

        <!-- Mostra il risultato solo se il calcolo è andato a buon fine -->
        <?php if (isset($log_bmi) && $log_bmi != "NOT_VALID"): ?>
            <div class="card <?php echo $classe_css; ?>">
                <h2><?php echo $log_nome; ?></h2>
                <p>Peso: <?php echo $log_peso; ?> kg | Altezza: <?php echo $log_altezza; ?> cm</p>
                <h1><?php echo $log_bmi; ?></h1>
                <h3><?php echo strtoupper($log_class); ?></h3>
            </div>
        <?php endif; ?>

        <!-- Form HTML -->
        <form method="POST" action="index.php">
            <label>Nome:</label>
            <input type="text" name="nome" required>

            <label>Peso (kg):</label>
            <input type="number" step="0.1" name="peso" required>

            <label>Altezza (cm):</label>
            <input type="number" name="altezza" required>

            <button type="submit">Calcola</button>
        </form>
    </div>

</body>
</html>