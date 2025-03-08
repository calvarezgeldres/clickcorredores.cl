<?php
require 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Configura Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);

// Contenido HTML (formulario)




$html = '<!DOCTYPE html>
<html>
<head>

</head>
    <title>Ejemplo de Tabla</title>
    <style>
          body {
            font-family: Arial, sans-serif;
           
        }
        h1 {
            font-family: Arial, sans-serif !important;
        }
        h3 {
            font-family: Arial, sans-serif !important;
        }
        /* Estilos para la primera tabla con bordes transparentes */
        table.first-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }
        table.first-table th, table.first-table td {
            border: none;
            padding:5px;   
            font-family: Arial, sans-serif;
            font-size:14px;         
        }

        /* Estilos para la segunda tabla con bordes regulares */
        table.second-table {
            width: 100%;
            border-collapse: collapse;
            border-color:#ddd;
            border-width:1px;
            border-style:solid;
            font-size:14px;
            font-family: Arial, sans-serif;
        }
        table.second-table th, table.second-table td {
            border: 1px solid #000; /* Color de los bordes regulares (negro) */
            padding: 8px;
            border-color:#ddd;
            border-width:1px;
            border-style:solid;
            font-size:14px;
            font-family: Arial, sans-serif;
        }
        table.fourth-table {
            width: 100%;
            border-collapse: collapse;
       
            font-size:14px;
            font-family: Arial, sans-serif;
        }
    </style>
</head>
<body >
    <div  align="center" style="margin-bottom:40px;"><h2 style="font-family: Arial, sans-serif;">Liquidacion de arriendo</h2></div>
    <div>
        <table class="first-table">
            <tr>
                <td width="18%"><b>Propiedad:</b></td>
                <td>ANTONIO VARAS 979 605 979, Las Condes, Región Metropolitana</td>
            </tr>
            <tr>
                <td><b>Comuna:</b></td>
                <td>Las Condes</td>
            </tr>
            <tr>
                <td><b>Propietario (a):</b></td>
                <td>Javiera Henríquez Barbos</td>
            </tr>
            <tr>
                <td><b>RUT:</b></td>
                <td>17.405.812-1</td>
            </tr>
            <tr>
                <td><b>Periodo:</b></td>
                <td>Periodo: 16 octubre 2023 - 15 noviemb</td>
            </tr>
            
        </table>
        
    </div>
    
    <div style="margin-top:30px;margin-bottom:20px;font-family: Arial, sans-serif; "><h3 style="font-family: Arial, sans-serif;">Detalles de los movimientos</h3></div>
    <div style="margin-top:30px;">
        <table class="second-table" border="0" width="100%">
            <tr>
                <th>Fecha</th>
                <th>Razón</th>
                <th>Abonos</th>
                <th>Descuentos</th>
                <th>Saldo</th>
            </tr>
            <tr>
                <td>Saldo anterior</td>
                <td></td>
                <td></td>
                <td></td>
                <td>$0</td>
            </tr>
            <tr>
                <td>25/10/2023</td>
                <td>Abono al canon de arriendo</td>
                <td>$3.000.000</td>
                <td></td>
                <td>$3.000.000</td>
            </tr>
            <tr>
                <td>25/10/2023</td>
                <td>Descuento por pagos de GGCC arrendatario anterior</td>
                <td>$130.000</td>
                <td></td>
                <td>$3.130.000</td>
            </tr>
            <tr>
                <td>25/10/2023</td>
                <td>Descuento por cambio de chapa</td>
                <td>$50.000</td>
                <td></td>
                <td>$2.950.000</td>
            </tr>
            <tr>
                <td>25/10/2023</td>
                <td>Comisión: pagos recibidos al 25/10/2023 (10%)</td>
                <td>$313.000</td>
                <td></td>
                <td>$2.637.000</td>
            </tr>
            <tr>
                <td>25/10/2023</td>
                <td>IVA</td>
                <td></td>
                <td>$59.470</td>
                <td>$2.577.530</td>
            </tr>
            <tr>
                <td>Totales</td>
                <td></td>
                <td>$3.130.000</td>
                <td>$552.470</td>
                <td>$2.577.530</td>
            </tr>
           
        </table>
    </div>
    <div align="right" style="margin-top:20px;font-size:18px;"><b>Total a Pagar: $2.577.530</b></div>

    <div style="margin-top:20px;margin-bottom:20px;"><h3 style="font-family: Arial, sans-serif;">Cuentas de pago</h3></div>
<div style="margin-top:30px;">
    <table class="fourth-table">
        
        <tr>
            <th style="text-align: left;" width="14%">Nombre:</th>
            <td>Javiera Henríquez Barbosa</td>
        </tr>
        <tr>
        <th style="text-align: left;">Titular:</th>
        </tr>
        <tr>
            <th style="text-align: left;">RUT:</th>
            <td>17.405.812-1</td>
        </tr>
        <tr>
            <th style="text-align: left;">Banco:</th>
            <td>Banco Consorcio</td>
        </tr>
        <tr>
            <th style="text-align: left;">Cuenta:</th>
            <td>456</td>
        </tr>
    </table>
</div>



</body>
</html>

';

// Cargar el contenido HTML en Dompdf
$dompdf->loadHtml($html);

// Configurar el papel y la orientación del PDF
$dompdf->setPaper('A4', 'portrait');

// Renderizar el PDF
$dompdf->render();

// Ruta de la carpeta donde deseas guardar el PDF
$carpeta_destino = './pdf/';

// Nombre del archivo PDF
$nombre_archivo = 'liquidacion_'.date("dmY_His").'.pdf';

// Guardar el PDF en la carpeta destino
$archivo_pdf = $carpeta_destino . '/' . $nombre_archivo;
file_put_contents($archivo_pdf, $dompdf->output());

echo 'PDF generado y guardado en ' . $archivo_pdf;
?>