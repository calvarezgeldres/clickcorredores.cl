<?php 
session_start();

require_once("./clases/class.controlPropi.php");
$controlProp=new controlProp();
if(isset($_GET["op"])){
  $op=htmlentities($_GET["op"]);
  if($op==1 || $op==5 || $op==9 || $op==10){
    require_once("./clases/class.publicar.php");
    $publicar=new publicar();
  }
}else{
  require_once("./clases/class.publicar.php");
  $publicar=new publicar();
}

?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administración de arriendos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
      .dropdown-item {
        font-size: 14px !important;
      }
      tbody, td, tfoot, th, thead, tr {    
    font-size: 14px !important;
}
      .breadcrumb {
        margin-top:10px;
  background-color: #f8f9fa; /* Color de fondo */
  padding: 5px 5px 10px 10px; /* Espaciado interior */
 
 
}

.breadcrumb .breadcrumb-item a {
  text-decoration: none; /* Quita el subrayado de los enlaces */
  color: #007bff; /* Color del enlace */
 
  font-size:14px !important;
}

.breadcrumb .breadcrumb-item.active {
  color: #555; /* Color del elemento activo */
  font-size:14px !important;
  padding-top:3px !important;
}

      .navbar-expand-lg .navbar-nav .dropdown-menu {
    position: absolute;
    font-size: 14px !important;
}
    .nav-tabs {
        font-size: 14px !important;
    }
    </style>
  </head>
  <body>
  <nav class="navbar navbar-expand-lg bg-light">
  <div class="container">
    <a class="navbar-brand" href="#">AdmProp</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="panel.php">Inicio</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
           Propiedades
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="panel.php?op=1">Listado de Propiedades</a></li>            
            <li><a class="dropdown-item" href="panel.php?op=5">Agregar Propiedad</a></li>      
          </ul>
        </li>
     

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          Arriendos
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="panel.php?op=2">Listado de Arriendos</a></li>  
            <li><a class="dropdown-item" href="panel.php?op=16">Ingresar Nuevo Arriendo</a></li>  
            
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          Personas
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="panel.php?op=3">Propietarios</a></li>
            <li><a class="dropdown-item" href="panel.php?op=12">Agregar Propitario</a></li>      
            <li><a class="dropdown-item" href="panel.php?op=4">Arrendatarios</a></li>            
            <li><a class="dropdown-item" href="panel.php?op=6">Agregar Arrendatarios</a></li>     
            <li><a class="dropdown-item" href="panel.php?op=18">Codeudores</a></li>            
            <li><a class="dropdown-item" href="panel.php?op=17">Agregar Codeudor</a></li>            
            
          </ul>
        </li>
        
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        Acciones
          </a>
          <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="panel.php?op=24">Liquidación pago a propietario</a></li>            
          <li><a class="dropdown-item" href="panel.php?op=25">Liquidación pago a arrendatario</a></li>            
            <li><a class="dropdown-item" href="panel.php?op=23">Generar Liquidación Masiva</a></li>            
          </ul>
        </li>
  

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          Accesos
          </a>
          <ul class="dropdown-menu">
          
          <li><a class="dropdown-item" href="https://calculadoraipc.ine.cl/" target="_blank">Calculadora IPC</a></li>            
          <li><a class="dropdown-item" href="https://servicios.cmfchile.cl/simuladorhipotecario/aplicacion?indice=101.2.1" target="_blank">Simulador de Credito Hipotecario</a></li>            
          <li><a class="dropdown-item" href="https://www.clickcorredores.cl" target="_blank">Click Corredores</a></li>            
          <li><a class="dropdown-item" href="https://www.gmail.com" target="_blank">Gmail</a></li>            
            <li><a class="dropdown-item" href="https://www.google.com/maps/place/Santiago,+Regi%C3%B3n+Metropolitana/@-33.4718999,-70.9100253,10z/data=!3m1!4b1!4m5!3m4!1s0x9662c5410425af2f:0x8475d53c400f0931!8m2!3d-33.4488897!4d-70.6692655?hl=es-CL" target="_blank">GoogleMap</a></li>            
            <li><a class="dropdown-item" href="https://homer.sii.cl/" target="_blank">SII</a></li>            
            <li><a class="dropdown-item" href="https://www.google.com/search?client=firefox-b-d&q=calculadora+online" target="_blank">Calculadora OnLine</a></li>            
            
          </ul>
        </li>
  


        <li class="nav-item position-relative " style="margin-left:50px; margin-right:20px;">
            <a class="nav-link active" aria-current="page" href="#">
            <i class="fas fa-bell"></i>
              <span class="badge position-absolute top-10 start-100 translate-middle p-1 bg-danger border border-light rounded-circle" style="font-size: 12px; transform: translate(-50%, -50%);">10</span>
            </a>
        </li>

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="fas fa-home"></i>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="panel.php?op=5">Agregar Propiedad</a></li>
            <li><a class="dropdown-item" href="panel.php?op=6">Agregar Arrendatario</a></li>
            <li><a class="dropdown-item" href="panel.php?op=16">Agregar Nuevo Arriendo</a></li>
            
          </ul>
        </li>

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" style="font-size: 14px !important;" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="fas fa-user"></i> Corredor de propiedades
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="#">contacto@progwebchile.cl</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="panel.php?op=7">Usuarios</a></li>
            <li><a class="dropdown-item" href="panel.php?op=8">Configuraciones</a></li>
            <li><a class="dropdown-item" href="panel.php?op=9">Perfil</a></li>
            <li><a class="dropdown-item" href="panel.php?op=10">Seguridad y Contraseña</a></li>
            <li><a class="dropdown-item" href="panel.php?op=11">Acerca de</a></li>
            <li><a class="dropdown-item" href="desconectarAgente.php">Cerrar Sesión</a></li>
          </ul>
        </li>

        
      </ul>
    
