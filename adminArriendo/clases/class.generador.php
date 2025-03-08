<?php
 ob_start(); 
require_once("./clases/class.coneccion.php"); 


require './vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;


class pdf extends coneccion{     
	public $link;
	public function __construct(){
		$this->link=$this->conectar();
		
 	} 
    public function devolverValorGarantia($idArriendo){
        $sql="select garantia from mm_arriendos where idArriendo='".$idArriendo."'";	
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
        $m3=$r["garantia"];
        return($m3);
    }
    public function devolverArriendo($idArriendo){
        $sql="select idArrendatarios from mm_arriendos where idArriendo='".$idArriendo."'";	
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
        $m3=$r["idArrendatarios"];
        return($m3);
    }
    public function devolverArrendatario($idArrendatario){        
        $sql="select nombre,rut from mm_arrendatario where idArrendatario='".$idArrendatario."'";	
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
        $m3["nombre"]=$r["nombre"];
        $m3["rut"]=$r["rut"];
   
        return($m3);
    }
    public function generarPdfGarantia($idGarantia,$idArriendo){
 

       
    $idProp=$_GET["idProp"];
    $idArrendatario=$this->devolverArriendo($idArriendo);    
    $valorGarantia=$this->devolverValorGarantia($idArriendo);


    $m10=$this->devolverArrendatario($idArrendatario);
    $nomArrendatario=$m10["nombre"]."/".$m10["rut"];

    
    $m1=$this->devolverDatos($idProp);

    
    $direccion=$m1["direccion"];
    $comuna=$m1["comuna"];
    $idPropi=$m1["idPropi"];
    $m2=$this->devolverPropi($idPropi);
    $nomPropi=$m2["nombre"];
    $rutPropi=$m2["rut"];
    $periodo=date("d-m-Y",$r["fecha"]);
    $razon=$r["razon"];
    $montoPagar=$this->formatoNumerico($r["montoPagar"]);
    $m3=$this->cuentaBancariaPropi($idPropi);
    $nomTitular=$m3["nombre"];
    $rutTitular=$m3["rut"];
    $banco=$this->devolverBanco($m3["banco"]);
    $cta=$m3["cuenta"];

 

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
    <div align="left">
    <div style="margin-bottom:30px;"><img src="https://clickcorredores.cl/logoClick.png" /></div>
    </div>
    <div  align="center" style="margin-bottom:40px;"><h2 style="font-family: Arial, sans-serif;">Liquidación de Garantía</h2></div>
    <div>
        <table class="first-table">
        <tr>
                <td><b>Propietario (a):</b></td>
                <td>'.ucfirst($nomPropi).'</td>
            </tr>
            <tr>
            <td><b>RUT:</b></td>
            <td>'.ucfirst($rutPropi).'</td>
        </tr>
        <tr>
        <td><b>Arrendatario:</b></td>
        <td>'.ucfirst($nomArrendatario).'</td>
    </tr>
            <tr>
                <td width="18%"><b>Propiedad:</b></td>
                <td>'.ucfirst($direccion).'</td>
            </tr>
            
            
         
           
            
        </table>
        
    </div>';
    $html.="<h3>Valor de la Garantía: $".$this->formatoNumerico($valorGarantia)."</h3>";
    $html.= '
    
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
            $sql3="select* from mm_detalleGarantia where idGarantia='".$idGarantia."'";	
       
            $q3=mysqli_query($this->link,$sql3);
         

while($r3=mysqli_fetch_array($q3)){
    $abo[]=$r3["abono"];
    $des[]=$r3["descuento"];
    $sal[]=$r3["abono"]-$r3["descuento"];
    $total=array_sum($abo)-array_sum($des);
            $html.= '<tr>
                <td>'.$r3["fecha"].'</td>
                <td>'.$r3["concepto"].'</td>
                <td>$'.$this->formatoNumerico($r3["abono"]).'</td>
                <td>$'.$this->formatoNumerico($r3["descuento"]).'</td>
                <td>$'.$this->formatoNumerico($r3["abono"]-$r3["descuento"]).'</td>
            </tr>';
}
$html.= "<tr><td colspan=2>&nbsp;</td><td>$ ".$this->formatoNumerico(array_sum($abo))."</td><td>$ ".$this->formatoNumerico(array_sum($des))."</td><td>$ ".$this->formatoNumerico(array_sum($sal))."</td></tr>";
$html.= "<tr><td colspan=3>&nbsp;</td><td><b style='font-size:14px;'>Total Descuentos :</b></td><td><b style='font-size:16px;'>$".$this->formatoNumerico(array_sum($des))."</b></td></tr>";

$html.= "<tr><td colspan=3>&nbsp;</td><td><b style='font-size:14px;'>Total a devolver :</b></td><td><b style='font-size:16px;'>$".$this->formatoNumerico($total)."</b></td></tr>";

           
        $html.='</table>
    </div>
    

   
<div style="margin-top:30px;" align="right">';
setlocale(LC_TIME, 'es_ES.UTF-8');

$fecha = strtotime(date("d-m-Y"));
$html.=strftime("%e de %B de %Y", $fecha);
$html.= '</div>



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
$nombre_archivo = 'liquidacionGarantia_'.$idGarantia."_".date("dmY").'.pdf';

$this->actualizarRutaGarantia($nombre_archivo,$idGarantia);

// Guardar el PDF en la carpeta destino
$archivo_pdf = $carpeta_destino . '/' . $nombre_archivo;
file_put_contents($archivo_pdf, $dompdf->output());
return(true);


    }
	public function generarPdf($idLiq){
		
 
		$sql="select* from mm_liquidacionArriendo where idLiquidacion='".$idLiq."'";				 
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
		$idProp=$r["idProp"];
		$idArriendo=$r["idArriendo"];
		$fechaCobro=$this->devolverFechaCobro($idArriendo);
		$sql3="select* from mm_detalleLiquidacion where idLiquidacion='".$idLiq."'";		
		$q3=mysqli_query($this->link,$sql3);
		$m1=$this->devolverDatos($idProp);


		$direccion=$m1["direccion"];
		$comuna=$m1["comuna"];
		$idPropi=$m1["idPropi"];
		$m2=$this->devolverPropi($idPropi);
		$nomPropi=$m2["nombre"];
		$rutPropi=$m2["rut"];
		$periodo=date("d-m-Y",$r["fecha"]);
		$razon=$r["razon"];
		$montoPagar=$this->formatoNumerico($r["montoPagar"]);
		$m3=$this->cuentaBancariaPropi($idPropi);
		$nomTitular=$m3["nombre"];
		$rutTitular=$m3["rut"];
		$banco=$this->devolverBanco($m3["banco"]);
		$cta=$m3["cuenta"];

		
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
    <div align="left">
    <div style="margin-bottom:30px;"><img src="https://clickcorredores.cl/logoClick.png" /></div>
    </div>
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
                <td>$'.$this->formatoNumerico($r3["abono"]).'</td>
                <td>$'.$this->formatoNumerico($r3["descuento"]).'</td>
                <td>$'.$this->formatoNumerico($r3["abono"]-$r3["descuento"]).'</td>
            </tr>';
}
$html.= "<tr><td colspan=2>&nbsp;</td><td>$ ".$this->formatoNumerico(array_sum($abo))."</td><td>$ ".$this->formatoNumerico(array_sum($des))."</td><td>$ ".$this->formatoNumerico(array_sum($sal))."</td></tr>";
$html.= "<tr><td colspan=3>&nbsp;</td><td><b style='font-size:18px;'>Total a Pagar :</b></td><td><b style='font-size:18px;'>$".$this->formatoNumerico($total)."</b></td></tr>";
           
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
$nombre_archivo = 'liquidacion_'.$idLiq."_".date("dmY").'.pdf';

$this->actualizarRuta($nombre_archivo,$idLiq);

// Guardar el PDF en la carpeta destino
$archivo_pdf = $carpeta_destino . '/' . $nombre_archivo;
file_put_contents($archivo_pdf, $dompdf->output());
return(true);


	}

