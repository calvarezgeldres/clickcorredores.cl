
<?php 


session_start();
require_once("../clases/class.coneccion.php");
$miCon=new coneccion();
$link=$miCon->conectar();


if(isset($_POST["nick"]) || isset($_POST["pass"]) || isset($_POST["tipo"])){
    
    $usuario=htmlentities($_POST["nick"]);
    $pass=htmlentities($_POST["pass"]);
    $tipo=htmlentities($_POST["tipo"]);
    $sql="select* from mm_arrendatario where email='".$usuario."' and contra='".$pass."'";
    
    $q=mysqli_query($link,$sql);
 
    

    if(mysqli_num_rows($q)!=0){
        $r=mysqli_fetch_array($q);
        $_SESSION["auth"]["usuario"]=$r["nombre"]."&nbsp;".$r["apellido"];
        $_SESSION["auth"]["idReg"]=$r["idArrendatario"];
        $_SESSION["auth"]["tipo"]="arrendatario";
        $_SESSION["auth"]["correo"]=$r["email"];
        $_SESSION["auth"]["foto"]="./upload/".$r["rutaFoto"];
     
        echo trim("ok");
    }else{
        echo "no";
    }

} 
?>
