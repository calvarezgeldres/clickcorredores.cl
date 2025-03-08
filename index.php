<?php 
ob_start();
?><!doctype html>
<html lang="es">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#000" />
    <?php 
      if(isset($_GET["mod"]) || isset($_GET["idProp"])){
         echo '<script>var userLogged=false;</script>';
         require_once("./clases/class.detOrbis.php");
         $detOrbis=new detOrbis();         
      } 
        require_once("./clases/class.orbisPlus23.php");
        $miOrbis=new orbis();
        $d=$miOrbis->datosPag();        
        $miOrbis->tiempoInicio();    
        $miOrbis->registrarVisita();
    ?>
 <title><?php echo htmlspecialchars($d["titulo"], ENT_QUOTES, 'UTF-8'); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($d["des"], ENT_QUOTES, 'UTF-8'); ?>" />
    <meta name="keywords" content="<?php echo htmlspecialchars($d["meta"], ENT_QUOTES, 'UTF-8'); ?>" />
    <meta property="fb:app_id" content="106947263291884" />
      <?php  isset($_GET["mod"]) || $_GET["mod"] == "det" ? $miOrbis->red(true) : $miOrbis->red(false);  ?>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;400&display=swap" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    
    <link rel="shortcut icon" href="https://clickcorredores.cl/favicon.png" type="image/x-icon" />
    <link rel="icon" href="https://clickcorredores.cl/favicon.png" type="image/x-icon" />   
    <link rel="shortcut icon" href="https://clickcorredores.cl/favicon.png"/>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-3E0PHD41QX"></script>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<div id="fb-root"></div>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/es_LA/sdk.js#xfbml=1&version=v20.0&appId=106947263291884" nonce="df8nPw9k"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-3E0PHD41QX');
</script> 
    <style> 
   
.btn-outline-custom {
    border-color: #33c240;
    color: #33c240;
}
    .navbar-expand-lg .navbar-nav .nav-link {
    padding-right: 1rem !important;
    padding-left: 1rem !important;
}
    .navbar-nav .nav-link {
    color: #ffffff !important;
    font-size: 16px !important;
    font-weight: 400 !important;
}
    
    .form-label{ font-size:16px;}
    a.red:link,a.red:visited,a.red:active{
      color:black;
      text-decoration:none;
    }
    a.red:hover{
      color:white;
      text-decoration:none;
    }
         .contact-list {
      list-style: none;
      padding: 0;
      text-align: right;
    }
    .contact-list li {
      display: inline-block;
      margin-right: 20px;
    }
    .contact-list a {
      color: #333;
      text-decoration: none;
      display: flex;
      align-items: center;
    }
    .contact-list .info {
      margin-left: 10px;
    }
    .contact-list .title {
      font-weight: bold;
      text-align: center;
    }
    .contact-list .value {
      color: #888;
      text-align: center;
    }
    .contact-list .icon {
      font-size: 24px;
      margin-right: 10px;
    }
    
    .bg-light {
  background-color: #fff !important;
}
    
    .navbar {  
  padding-top: .0rem !important;
  padding-bottom: .0rem !important;
}
    .btn-primary2 {
      color: #fff;
      background-color: #303031 !important;
      border-color: #303031 !important;
    }
    
    
 
  a.m3:link, a.m3:active, a.m3:visited, a.m3:hover {
  color: #000 !important;
  text-decoration: none;
  font-size: 14px;
}
    
a.m:link, a.m:active, a.m:visited, a.m:hover {
  color: white !important;
  text-decoration: none;
  font-size: 14px;
}
    .form-select-lg {
  padding-top: .5rem;
  padding-bottom: .5rem;
  padding-left: 1rem;
  font-size: 1.25rem; 
  background-color: #d2cfcf !important;
}

 
    hr {
  margin: 1rem 0;
  color: inherit;
  
  border: 0;
  opacity: .25;
  background-color: #aaa !important;
}
    a.mt1:link,a.mt1:active,a.mt1:hover,a.mt1:visited{ font-size:16px; text-decoration:none; color:black;}

    
    .container, .container-lg, .container-md, .container-sm, .container-xl {
  max-width: 1120px !important;
} 

    
    .accordion-button:not(.collapsed) {
  color: #0c63e4;
  background-color: #fbfbfb !important;
  box-shadow: inset 0 -1px 0 rgba(0,0,0,.125);
}
.btn-light1 {
  color: #000;
  background-color: #f8f9fa;
  border-color: #f8f9fa;
  border: 1px solid #ced4da !important;
}

