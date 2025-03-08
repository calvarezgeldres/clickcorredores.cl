<?php
require_once("./clases/class.coneccion.php");
$miCon=new coneccion();
$link=$miCon->conectar();
if(isset($_GET["idLiq"])){
    $idLiq=htmlentities($_GET["idLiq"]);
}


$sql="select* from mm_liquidacionArriendo where idLiquidacion='".$idLiq."'";

 
$q=mysqli_query($link,$sql);
$r=mysqli_fetch_array($q);
$idProp=$r["idProp"];
$idArriendo=$r["idArriendo"];
$fechaCobro=devolverFechaCobro($idArriendo);
$sql3="select* from mm_detalleLiquidacion where idLiquidacion='".$idLiq."'";

$q3=mysqli_query($link,$sql3);


function actualizarRuta($pdf,$idLiq){
    $miCon=new coneccion();
    $link=$miCon->conectar();
    $sql="update mm_liquidacionArriendo set rutaPdf='".$pdf."' where idLiquidacion='".$idLiq."'";
    
    mysqli_query($link,$sql);
    return(true);
}
function devolverFechaCobro($idArriendo){
    $miCon=new coneccion();
    $link=$miCon->conectar();
    $sql="select fechaCancelacion from mm_arriendos where idArriendo='".$idArriendo."'";
    $q=mysqli_query($link,$sql);
    $r=mysqli_fetch_array($q);
    return($r["fechaCancelacion"]);
}
 
 function devolverRegion($id){
    $miCon=new coneccion();
    $link=$miCon->conectar();
    $sql="select* from mm_region where idRegion='".$id."'";    
    $q=mysqli_query($link,$sql) or die(mysqli_error($link));
    $r=mysqli_fetch_array($q);
    mysqli_free_result($q);
    return($r["nombre"]);
}
function devolverComuna($id){
    $miCon=new coneccion();
    $link=$miCon->conectar();
    $sql="select* from mm_comuna where idComuna='".$id."'";	 
    $q=mysqli_query($link,$sql) or die(mysqli_error($link));
    $r=mysqli_fetch_array($q); 
    mysqli_free_result($q);
    return($r["nombre"]);
}
function devolverDatos($idProp){
    $miCon=new coneccion();
    $link=$miCon->conectar();
    $sql1="select direccionProp,idComuna,idRegion,idPropietario from mm_propiedad1 where idProp='".$idProp."'";
    $q1=mysqli_query($link,$sql1);
    $r1=mysqli_fetch_array($q1);    
    $direccion=$r1["direccionProp"].",".devolverComuna($r1["idComuna"]).",".devolverRegion($r1["idRegion"]);
    $comuna=$r1["idComuna"];
    $idPropi=$r1["idPropietario"];
    $m["direccion"]=$direccion;
    $m["comuna"]=devolverComuna($comuna);
    $m["idPropi"]=$idPropi;
    return($m);
}
function cuentaBancariaPropi($idPropi){
    $miCon=new coneccion();
    $link=$miCon->conectar();
    $sql="select* from  mm_cuentaBancaria where idPropietario='".$idPropi."'";
    $q=mysqli_query($link,$sql);
    $r=mysqli_fetch_array($q);
    $m["nombre"]=$r["nomTitular"];
    $m["rut"]=$r["rutTitular"];
    $m["banco"]=$r["banco"];
    $m["cuenta"]=$r["numeroCuenta"];

    return($m);
}
function formatoNumerico($num){
	$n=number_format($num, 0,",",".");
	return($n);
}
function devolverPropi($idPropi){
    $miCon=new coneccion();
    $link=$miCon->conectar();
    $sql1="select nombre,apellido,rut from mm_propietarios  where idPropietario='".$idPropi."'";
    $q1=mysqli_query($link,$sql1);
    $r1=mysqli_fetch_array($q1);  
    $c["nombre"]=$r1["nombre"]."&nbsp;".$r1["apellido"];
    $c["rut"]=$r1["rut"];
return($c);
}
function devolverBanco($id){
    $arrSel = array(1=>
    "Banco De Chile - Edwards",
    "Banco Bice",
    "Banco Consorcio",
    "Banco del Estado de Chile",
    "Banco Do Brasil S.A.",
    "Banco Falabella",
    "Banco Internacional",
    "Banco Paris",
    "Banco Ripley",
    "Banco Santander",
    "Santander Office Banking",
    "Banco Security",
    "Banco Coopeuch",
    "Citibank",
    "BBVA",
    "BCI",
    "HSBC Bank",
    "Itau",
    "Itau-Corpbanca",
    "Scotiabank"
  );
  return($arrSel[$id]);
}
$m1=devolverDatos($idProp);


