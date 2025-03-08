<?php 
session_start();
require_once("./clases/class.publicar.php");

if(!isset($_SESSION["auth"])){
  header("location:loginUser.php");
  exit;
}
$miOrbis=new publicar();
if(isset($_SESSION["auth"]["idUser"])){
  $idUser=$_SESSION["auth"]["idUser"];
}
?>


<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-WO4M4la5SZZR2X4LWhmrraQJYBDt9ibE0l8+y3CddT9KRLtKjaQVuU3kWEHhsV5B+T1Hr0jr5HRmmaz2tnU/0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://kit.fontawesome.com/a50208ac31.js" crossorigin="anonymous"></script>
  <style>
    .container, .container-lg, .container-md, .container-sm, .container-xl {
    max-width: 1190px !important;
}
    </style>
  </head>
  <body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
  <div class="container">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"
      aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <a class="navbar-brand" href="panelUser.php">Panel de Usuario</a>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
      <ul class="navbar-nav">
        <li class="nav-item dropdown">
          
          <div class="dropdown">
                     <a class="btn btn-light dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="true">
                     <i class="fas fa-user" aria-hidden="true"></i>&nbsp;&nbsp; <?php 
                     
                     if(isset($_SESSION["auth"]["email"])){
                        echo $_SESSION["auth"]["email"];
                     }
                     ?>                   </a>
                     <ul class="dropdown-menu" data-bs-popper="none">
                     <li><a class="dropdown-item" href="?mod=panel">Mis Publicaciones</a></li>
                        <li><a class="dropdown-item" href="?mod=panel&op=2">Mi Perfil</a></li>
                        <li><a class="dropdown-item" href="?mod=panel&op=3">Modificar Contraseña</a></li>
                         
                        <li><a class="dropdown-item" href="desconectar2.php">Salir</a></li>
                     </ul>
                  </div>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Content -->
<div class="container mt-5">
  <div class="row">
    <!-- Sidebar -->
    <div class="col-12 col-md-12 col-lg-2 bg-light sidebar">
 
     <div style="margin-top:10px;"><div align="center">
<?php 
 
if(isset($_SESSION["auth"]["foto"])){
  $miOrbis->avatar();
}else{
  echo '<img src="https://www.doomos.cl/template/images/default_avatar.png" style="width:50%;">';
}

?>

     </div></div><div style="margin-top:20px;" class="list-group">
			
     
			<a href="panelUser.php?mod=panel" class="list-group-item list-group-item-action active">Mis Publicaciones</a>							
      <a href="panelUser.php?mod=panel&amp;op=1" class="list-group-item list-group-item-action">Publicar Propiedad</a>							

			<a href="panelUser.php?mod=panel&op=2" class="list-group-item list-group-item-action">Mi Perfil</a>
			<a href="panelUser.php?mod=panel&op=3" class="list-group-item list-group-item-action">Modificar Contraseña</a>					 
      <a href="https://www.clickcorredores.cl"  target="_blank" class="list-group-item list-group-item-action">Ver Página Web</a>			
			<a href="https://calculadoraipc.ine.cl/" target="_blank" class="list-group-item list-group-item-action ">Calculadora IPC</a>
			<a href="https://servicios.cmfchile.cl/simuladorhipotecario/aplicacion?indice=101.2.1" target="_blank" class="list-group-item list-group-item-action ">Simulador Hipotecario</a>
			<a href="https://homer.sii.cl/" target="_blank" class="list-group-item list-group-item-action ">SII.cl</a>
			
			<a href="desconectar2.php" class="list-group-item list-group-item-action ">Salir</a>

		  </div> 
     
    </div>

    <!-- Main Content -->
    <div class="col-10 col-md-9 col-lg-10">
    <div class="container-fluid mt-5">
  <div class="row">
    <div class="col-12">
    <div class="container">
  <div class="row">
    <div class="col-md-9 text-start">
      <h5><i class="fas fa-user"></i> Hola <?php echo $_SESSION["auth"]["nombre"]; ?>, Bienvenido a tu panel de control</h5>
    </div>
    <div class="col-md-3 text-end">
      <a href="panelUser.php?mod=panel&op=1" class="btn btn-primary"><i class="fas fa-plus"></i> Publicar Propiedad</a>
    </div>
  </div>
