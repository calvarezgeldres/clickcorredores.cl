<?php
ob_start();
session_start();
 error_reporting(1);
require_once("../clases/class.coneccion.php");
$miCon=new coneccion();
$link=$miCon->conectar();
if(isset($_POST["action"])){
	$email=htmlentities($_POST["email"]);
	$pass=htmlentities($_POST["pass"]);
 
	$sql="select* from  admin where nombre='".$email."' and pass='".$pass."'";
	  $query=mysqli_query($link,$sql) or die(mysqli_error($link));
                if(mysqli_num_rows($query)==0){
                   echo "error";
                }else{
                 $row=mysqli_fetch_array($query);                 
                 $_SESSION["auth"]["nick"]=$row["nombre"];
                 $_SESSION["auth"]["id"]=$row["idAdmin"];
			//	 $_SESSION["auth"]["tipo"]="admin";
			//	 $_SESSION["auth"]["rutaFoto"]=$row["rutaFoto"];
                 echo "ok";
                }            
}
?>