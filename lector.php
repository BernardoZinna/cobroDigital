<!DOCTYPE html>
<html lang="en">

<head>
    <title>Lector archivos</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>

    <div class="container mt-12">
        <table class="table">
            <thead>
                <tr>
                    <th>IDENTIFICADOR</th>
                    <th>NRO TRANSACCION</th>
                    <th>MONTO</th>
                    <th>FECHA DE PAGO</th>
                    <th>MEDIO DE PAGO</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $file = fopen("888ENTES5723_308.txt", "r") or die("Unable to open file!");

                $mediosPagoAr = [
                    '00' => [
                        'nombre' => 'Efectivo',
                        'subtotal' => 0,
                        'contador' => 0
                    ],
                    '90' => [
                        'nombre' => 'Debito',
                        'subtotal' => 0,
                        'contador' => 0
                    ],
                    '99' => [
                        'nombre' => 'Credito',
                        'subtotal' => 0,
                        'contador' => 0
                    ]
                ];

                $subTotal = 0;
                $cont = 0;

                while (!feof($file)) {
                    $data = fgets($file);

                    if (str_contains($data, 'HEADER') || str_contains($data, 'TRAILER')) {
                        continue;
                    }

                    echo "<tr>";
                    $data = str_split($data, 1);

                    $identificador = array_slice($data, 58, 19);
                    echo "<td>" . implode("", $identificador) . "</td>";

                    $nroTransaccion = array_slice($data, 40, 8);
                    echo "<td>" . implode("", $nroTransaccion) . "</td>";

                    $monto = implode("", array_slice($data, 77, 11));
                    $subTotal += $monto;
                    echo "<td>" . ltrim($monto, "0") . "</td>";

                    $fechaPago = implode("", array_slice($data, 224, 6));

                    $dateTime = DateTime::createFromFormat('ymd', $fechaPago);
                    echo "<td>" . $dateTime->format('d/m/Y') . "</td>";

                    $medioPago = implode("", array_slice($data, 247, 2));
                    echo "<td>" . (isset($mediosPagoAr[$medioPago]['nombre']) ? $mediosPagoAr[$medioPago]['nombre'] : 'N/A') . "</td>";
                    
                    if(isset($mediosPagoAr[$medioPago]['subtotal'])){
                        $mediosPagoAr[$medioPago]['subtotal'] += $monto;
                    }

                    if(isset($mediosPagoAr[$medioPago]['contador'])){
                        $mediosPagoAr[$medioPago]['contador']++;
                    }

                    echo "</tr>";

                    $cont++;
                }

                fclose($file);
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th>TOTAL</th>
                    <th></th>
                    <th><?php echo $subTotal ?></th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
        <div class="col">
                <p>CANTIDAD TOTAL DE REGISTROS COBRADOS: <?php echo $cont;?></p>
                <?php
                    foreach($mediosPagoAr as $m){
                        echo "<p>PROMEDIO PAGOS EN ".strtoupper($m['nombre']).": ";
                        echo $m['contador']>0 ? round($m['subtotal']/($m['contador']), 2) : 0;
                        echo "</p>";
                    }
                ?>
        </div>
    </div>
</body>

</html>