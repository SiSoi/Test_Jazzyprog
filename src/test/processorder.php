<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order result</title>
</head>
<body>
<h1>ORDER RESULT</h1>
<?php
    define('TIREPRICE', 100.00);
    define('OILPRICE', 50.00);
    define('SPARKPRICE', 70.00);

    $total_qty = $_POST['tireqty'] + $_POST['oilqty'] + $_POST['sparkqty'];
    $total_amount = TIREPRICE * $_POST['tireqty']
                    + OILPRICE * $_POST['oilqty']
                    + SPARKPRICE * $_POST['sparkqty'];
    $tax_rate = 0.10;

    echo '<h2>Order processed at '.date('H:i jS, F Y').' </h2>';
    echo '<table>';
    echo '<tr style="border-bottom: black"><td style="text-align: center; width: 150px">Name</td><td style="text-align: center; width: 100px">Quantity</td><td style="text-align: center; width: 100px">Unit price</td></tr>';
    echo '<tr><td style="text-align: left">Tire</td><td style="text-align: right">'.$_POST['tireqty'].'</td><td style="text-align: right">'.TIREPRICE.'</td></tr>';
    echo '<tr><td style="text-align: left">Oil</td><td style="text-align: right">'.$_POST['oilqty'].'</td><td style="text-align: right">'.OILPRICE.'</td></tr>';
    echo '<tr><td style="text-align: left">Spark plug</td><td style="text-align: right">'.$_POST['sparkqty'].'</td><td style="text-align: right">'.SPARKPRICE.'</td></tr>';
    echo '<tr><td style="text-align: left">Total:</td><td style="text-align: right">'.$total_qty.'</td><td style="text-align: right">'.$total_amount.'</td></tr>';
    echo '<tr><td colspan="2"; style="text-align: right">Tax rate:</td><td style="text-align: right">'.($tax_rate * 100).' %</td></tr>';
    echo '<tr><td colspan="2"; style="text-align: right">Final:</td><td style="text-align: right">'.number_format($total_amount * $tax_rate, 2).'</td></tr>';
    echo '</table>';
?>
</body>
</html>
