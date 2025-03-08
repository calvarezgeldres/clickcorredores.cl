<?php
require_once("../clases/class.coneccion.php");
$miCon=new coneccion();
$link=$miCon->conectar();
$id=$_POST["id"];
$sql="select* from subMenu where idCategoria='".$id."'";
 
$query=mysqli_query($link,$sql);
if(mysqli_num_rows($query)!=0){
while($row=mysqli_fetch_array($query)){
    $datos[][$row["idSub"]]=utf8_encode($row["nombre"]);
}
}else{
    $datos=array();
}
$total=count($datos);
 $miCon->cerrar();
 if($total==0){
	 echo "error";
 }else{
	echo json_encode($datos);
 }

?>