</div>
         
    </div>
  </div>
  <?php 
  if(isset($_GET["op"])){ 
    $op=htmlentities($_GET["op"]);
    if($op==1){
      $miOrbis->ingresarPropiedad();
    }else if($op==2){
      $miOrbis->editarCuenta();
    }else if($op==3){
      $miOrbis->modificarPass();
    }else if($op==4){
      if(isset($_GET["idq"])){
        $idq=htmlentities($_GET["idq"]);
        $miOrbis->editarPropiedad($idq);
      }      
    }else if($op==5){
      $idq=htmlentities($_GET["idq"]);
      $miOrbis->editarPropiedad($idq);
    }
    
  }else{
    ?>
  <div class="row mt-4">
    <div class="col-12 col-md-4">
      <div class="card bg-primary text-white">
        <div class="card-body">
          <h5 class="card-title">Propiedades publicadas</h5>
          <p class="card-text"><?php echo $miOrbis->totalProp($idUser);?></p>  
        </div>
      </div>
    </div>
    
    <div class="col-12 col-md-4">
      <div class="card bg-success text-white">
        <div class="card-body">
          <h5 class="card-title">Propiedades en venta</h5>
          <p class="card-text"><?php echo $miOrbis->totalVentas($idUser);?></p>
        </div>
      </div>
    </div>
    
    <div class="col-12 col-md-4">
      <div class="card bg-info text-white">
        <div class="card-body">
          <h5 class="card-title">Propiedades en arriendo</h5>
          <p class="card-text"><?php echo $miOrbis->totalArriendos($idUser);?></p>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div style="margin-top:25px;">
        <?php 
        
        $miOrbis->tablaPropiedades();
        ?>
        </div>
      </div>
    </div>
    <?php 
  }
  
  ?>
  <div class="row">
    <div class="col-md-12">
        <div style="margin-top:20px; margin-bottom:20px;">
          <span>2023 - Desarrollado por programación web chile</span>
        </div>
    </div>
</div>
  </div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function copiarEnlace(idInput) {
        // Obtener el input que contiene el enlace por su ID
        var input = document.getElementById(idInput);

        // Verificamos si el input fue encontrado
        if (input) {
            // Seleccionar el contenido del input
            input.select();
            input.setSelectionRange(0, 99999); // Para dispositivos móviles

            // Copiar al portapapeles
            document.execCommand("copy");

            // Opcional: Puedes mostrar un mensaje de confirmación o cambiar el icono
            alert("Enlace copiado: " + input.value);
        } else {
            alert("No se encontró el campo de texto.");
        }
    }
</script>
 <script>

$(document).ready(function() {
   
	$("#region").change(function(){
      		var region=$("#region").val();	 
      	 	$("#ciudad").empty();
      		 $.ajax({
      			type: "POST",
      			 url: "./include/proceso3.php",
      			 data: "idRegion="+region, 		
      			 dataType: "json",
      			 success: function(datos){					
      				  $('#ciudad').prop('disabled', false);				
      				  $('#ciudad').empty();
                $("#ciudad").append("<option value='0' selected>Seleccione Ciudad</option>");        
      				  var res=datos;		  		  
      				    for(k in res){
      						 for (i in res[k]){
                    
      							 $("#ciudad").append("<option value="+i+">"+res[k][i]+"</option>");   

      					 	}
      				 	}
      
      			 }
      		 });
      	});
      	$("#ciudad").change(function() {
    var ciudad = $("#ciudad").val();
    $("#comuna").empty(); // Limpia las opciones anteriores

    $.ajax({
        type: "POST",
        url: "./include/proceso3a.php",
        data: { idCiudad: ciudad }, // Usa un objeto para los datos
        dataType: "json",
        success: function(datos) {
            $('#comuna').prop('disabled', false); // Habilita el select de comunas
            $('#comuna').empty(); // Limpia las opciones anteriores
            $("#comuna").append("<option value='0' selected>Seleccione Comuna</option>");

            // Recorre el objeto de datos
            $.each(datos, function(idComuna, nombre) {
                $("#comuna").append("<option value='" + idComuna + "'>" + nombre + "</option>");
            });
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error("Error en la petición: " + textStatus, errorThrown);
            alert("Error al cargar las comunas. Intente nuevamente.");
        }
    });
});

      return(false);
});



  </script>
 
  </body>
</html>
 