$direccion=$m1["direccion"];
$comuna=$m1["comuna"];
$idPropi=$m1["idPropi"];
$m2=devolverPropi($idPropi);
$nomPropi=$m2["nombre"];
$rutPropi=$m2["rut"];
$periodo=date("d-m-Y",$r["fecha"]);
$razon=$r["razon"];
$montoPagar=formatoNumerico($r["montoPagar"]);
$m3=cuentaBancariaPropi($idPropi);
$nomTitular=$m3["nombre"];
$rutTitular=$m3["rut"];
$banco=devolverBanco($m3["banco"]);
$cta=$m3["cuenta"];


 
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
                <td>'.utf8_encode(ucfirst($direccion)).'</td>
            </tr>
            <tr>
                <td><b>Comuna:</b></td>
                <td>'.ucfirst($comuna).'</td>
            </tr>
            <tr>
                <td><b>Propietario (a):</b></td>
                <td>'.ucfirst($nomPropi).'</td>
            </tr>
            <tr>
                <td><b>RUT:</b></td>
                <td>'.ucfirst($rutPropi).'</td>
            </tr>
            <tr>
                <td><b>Periodo:</b></td>
                <td>Periodo: '.$periodo.'</td>
            </tr>
            
        </table>
        
    </div>
    
    <div style="margin-top:30px;margin-bottom:20px;font-family: Arial, sans-serif; "><h3 style="font-family: Arial, sans-serif;">Detalles de los movimientos</h3></div>
    <div style="margin-top:30px;">
        <table class="second-table" border="0" width="100%">
            <tr>
                <th>Fecha</th>
                <th>Razón</th>
                <th>Abono</th>
                <th>Descuento</th>
                <th>Saldo</th>
            </tr>';
  
while($r3=mysqli_fetch_array($q3)){
    $abo[]=$r3["abono"];
    $des[]=$r3["descuento"];
    $sal[]=$r3["abono"]-$r3["descuento"];
    $total=array_sum($abo)-array_sum($des);
            $html.= '<tr>
                <td>'.date("d-m-Y",strtotime($fechaCobro)).'</td>
                <td>'.$r3["concepto"].'</td>
                <td>$'.formatoNumerico($r3["abono"]).'</td>
                <td>$'.formatoNumerico($r3["descuento"]).'</td>
                <td>$'.formatoNumerico($r3["abono"]-$r3["descuento"]).'</td>
            </tr>';
}
$html.= "<tr><td colspan=2>&nbsp;</td><td>$ ".formatoNumerico(array_sum($abo))."</td><td>$ ".formatoNumerico(array_sum($des))."</td><td>$ ".formatoNumerico(array_sum($sal))."</td></tr>";
$html.= "<tr><td colspan=3>&nbsp;</td><td><b style='font-size:18px;'>Total a Pagar :</b></td><td><b style='font-size:18px;'>$".formatoNumerico($total)."</b></td></tr>";
           
        $html.='</table>
    </div>
    

    <div style="margin-top:20px;margin-bottom:20px;"><h3 style="font-family: Arial, sans-serif;">Cuentas de pago</h3></div>
<div style="margin-top:30px;">
    <table class="fourth-table">
          
        <tr>
            <th style="text-align: left;" width="14%">Nombre:</th>
            <td>'.ucfirst($nomPropi).'</td>
        </tr>
        <tr>
        <th style="text-align: left;">Titular:</th>
        </tr>
        <tr>
            <th style="text-align: left;">RUT:</th>
            <td>'.$rutTitular.'</td>
        </tr>
        <tr>
            <th style="text-align: left;">Banco:</th>
            <td>'.$banco.'</td>
        </tr>
        <tr>
            <th style="text-align: left;">Cuenta:</th>
            <td>'.$cta.'</td>
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

actualizarRuta($nombre_archivo,$idLiq);

// Guardar el PDF en la carpeta destino
$archivo_pdf = $carpeta_destino . '/' . $nombre_archivo;
file_put_contents($archivo_pdf, $dompdf->output());

//header("location:prueba2.php");
exit;
?>