</div>

    </div>

  </div>
  
</nav>



<div class="container">


<?php 

if(!isset($_GET["op"])){
?>
  <div class="row">
    <div class="col-md-12">
      <div style="margin-top:10px;">
      <?php 
      $controlProp->indicador();
      ?>
      </div>
     
    </div>
    
  </div>
  <?php 
}else{
  $controlProp->breadcrumb();
}
?>
  <div class="row" style="margin-top:20px;">
    <div class="col-md-12">
    <div  >
    <?php 
      if(isset($_GET["op"])){
        $op=htmlentities($_GET["op"]);
        if($op==1){
            $publicar->tablaPropiedades();
        }else if($op==2){
          $controlProp->tabla_mm_arriendos();     
        }else if($op==3){
          $controlProp->tabla_mm_propietarios();
        }else if($op==4){
          $controlProp->tabla_mm_arrendatario();
        }else if($op==5){         
          $publicar->ingresarPropiedad();
        }else if($op==6){
          $controlProp->ingresar_mm_arrendatario();
        }else if($op==7){
          echo "Agregar lista de usuarios";
        }else if($op==8){
          $controlProp->modificar_mm_configuracion();
        }else if($op==9){
          $publicar->editarCuenta();
        }else if($op==10){
          $publicar->modificarPass();
        }else if($op==11){
          $controlProp->acercaDe();
        }else if($op==12){
          $controlProp->ingresar_mm_propietarios();
        }else if($op==13){          
          if(isset($_GET["act"])){
            $controlProp-> modificar_mm_cuentaBancaria();
          }else{
            $controlProp-> ingresar_mm_cuentaBancaria();
          }          
        }else if($op==14){
          $controlProp->fichaPropietario();
        }else if($op==15){
          $controlProp->fichaPropiedad();
        }else if($op==16){
          $controlProp->ingresar_mm_arriendos();
        }else if($op==17){
          $controlProp->ingresar_mm_codeudor();
        }else if($op==18){
          $controlProp->tabla_mm_codeudor();
        }else if($op==19){
          $controlProp->fichaCodeudor();
        }else if($op==20){
          $controlProp->fichaArrendatario();
        }else if($op==21){
          // propietario
          if(isset($_GET["act"]) && $_GET["act"]=="edit"){
            if(isset($_GET["idPropi"])){
              $idPropi=htmlentities($_GET["idPropi"]);
              $controlProp->modificar_mm_cuentaBancaria($idPropi);
            }            
          }else{
            $controlProp->ingresar_mm_cuentaBancaria(0);
          }
        
          
        }else if($op==22){
          // propietario
          $controlProp->ingresarGastos();
        }else if($op==255){
             // arriendo
           // $controlProp-> modificar_mm_cuentaBancaria();

           if(isset($_GET["act"]) && $_GET["act"]==1){
            $controlProp-> ingresar_mm_cuentaBancaria(1);    
           }else{
            $controlProp-> modificar_mm_cuentaBancaria(0);    
           }        
        
          }else if($op==256){
            // codeudor
          if(isset($_GET["act"]) && $_GET["act"]=="edit"){
            $controlProp-> modificar_mm_cuentaBancaria(0);    
          }else{
            $controlProp-> ingresar_mm_cuentaBancaria(2);               
          }        
        }else if($op==24){
          echo "Modulo en proceso de instalación";
        }else if($op==25){
          echo "Modulo en proceso de instalación";
        }else if($op==23){
          // propietario
          $controlProp->generarLiquidacion();
       
        }else{
          $publicar->tablaPropiedades();
        }
      
      
          
      }else{
        $controlProp->clasificaArriendos();
      }
      
      ?>

    </div>
    </div></div>

    <section id="piePagina" name="piePagina">
      <div class="row">
        <div class="col-md-12">
          <div style="padding-bottom:15px;">
            <div><hr/></div>
            <div align="center">Administración de propiedades cmsAdm v1.0 - <a href="https://www.programacionwebchile.cl" style="font-size:14px;" target="_blank">Programación Web Chile</a></div>
          </div>
        </div>
      </div>
    </section>
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
          $('#comuna').prop('disabled', false);				
         $('#comuna').empty();
         $("#ciudad").append("<option value='0' selected='selected'>Selecione ciudad</option>");       
                                   
                                 
         var res=datos;
         for(k in res){
            for (i in res[k]){
              $("#ciudad").append("<option value="+i+">"+res[k][i]+"</option>");                    
            }
          }
        }
    });
  });
 $("#ciudad").change(function(){
   
   var ciudad=$("#ciudad").val();
    $("#comuna").empty();
    
    $.ajax({
     type: "POST",
     url: "./include/proceso3a.php",
     data: "idCiudad="+ciudad, 	
     dataType: "json",
     success: function(datos){	
        
        $('#comuna').prop('disabled', false);				
       $('#comuna').empty();
       $("#comuna").append("<option value='0' selected='selected'>Seleccione Comuna</option>");  
       var res=datos;
               
       for(k in res){
         for (i in res[k]){
             $("#comuna").append("<option value="+i+">"+res[k][i]+"</option>");                    
         }
        }
     }
   });
  });
  
  
      $("div a#p").click(function(){
       var alt="./upload/"+$(this).attr("alt");				 
        $("#res").html("<a href='"+alt+"' class='fancybox' id='gallery1'><img src=\'"+alt+"\' width=\"300\" height=\"226\" /></a>");		 
       return(false);
     });
     // $(".fancybox").fancybox();
      return(false);
   });
</script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
  
  </body>
</html>