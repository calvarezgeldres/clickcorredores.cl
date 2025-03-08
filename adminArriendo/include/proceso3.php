<?php
require_once("../clases/class.coneccion.php");
$miCon=new coneccion();
$link=$miCon->conectar();
 
$id=htmlentities($_POST["idRegion"]);
$sql="select* from mm_ciudad where idRegion='".$id."'";


$query=mysqli_query($link,$sql);

while($row=mysqli_fetch_array($query)){
    
    $datos[][$row["idCiudad"]]=$row["ciudad"];
}
 
echo json_encode($datos);
 
?>