@media (max-width: 979px){
  .vCover{
   height:600px; 
   object-fit: cover;
 }
  #desk{display:block;}  
  #cel{display:none;}  
 
  .w-100 {
  width: 100% !important;
  height: 300px !important;
}
  .navbar-brand {
  padding-top: .3125rem;
  padding-bottom: .3125rem;
  margin-right: 1rem;
  font-size: 1.25rem;
  text-decoration: none;
  white-space: nowrap;
  width: 53% !important;
}
}
@media (max-width: 439px){  
  .w-100 {
  width: 100% !important;
  height: 230px !important;
}
  #desk{display:none;}
  #cel{display:block;}
  .vCover{
   height:600px; 
   object-fit: cover;
 }
  .navbar-brand {
  padding-top: .3125rem;
  padding-bottom: .3125rem;
  margin-right: 1rem;
  font-size: 1.25rem;
  text-decoration: none;
  white-space: nowrap;
  width: 53% !important;
}
} 
@media (min-width: 992px){ 
  #desk{display:block;}  
  #cel{display:none;}  
  .vCover{
   height:600px; 
   object-fit: cover;
 }
  .navbar-brand {
  padding-top: .3125rem;
  padding-bottom: .3125rem;
  margin-right: 1rem;
  font-size: 1.25rem;
  text-decoration: none;
  white-space: nowrap;
  width: 53% !important;
}
}
@media (min-width: 1200px){
  #desk{display:block;}  
  #cel{display:none;}  
 .vCover{
   height:600px; 
   object-fit: cover;
 }
  .navbar-brand {
  padding-top: .3125rem;
  padding-bottom: .3125rem;
  margin-right: 1rem;
  font-size: 1.25rem;
  text-decoration: none;
  white-space: nowrap;
  width:22% !important;
}
}
    