public function actualizarRutaGarantia($pdf,$idGarantia){
        $miCon=new coneccion();
        $this->link=$miCon->conectar();
        $sql="update mm_garantia set pdf='".$pdf."' where idGarantia='".$idGarantia."'";
        
        mysqli_query($this->link,$sql);
        return(true);
   }
public function actualizarRuta($pdf,$idLiq){
    $miCon=new coneccion();
    $this->link=$miCon->conectar();
    $sql="update mm_liquidacionArriendo set rutaPdf='".$pdf."' where idLiquidacion='".$idLiq."'";
    
    mysqli_query($this->link,$sql);
    return(true);
}
public function devolverFechaCobro($idArriendo){
    $miCon=new coneccion();
    $this->link=$miCon->conectar();
    $sql="select fechaCancelacion from mm_arriendos where idArriendo='".$idArriendo."'";
    $q=mysqli_query($this->link,$sql);
    $r=mysqli_fetch_array($q);
    return($r["fechaCancelacion"]);
}
 
public function devolverRegion($id){
 
   
    $sql="select* from mm_region where idRegion='".$id."'";   
	
    $q=mysqli_query($this->link,$sql);
    $r=mysqli_fetch_array($q);
 
    return($r["nombre"]);
}
public function devolverComuna($id){

    
    $sql="select* from mm_comuna where idComuna='".$id."'";	 
    $q=mysqli_query($this->link,$sql);
    $r=mysqli_fetch_array($q); 
  
    return($r["nombre"]);
}
public function devolverDatos($idProp){    
	$sql1="select direccionProp,idComuna,idRegion,idPropietario from mm_propiedad1 where idProp='".$idProp."'";
    $q1=mysqli_query($this->link,$sql1);
    $r1=mysqli_fetch_array($q1);    
	$direccion=$r1["direccionProp"];
	 
 
    
    
    $comuna=$r1["idComuna"];
    $idPropi=$r1["idPropietario"];
    $m["direccion"]=$direccion;
    $m["comuna"]=$this->devolverComuna($comuna);
    $m["idPropi"]=$idPropi;
    return($m);
	 
}
public function cuentaBancariaPropi($idPropi){
    $miCon=new coneccion();
    $this->link=$miCon->conectar();
    $sql="select* from  mm_cuentaBancaria where idPropietario='".$idPropi."'";
    $q=mysqli_query($this->link,$sql);
    $r=mysqli_fetch_array($q);
    $m["nombre"]=$r["nomTitular"];
    $m["rut"]=$r["rutTitular"];
    $m["banco"]=$r["banco"];
    $m["cuenta"]=$r["numeroCuenta"];

    return($m);
}
public function formatoNumerico($num){
	$n=number_format($num, 0,",",".");
	return($n);
}

public function devolverBanco($id){
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
public function devolverPropi($idPropi){
    $miCon=new coneccion();
    $this->link=$miCon->conectar();
    $sql1="select nombre,apellido,rut from mm_propietarios  where idPropietario='".$idPropi."'";
    $q1=mysqli_query($this->link,$sql1);
    $r1=mysqli_fetch_array($q1);  
    $c["nombre"]=$r1["nombre"]."&nbsp;".$r1["apellido"];
    $c["rut"]=$r1["rut"];
return($c);
}
} 





?>