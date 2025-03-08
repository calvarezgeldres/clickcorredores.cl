<?php
require_once("../clases/class.coneccion.php");
$miCon=new coneccion();
$conn=$miCon->conectar();
 
 $estado=$_POST["estado"];
 $idProp=$_POST["idProp"];

 $sql="update mm_propiedad set estadoPublicacion='".$estado."' where idProp='".$idProp."'";
 if($q=mysqli_query($conn,$sql)){
  echo "true";
 }else{
  echo "false";
 }
?>
 
 