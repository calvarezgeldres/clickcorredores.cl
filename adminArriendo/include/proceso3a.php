<?php
require_once("../clases/class.coneccion.php");
$miCon=new coneccion();
$link=$miCon->conectar();
//mysqli_set_charset($miCon, 'utf8');
$id=htmlentities($_POST["idCiudad"]);
$sql="select* from mm_comuna where idCiudad='".$id."'";
 

$query=mysqli_query($link,$sql);
while($row=mysqli_fetch_array($query)){
    $datos[][$row["idComuna"]]=$row["nombre"];
}
 
echo json_encode($datos);
 
?>