.card {
  border: 0;
  -webkit-box-shadow: 0 10px 15px -3px rgba(0,0,0,.07),0 4px 6px -2px rgba(0,0,0,.05);
  box-shadow: 0 10px 15px -3px rgba(0,0,0,.07),0 4px 6px -2px rgba(0,0,0,.05);
}
				 
				 
.card {
  background-color: #FFFFFF !important;
  
  border: 1px solid #F9F9F9 !important;
  
}
.list-group-item {
  color: #212529;
  background-color: #FFFFFF !important;
}
    .m{color: #FFFFFF !important;}
    .t{font-size:16px; font-family: 'Open Sans', sans-serif !important; font-weight:bold;text-decoration:none !important; color:#4D4D4D !important; line-height:16px;}  
    div,b,p{ font-family: 'Open Sans', sans-serif !important; font-size: !important;}
    ul,li{font-family: 'Roboto', sans-serif; }
        #titulo{ font-size:20px; font-weight:bold;font-family: 'Roboto', sans-serif;}
        
        a.en11:link,a.en11:visited,a.en11:hover,a.en11:active{  font-family: 'Open Sans', sans-serif !important; font-size:14px !important; color:#FBFBFB !important; text-decoration:none;}
        body{
           font-family: 'Roboto', sans-serif;
            font-size:20px !important;
        }
        .dropdown-item {  
            color:  #080808 !important;  
        }
        .dropdown-menu {
                background-color: #D1D1D1 !important;
        }
        body{background-color:#EDEDED !important;}
    </style>
   	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">    
   
 <style>
  #menu {
    color: #ffffff !important;
}
  .btn-info, .btn-info:hover {
    color: #fff !important;
    background-color: #dc3545 !important;
    border-color: #dc3545 !important;
}
  .navbar {
  padding-top: .0rem !important;
  padding-bottom: .0rem !important;
  padding: 12px !important;
}
  .bg-light {
  box-shadow: 0 10px 15px -3px rgba(0,0,0,.07),0 4px 6px -2px rgba(0,0,0,.35);
}
  .box-estado {
  overflow: hidden !important;
  position: absolute !important;
  width: 60% !important;
  height: 30px !important;
  float: right !important;
  top: 195px !important;
  right: 15px !important;
  text-align: left;
  left: 8px;
} 
.btn-whatsapp {
  display: block;
  width: 50px;
  height: 50px;
  color: #fff;
  position: fixed;
  right: 15px;
  bottom: 20px;
  border-radius: 50%;
  line-height: 80px;
  text-align: left;
  z-index: 999;
}
   .btn-discado{
    display: block;
  width: 50px;
  height: 50px;
  color: #fff;
  position: fixed;
  right: 15px;
  bottom: 90px;
  border-radius: 50%;
  line-height: 80px;
  text-align: left;
  z-index: 999;
      }

      .btn-correo{
    display: block;
  width: 70px;
  height: 70px;
  color: #fff;
  position: fixed;
  right: 15px;
  bottom: 160px;
  border-radius: 50%;
  line-height: 80px;
  text-align: left;
  z-index: 999;
      }

      .visitanos {
    margin: 0 auto;
    padding: 0px !important;
    border: 0;
    overflow: hidden;
    clear: both;
    width: 100%;
}

.visitanos {
  background: #333 url("./data1/images/a.jpg") top center no-repeat;
        background-position-x: center;
        background-position-y: top;
        background-repeat: no-repeat;
        background-attachment: scroll;
        background-size: auto;
    background-position-x: center;
    background-position-y: top;
    background-repeat: no-repeat;
    background-attachment: scroll;
    background-size: auto;
    background-position-x: center;
    background-position-y: top;
    background-repeat: no-repeat;
    background-attachment: scroll;
    background-size: auto;
    background-attachment: fixed;
    background-position: center;
    background-repeat: no-repeat;
    background-size: cover;
  
}

.bg-danger {
  background-color: #334255 !important;
  color: white !important;
}
body {top: 0 !important;}
.goog-te-banner-frame { display:none !important; }      
.elementor-icon-list-text,li{
  font-size:14px;
}

   </style>
      <?php if(isset($_GET["idProp"])){?> <link rel="stylesheet" href="./css/k.css"><?php } ?>
  </head>
  <body>
  

  <nav class="navbar  navbar-nav fixed-top  navbar-expand-lg navbar-expand-lg navbar-dark bg-dark" style="background-color:#0059b0 !important;">
         <div class="container-fluid">
         <a class="navbar-brand" href="index.php">
  <img src="logoClick.png" style="width:100%; max-width:100%;"/>
  </a>   

            <button class="navbar-toggler" style="margin-right:20px;" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
               <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                  <li class="nav-item">
                     <a class="nav-link active" aria-current="page" href="index.php">Inicio</a>
                  </li>
              
                  <li class="nav-item">
                     <a class="nav-link" href="index.php?mod=pag&rd=6">Quienes somos</a>
                  </li>
 
                  <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          Evaluación Comercial
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="evaluacion-comercial-arrendatario.php">Evaluación comercial arrendatario</a></li>
            <li><a class="dropdown-item" href="evaluacion-comercial-aval.php">Evaluación comercial aval</a></li>
    
          </ul>
        </li>

                  <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          Propiedades
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="index.php?m=1">Venta de propiedades</a></li>
            <li><a class="dropdown-item" href="index.php?m=2">Arriendo de propiedades</a></li>
    
          </ul>
        </li>
                  <?php 
                  if(!isset($_SESSION["auth"])){
                    ?>
                  <li class="nav-item">
                     <a class="nav-link" href="index.php?mod=login"><i class="fas fa-user" aria-hidden="true"></i> Ingresar</a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link" href="index.php?mod=crearCuenta">Registrarse</a>
                  </li> 
                  <li class="nav-item">
                     <a class="nav-link btn btn-danger btn-sm" href="index.php?mod=publicar" style="padding-top: 3px !important;padding-bottom: 3px !important;font-size:14px; color:white !important;"><i class="far fa-check-circle" aria-hidden="true"></i> Publica Gratis</a>
                  </li>
                    <?php 
                  }else{
                    if($_SESSION["auth"]["usuario"]!="admin"){
                      ?>
                      <li class="nav-item dropdown">
          
          <div class="dropdown">
                     <a class="btn btn-light dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="true">
                     <i class="fas fa-user" aria-hidden="true"></i>&nbsp;&nbsp; <?php echo $_SESSION["auth"]["email"];?>                   </a>
                     <ul class="dropdown-menu" data-bs-popper="static">
                     <li><a class="dropdown-item" href="https://www.clickcorredores.cl/panelUser.php?mod=panel">Mis Publicaciones</a></li>
                        <li><a class="dropdown-item" href="https://www.clickcorredores.cl/panelUser.php?mod=panel&amp;op=2">Mi Perfil</a></li>
                        <li><a class="dropdown-item" href="https://www.clickcorredores.cl/panelUser.php?mod=panel&amp;op=3">Modificar Contraseña</a></li>                         
                        <li><a class="dropdown-item" href="https://www.clickcorredores.cl/desconectar2.php">Salir</a></li>
                     </ul>
                  </div>
        </li>
                      <?php 
                    }
                  }
                  ?>
               </ul>
            </div>
         </div>
      </nav>

 

<?php
            if(!isset($_GET["idProp"]) && !isset($_GET["mod"])){
               if(!isset($_GET["action1"]) && !isset($_GET["m"])){
                  ?>
<div id="carouselExampleIndicators" class="carousel slide position-relative pointer-event" data-bs-ride="carousel">
  <div class="carousel-indicators">
    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="" aria-label="Slide 1"></button>
    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2" class="active" aria-current="true"></button>    
    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3" class=""></button>    
  </div>
  <div class="carousel-inner">
    <div class="carousel-item">
      <img src="./data1/images/a.jpg" class="d-block w-100 vCover" alt="...">
    </div>
    <div class="carousel-item active">
      <img src="./data1/images/d.jpg" class="d-block w-100 vCover" alt="...">
      <div class="carousel-caption d-none d-md-block"></div>
    </div>
    <div class="carousel-item">
      <img src="./data1/images/b.jpg" class="d-block w-100 vCover" alt="..." >
      <div class="carousel-caption d-none d-md-block"></div>
    </div>
    <div id="desk" name="desk">
    <div class=" position-absolute top-0 end-0 p-2" style="z-index:2;top: 100px !important;">
      <a href="https://clickcorredores.cl/index.php?mod=contacto" target="_blank" role="button" class="btn btn-warning btn-mb">
        <i class="fas fa-info-circle me-2" aria-hidden="true"></i>
        <strong>Necesitas un corredor</strong>
               </a>
    </div>
    <div class="position-absolute top-0 end-0 p-2" style="top: 150px !important;">
                <div style="background-color:white !important;"><a href="https://www.sognopropiedades.com/" target="_blank"><img src="sognopro.png" style="width:160px;"/></a></div>
    </div>
               </div>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>




<?php
               }
              } 
              if(isset($_GET["action1"])){
                echo "<div id='desk' name='desk' style='margin-top:100px;padding-top:5px; padding-bottom:5px;' class='card'>";
                
                $miOrbis->buscadorHorizontal();
                echo "</div>";
              }
            ?>
<section name="iconos" style="background-color:white;"> 
<div id="desk" name="desk">
<?php
echo '<div style=" color: white;
position: relative;
top: -350px;" align="center"><h1 style="color:white;text-shadow: 2px 2px 2px #000;">Encuentra la propiedad de tus sueños </h1></div>';
if(!isset($_GET["mod"]) && !isset($_GET["m"])){
  echo "<div style='background-color: #ffffffa3;
  position: relative;
  top: -300px;
  margin-right: 100px;
  margin-left: 100px;
  padding-top: 10px;
  padding-bottom: 10px;'>";  
  $miOrbis->buscadorHorizontal();
  echo "</div>";
}
echo '</div>';
 
if(!isset($_GET["m"]) &&  !isset($_GET["mod"])){ 
?>

<div id="cel" name="cel" <?php 
if(isset($_GET["action1"])){
  echo ' style="margin-top:80px;" ';
}
?>> 
<nav class="navbar navbar-dark bg-danger">
  <div class="container-fluid">
    <a class="navbar-brand" href="#"><i class="fas fa-search"></i> Buscador</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent1" aria-controls="navbarSupportedContent1" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent1">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
      <?php $miOrbis->buscador2(); ?>
      </ul>
      
    </div>
  </div>
</nav>
</div>
<?php 
if(isset($_GET["action1"])){
  echo "<section style='position: relative; top: -100px;' >";
}else{
  echo "<section >";
}
?>

<div class="container">
<div class="row">
    <div class="col-md-12">
      
    <?php
    if(isset($_GET["action1"])){
      if($miOrbis->detDisp()=="movil"){
        echo '<div>
        <div align="center" style="margin-top:120px;font-size:20px;margin-bottom:0px;">RESULTADO DE LA BUSQUEDA</div>        
        </div>';
      }else{
        echo '<div>
              <div align="center" style="margin-top:0px;font-size:20px;margin-bottom:0px;">RESULTADO DE LA BUSQUEDA</div>        
              </div>';
      }
      
    }else{
      if($miOrbis->detDisp()=="movil"){
        echo '<div>
              <div align="center" style="margin-top:20px;font-size:20px;margin-bottom:20px;">PROPIEDADES DESTACADAS</div>        
              </div>';
    }else{
      echo '<div style="margin-top: -70px; position: relative;">
      <div align="center" style="font-size:25px;margin-bottom:50px;">PROPIEDADES DESTACADAS</div>        
      </div>';
    }
    }
      
    ?>
  
      
    </div>
</div>
 
 
<?php  
  $miOrbis->desplegarProp4(); 
?> 
 

 
  
 
</section>
<?php
}else if (isset($_GET["mod"])){
  
?>

<section id="paginas">
<div class="container">
  <div class="row">
      <div class="col-md-12">
      <?php            
      if($_GET["mod"]=="blog"){
        $id=htmlentities($_GET["id"]);
?>
<div class="container">
        
      <div class="row">
        <div class="col-md-12">
            <?php echo $miOrbis->leerBlog($id); ?>
        </div>
      </div>
</div>
<?php
      }else  if($_GET["mod"]=="det"){
      ?>
        <section>
                  <div class="container">

                  <div class="row">

                    <div class="col-md-8">
                     <?php 
                      $detOrbis->detProp($id); 
                      if(isset($_GET["idProp"])){ $id=htmlentities($_GET["idProp"]);}                         
                      $detOrbis->detMoz($id);                      
                     ?>
                        <?php  $detOrbis->mapa1($id); ?>
                    </div>
                    <div class="col-md-4">
                    <div style="margin-top:60px;"> <?php  $detOrbis->contacto2($id); ?> </div>
                        <div> <?php  $detOrbis->detalles($id); ?> </div>
                     
                    </div>
                    
                  </div>

                   


                     
                  </div>
              </section>
      <?php
        }else  if($_GET["mod"]=="crearCuenta"){ 
          header("location:registrar.php");
          exit;
        }else if($_GET["mod"]=="rec"){
          header("location:recuperarContrasena.php");
          exit;
        }else  if($_GET["mod"]=="login"){ 
          header("location:loginUser.php");
          exit;
        }else  if($_GET["mod"]=="publicar"){ 
          header("location:publicar.php");
          exit;
      }else  if($_GET["mod"]=="contacto"){

        ?>
        <div class="container" style="margin-top:70px;">
          <div class="row">
              <div class="col-md-6">
         <div style="margin-top:30px;"><h4>Datos de Contacto</h4></div>
         <div><hr/></div>
          
          <div style="font-size:16px;margin-top:20px;"><i class="far fa-envelope"></i>&nbsp;contacto@clickcorredores.cl</div>
          <div style="font-size:16px;margin-top:20px;"><i class="fas fa-phone"></i>&nbsp;<?php echo $d["telefono"];?></div>
          <div style="margin-top:20px;font-size:16px;"><i class="fas fa-mobile"></i>&nbsp;<?php echo $d["telefono1"];?></div>
          <div style="margin-top:20px;font-size:16px;"><i class="fas fa-map-marker-alt"></i> Dr. Manuel Barros Borgoño 71, of 1105, 8320000 Providencia, Región Metropolitana</div>
          <div style='margin-top:20px;font-size:16px;'>Lee nuestro codigo QR y contactenos</div>
          <div><img src='clickcorredores3.png' style='width:50%;'/></div>
            </div>
              <div class="col-md-6">
              <?php
            $detOrbis->contacto222();
            ?>

              </div>
          </div>
        </div>
        <?php

        
      }else{
        if(isset($_GET["rd"])){
          $rd=htmlentities($_GET["rd"]);
          echo "<div style='margin-top:50px;padding-left:20px;padding-right:20px;'>";
          echo "<div><h4>";
          $detOrbis->leerTitulo($rd);
          echo "</h4></div>";
          echo "<div class='row' style='margin-top:30px;'>";
          echo "<div class='col-md-8'>";
          echo "<div>";
          $detOrbis->leerContenido($rd);
          echo "</div>";                           
          echo "</div>";
          echo "<div class='col-md-4'>";
          $detOrbis->leerImagen($rd);
          echo "</div>";
          echo "</div>";
          
          echo "</div>";
       
     }
      }        
      ?>
      </div>
  </div>
</div>
</section>
<?php
}else{
  ?>
<section id="operacion">

<div class="container">
<div class="row" style="padding-top:70px;">
    <div class="col-md-12">
      <div style="font-size:16px;">PROPIEDADES EN<br/>
        <span style="padding:0px; margin:0px;font-size:2.1em !important;font-weight:bold;color:#303031;"><?php
        
        if(isset($_GET["m"]) && $_GET["m"]==1){
          echo "VENTA";
        }else{
          echo "ARRIENDO";
        }
        ?></span></div>        
        <div>
        <div style="position:relative;float:left;background: #303031; top: -1px;left: 0;bottom: 0;right: 0;width: 10%;height: 3px;z-index: 1;margin-bottom:30px;">&nbsp;</div>
        </div>
    </div>
</div>
<div class="row">
<?php  
if(isset($_GET["m"])){
  $m=htmlentities($_GET["m"]);
}
if($m==1){
  $miOrbis->desplegarProp4(1); 
}else{
  $miOrbis->desplegarProp4(2); 
}
  
 ?>    
 
</div>
</div>
</section>
<?php
}
?>
  
  <section id="cel" name="cel">
  <div align="center">
      <a href="https://clickcorredores.cl/index.php?mod=contacto" target="_blank" role="button" class="btn btn-warning btn-mb" style="margin-top:20px;width:90%;" align="center">
        <i class="fas fa-info-circle me-2" aria-hidden="true"></i>
        <strong>Necesitas un corredor</strong>
</a>
    </div>
    <div>
                <div align="center" style="margin-top:10px;background-color:white !important;"><a href="https://www.sognopropiedades.com/" target="_blank"><img src="sognopro.png" style="width:70%;"/></a></div>
    </div>
</section>
<section id="ayuda">
<div class="visitanos" style="margin-top:30px;">
<div style="background-color:#0f0f0f78; padding-top: 40px;padding-bottom: 40px;">
    <div class="container">
    <div class="row">
                           <div class="col-md-12" align="center"><h2 style="font-weight:bold;color:white;">Confíanos tu propiedad</h2></div>
                            <div align="center" style="font-size:18px;color:white;">
                            Si quiere que busquemos el hogar de sus sueños, no dude en ponerse en contacto con nosotros.
                          </div>
                                
                                <div class="col-lg-12" style="margin-top:40px;" align="center">
                                    <a href="index.php?mod=contacto" role="button" class="btn btn-success btn-lg" style="background-color: #dc3545;border-color: #dc3545;
" id="contacto1" name="contacto1"><i class="far fa-envelope" aria-hidden="true"></i> <span style="font-size:15px;">CONTACTO</span></a>
                                    

                                    
                                </div>                             
                         </div>
    </div>
</div>
  </div>
</section>
 
<section id="piePagina" style="background-color:#0059b0;padding-top:20px;">
  <div class="container">
      <div class="row">
          <div class="col-md-4">
            <div><img src="logoClick.png" style="width:70%;max-width:100%;margin-bottom:15px;"/></div>
            <div>
            <div style="margin-top:10px;color:white;font-size:14px;margin-bottom:5px;"><i class="fa fa-map-marker"></i> <?php echo utf8_decode($d["direccion"]);?></div>
            </div>
          
          </div>
          <div class="col-md-2">
            <div><h5 style="color:white;">Enlaces</h3></div>
            <div>
                <ul>
                  <li id="menu"><a href="index.php" class="m">Inicio</a></li>
                  <li id="menu"><a href="index.php?mod=pag&rd=6" class="m">Nosotros</a></li>
                 
                  <li id="menu"><a href="index.php?mod=contacto" class="m">Contacto</a></li>
                </ul>

            </div>
            
             
          </div>
          <div class="col-md-3">
            <div><h5 style="color:white;">Servicios</h3></div>
            <div>
                <ul>
                  <li id="menu"><a href="index.php?mod=pag&rd=21" class="m">Servicios</a></li>
                  <li id="menu"><a href="index.php?m=1" class="m">Venta de propiedades</a></li>
                  <li id="menu"><a href="index.php?m=2" class="m">Arriendo de propiedades</a></li>
                  
                 
                </ul>

            </div>
                  </div>
     
          <div class="col-md-3">
          <div><h5 style="color:white;">Contacto</h3></div>

          <div>
              <div style="color:white;font-size:14px;"><i class="fas fa-phone"></i> 	<?php echo "<a href='tel:".$d["telefono"]."' class='m'>".$d["telefono"]."</a>";?></div>
              <div style="color:white;font-size:14px;margin-top:10px; margin-bottom:10px;"><i class="fas fa-phone"></i>	<?php echo "<a href='tel:".$d["telefono1"]."' class='m'>".$d["telefono1"]."</a>";?></div>
              <div style="color:white;font-size:14px;"><i class="fas fa-envelope"></i>	<?php echo "<a href='mailto:".$d["email"]."' class='m'>".$d["email"]."</a>";?></div>
              <div style="margin-top:10px;color:white;font-size:14px;"><i class="fas fa-globe"></i> www.clickcorredores.cl</div>
           
          </div>
          </div>
          
      </div>
      <div style="margin-top:5px;padding-top:13px;padding-bottom:6px;" align="center"><?php 
         //   $miOrbis->indicadoresEconomicos2();
            ?></div>
  </div>
</section>
<div id="copyright" style="padding:10px;background-color:#004991;">
    <div class="container">
        <div class="row">
            <div class="col-md-12" align="center"> 
            <div  align="center" style="font-size:14px;color:white !important;">&copy; 2023 <b>Portal Inmobiliario Click Corredores</b> Todos los Derechos Reservados</div>
           
            <div align="center" style="font-size:12px;margin-top:5px;color:#f3f3f3;"><?php $miOrbis->tiempoFin();?></div>
            
            
            </div>
        </div>
    </div>
</div>
<div class="btn-whatsapp" align="left">
      <a href="https://api.whatsapp.com/send?phone=56982580916" target="_blank">
      <img data-lazyloaded="1" data-placeholder-resp="150x150" src="w4.png" data-src="w4.png" alt="WhatsApp" title="WhatsApp" class="img-icon ccw-analytics desktop entered litespeed-loaded" data-ll-status="loaded" width="50" height="50">
      </a>
      </div>
      <div class="btn-discado" align="left">
      <a href="tel:+56982580916" target="_blank">
      <img data-lazyloaded="1" data-placeholder-resp="150x150" src="w2.png" data-src="w2.png" alt="Discado" title="Discado" class="img-icon ccw-analytics desktop entered litespeed-loaded" data-ll-status="loaded" width="50" height="50">
      </a>
      </div>
 
      <?php
    if(isset($_GET["idProp"])){
    ?>
    <script src="https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyDNpX_3El_MOS7bQnn3jPbDGXiPPnKIiV0"></script>
    <?php
    }

    
    ?>
 



   
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function(){
      $("#boton1").click(function(){
                  var con=$(this).val();
                  $("#msg").html(con);
                  return(false);         
                });
                $("#boton2").click(function(){
                  var con=$(this).val();
                  $("#msg").html(con);
                  return(false);         
                });
                $("#boton3").click(function(){
                  var con=$(this).val();
                  $("#msg").html(con);
                  return(false);         
                });
                $("#boton4").click(function(){
                  var con=$(this).val();
                  $("#msg").html(con);
                  return(false);         
                });
      $("#region33").change(function(){
        
 var region=$("#region33").val();
   $("#ciudad33").empty();
   $("#comuna33").empty();
$.ajax({
     type: "POST",
     url: "./include/proceso3.php",
     data: "idRegion="+region, 	
     dataType: "json",
     success: function(datos){	
 
       $('#ciudad33').prop('disabled', false);				
      $('#ciudad33').empty();
     $('#comuna33').prop('disabled', false);				
      $('#comuna33').empty();
     
      $("#ciudad33").append("<option value='0' selected='selected'>Provincia</option>");                           
     //$("#comuna33").append("<option value='0' selected='selected'>Comuna</option>");                           
      var res=datos;
      for(k in res){
         for (i in res[k]){
           $("#ciudad33").append("<option value="+i+">"+res[k][i]+"</option>");                    
         }
       }
     }
 });
});
$("#ciudad33").change(function(){

var ciudad=$("#ciudad33").val();
 $("#comuna33").empty();
 $("#comuna33").append("<option value='0' selected='selected'>Seleccione Comuna</option>");  
 $.ajax({
  type: "POST",
  url: "./include/proceso3a.php",
  data: "idCiudad="+ciudad, 	
  dataType: "json",
  success: function(datos){	 
    
     $('#comuna33').prop('disabled', false);				
    $('#comuna33').empty();
    
    var res=datos;
            
    for(k in res){
      for (i in res[k]){
          $("#comuna33").append("<option value="+i+">"+res[k][i]+"</option>");                    
      }
     }
  }
});
});





$("#region").change(function(){
 
 var region=$("#region").val();
   $("#ciudad").empty();
   $("#comuna").empty();
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
     
     //$("#ciudad").append("<option value='0' selected='selected'>Provincia</option>");                           
     //$("#comuna").append("<option value='0' selected='selected'>Comuna</option>");                           
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
 $("#comuna").append("<option value='0' selected='selected'>Seleccione Comuna</option>");  
 $.ajax({
  type: "POST",
  url: "./include/proceso3a.php",
  data: "idCiudad="+ciudad, 	
  dataType: "json",
  success: function(datos){	     
     $('#comuna').prop('disabled', false);				
    $('#comuna').empty();
    
    var res=datos;
            
    for(k in res){
      for (i in res[k]){
          $("#comuna").append("<option value="+i+">"+res[k][i]+"</option>");                    
      }
     }
  }
});
});


      return(false);
    });
  </script>
   <?php
    
    if(isset($_GET["idProp"])){
  
       $cor=$miOrbis->mostrarCordenadas();	
       
       echo '
       
       <script>
       $(document).ready(function(){
        
          $("#contacto2").click(function(){
            document.location="index.php?mod=contacto";
          });
       });
         function initialize() {
           var latlngPos = new google.maps.LatLng('.$cor.');
           var myOptions = {
         zoom: 15,
         center: latlngPos,
         mapTypeId: google.maps.MapTypeId.ROADMAP
       };
       var map = new google.maps.Map(document.getElementById("detalle-mapa"), myOptions);
       var markerPos = new google.maps.LatLng('.$cor.');
       var marker = new google.maps.Marker({
         position: markerPos,
         map: map,
         title: "Calle Ohiggins a pasos de Hospital Regional, Amoblado"
       });
       }
       
       window.onload = function () { initialize() };
       
       </script>	  ';
    }
       ?>
  <script>
           $(document).ready(function(){
                
             var myCarousel = document.querySelector('#carouselExampleIndicators')
              var carousel = new bootstrap.Carousel(myCarousel, {
              interval: 2800,
              wrap: true
          })

              
              return(false);
           });
       </script>
       <?php
       if(isset($_GET["idProp"])){
       ?>
 <script type='text/javascript' src="./js/a.js"></script>
 <script type='text/javascript' src="./js/b.js"></script>
 <?php } ?>
<script type="text/javascript">
         function ejecutar(i){
            document.location.href="index.php?km="+i;	 
         	return(false);
         }
         
         $(document).ready(function(){

         

          $("#boton13").click(function(){
            alert("ok");
          });
            $("#contacto").click(function(){
               document.location="index.php?mod=contacto";

            });
         	$("#buscar").click(function(){
         		var operacion=$("#operacion").val();
         		var tipo=$("#tipo").val();
         		var ciudad=$("#ciudad").val();
         		var orden=$("#orden").val();
         		var codigo=$("#codigo").val();
         		 
         		$("#frm-buscar").submit();
         		 
         		return(false);
         	});
         	$("#en").click(function(){
         		 
         		var nombre=$("#nombre").val();
         		var email=$("#email").val();
         		var telefono=$("#telefono").val();
         		var mensaje=$("#mensaje").val();
         	 
         		if(nombre.length==0){
         			alert("Ingrese su nombre");
         			$("#nombre").focus();
         		}else if(email.length==0){
         			alert("Ingrese su email");
         			$("#email").focus();
         		}else if(telefono.length==0){
         			alert("Ingrese su telefono");
         			$("#telefono").focus();
         		}else if(mensaje.length==0){
         			alert("Ingrese su mensaje");
         			$("#mensaje").focus();
         		}else{
         			alert("submit");
         		}
         		return(false);
         	});
          	$("div a#kt6").click(function(){
          		var foto=$(this).attr("alt");
           
          		$("#res").html("<img src='./load.gif'/>");
          		$("#res").html("<img src=\""+foto+"\"   width=\"342\"  height=\"234\"  >");
          		return(false);
          	});
         	$("#contacto").click(function(){
         		
         		document.location="index.php?mod=contacto";
         	});
         	return(false);
         });
         		 
         	
      </script>

  </body>
</html>