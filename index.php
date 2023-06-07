<?php


require_once(realpath($_SERVER['DOCUMENT_ROOT'] . '/src/Pages/IndexPage.php'));

$index = new IndexPage();
$index->refreshData();
$currencyFrom = $_GET['currencyFrom'] ?? 'PLN';
$currencyTo = $_GET['currencyTo'] ?? 'EUR';
$ammount = $_GET['ammount'] ?? 10;

if(!is_numeric($ammount)){
    exit('Wprowadzono błędne dane');
}

$calculation = $index->calculate($currencyFrom, $currencyTo, $ammount);

$rates = $index->latestRates();
$latestCalc = $index->latestCalculations();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalkulator Walut</title>
    <link rel="stylesheet" href="./public/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
</head>
<body>
    <div class="header">
        <h1>Kalkulator walutowy</h1>
    </div>
    <div class="wrapper">
        <div class="col col-1" style="border-right: 1px solid gray">
            <h2 class="title">Obecny kurs walut</h2>
            <?php if(count($rates) > 0){ ?>
            <table class="ratesTable">
                <thead>
                    <td>Kod</td>
                    <td>Waluta</td>
                    <td>Średni kurs waluty</td>
                </thead>
                <tbody>
                <?php foreach($rates as $rate){ ?>
                <tr>
                    <td><?= $rate['code'] ?></td>
                    <td><?= $rate['currency'] ?></td>
                    <td><?= $rate['value'] ?></td>
                </tr>
                <?php } ?>
                </tbody>

            </table>
            <?php } else { ?>
                <p>Brak aktualnych danych</p>
            <?php } ?>
        </div>
        <div class="col col-2">
            <div class="row" style="border-bottom: 1px solid gray">
                <form action="#" method="GET" class="currencyForm">
                    <label></label>
                    <input type="number" step="0.01" name="ammount" value="<?= $ammount ?>">

                    <select name="currencyFrom" class="currencyInput">
                        valdiate
                        <?php foreach($rates as $rate){
                            if($rate['code'] == $currencyFrom){
                                ?>
                            <option value="<?= $rate['code'] ?>" selected><?= $rate['code'] ?></option>
                            <?php }else{ ?>
                            <option value="<?= $rate['code'] ?>"><?= $rate['code'] ?></option>
                        <?php }} ?>
                    </select>
                    <span class="material-symbols-outlined">
                    arrow_forward
                    </span>
                    <select name="currencyTo" class="currencyInput">
                        <option value="PLN">PLN</option>
                        <?php foreach($rates as $rate){
                            if($rate['code'] == $currencyTo){
                                ?>
                            <option value="<?= $rate['code'] ?>" selected><?= $rate['code'] ?></option>
                            <?php }else{ ?>
                            <option value="<?= $rate['code'] ?>"><?= $rate['code'] ?></option>
                        <?php }} ?>
                    </select>
                    </br>
                    <button class="btn" type="submit">Przelicz walutę</button>

                </form>

                <h2 class="result"><?= $ammount ?> <?= $currencyFrom ?> = <?= $calculation['result']?> <?= $currencyTo ?></h2>
            </div>
            <div class="row">

            <h2 class="title">Ostatnie przeliczenia</h2>

            <table class="ratesTable">
                <thead>
                    <td>Waluta startowa - kod</td>
                    <td>Waluta startowa</td>
                    <td>Ilość startowa</td>
                    <td>Waluta docelowa - kod</td>
                    <td>Waluta docelowa</td>
                    <td>Ilość docelowa</td>
                    <td>Data</td>
                </thead>
                <tbody>
                <?php foreach($latestCalc as $late){ ?>
                <tr>
                    <td><?= $late['fromCode'] ?></td>
                    <td><?= $late['fromCurrency'] ?></td>
                    <td><?= number_format($late['initValue'],2) ?></td>
                    <td><?= $late['toCode'] ?></td>
                    <td><?= $late['toCurrency'] ?></td>
                    <td><?= $late['calculated'] ?></td>
                    <td><?= $late['dateTime'] ?></td>
                </tr>
                <?php } ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
    <div class="footer">

    </div>
</body>
</html>


