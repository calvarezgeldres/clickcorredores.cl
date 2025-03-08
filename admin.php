<?php

ob_start();
 session_start();
 echo '<div style="display:none;">';
  
 require_once("./clases/subClase/class.cmsPropAdmin.php");
 $miOrbis=new cmsPropAdmin();
 $miOrbis->estilo();
 $miOrbis->tiempoInicio();
 

 echo "</div>";
?>

<!DOCTYPE html>
<html lang="es">
	
<!-- Mirrored from www.dobbspropiedades.cl/ by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 26 Jul 2017 17:01:50 GMT -->
<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=UTF-8" /><!-- /Added by HTTrack -->
<head>
		<title>Panel de administración</title>
						
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="description" content="Dobbs Propiedades, venta y arriendo de propiedades en Concepción. Propiedades en la Octava Región." />
    <meta name="keywords" content="dobbs propiedades, propiedades concepcion, venta propiedades concepcion, arriendo propiedades concepcion, arriendo casa concepcion, venta casa concepcion, arriendo departamento concepcion, venta departamento concepcion" />
     
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<meta name="geo.region" content="CL-BI" />
	<meta name="geo.placename" content="Concepci&oacute;n" />
	<meta name="geo.position" content="-36.820135;-73.04439" />
	<meta name="ICBM" content="-36.820135, -73.04439" />

	
	<meta property="og:title" content="Dobbs Propiedades - Propiedades Destacadas" />
	<meta property="og:type" content="website" />
	<meta property="og:url" content="index.php" />
	<meta property="og:image" content="img/logo-fb.jpg" />
	<meta property="og:description" content="Dobbs Propiedades, venta y arriendo de propiedades en Concepción. Propiedades en la Octava Región." />				
	 
	 
	
	<link rel="stylesheet" type="text/css" href="./css/style.css" />
		
		<link rel="stylesheet" type="text/css" href="css/print.css" media="print" />
        <link rel="shortcut icon" href="favicon.ico" />
        <link rel="apple-touch-icon-precomposed" href="apple_icon.png" />
        <script src="js/vendor/modernizr-2.6.2.min.js"></script>
        <style> .correo:after{content: "contacto\40 dobbspropiedades.cl";}</style>
		 
<script src="./js/jquery.min.js"></script>

<script type="text/javascript">
function ejecutar(id){  
	    $("#sMenu").empty();
		 $('#sMenu').prop('disabled', 'disabled');
        var res=new Array(); 	 
		$.ajax({
			type:"post",
			url: "./include/proceso5.php", 
        	data: {id:id},  
			dataType:"json",
			success:function(datos){
			$("#sMenu").empty(); 
				$("#sMenu").attr("disabled",true);
				if(datos=="error"){					
					$('#sMenu').prop('disabled', 'disabled');
				}else{
					 $("#sMenu").empty();
					$("#sMenu").attr("disabled",false);
					res=datos;
					for(k in res){
						for (i in res[k]){
               	 		     $("#sMenu").append("<option value="+i+">"+res[k][i]+"</option>");     
						}
					}
				}
			}
		});    
}
function agregarSubMenu(){
	document.location="admin.php?mod=panel&op=92";
}
		$(document).ready(function() {
			 $("#sMenu").attr("disabled","true");
			$("#b1").click(function(){
				document.location="admin.php?mod=panel&op=1&id=1";
			});
			
		     $("#b2").click(function(){
				document.location="admin.php?mod=panel&op=1&id=2";
			});
			
			$("#b3").click(function(){
				document.location="?mod=panel&op=3";
			});
			
			
			$("#b4").click(function(){
				document.location="admin.php?mod=panel&op=4";
			});
			
			
			$("#b5").click(function(){
				document.location="admin.php?mod=panel&op=5";
			});
			
			
			$("#b6").click(function(){
				document.location="admin.php?mod=panel&op=6";
			});
			
			
			$("#b7").click(function(){
				document.location="admin.php?mod=panel&op=7";
			});
			
			
			$("#b8").click(function(){
				document.location="admin.php?mod=panel&op=9";
			});
			
			
			$("#b9").click(function(){
				document.location="admin.php?mod=panel&op=61";
			});
			$("#c1").click(function(){
				document.location="admin.php?mod=panel&op=4";	
			})
			
			$("#b10").click(function(){
				 
				document.location="admin.php?mod=panel&op=12";
			});
		 
			$("#b11").click(function(){
				document.location="admin.php?mod=panel&op=13";
			});
			
			
			$("#b12").click(function(){
				document.location="admin.php?mod=panel&op=14";
			});
			
			$("#b13").click(function(){
				document.location="admin.php?mod=panel&op=13";
			});
			
			$("#b14").click(function(){
				document.location="admin.php?mod=panel&op=14";
			});
			
			$("#b15").click(function(){
				document.location="admin.php?mod=panel&op=15";
			});
			
			
			$("#b16").click(function(){
				document.location="admin.php?mod=panel&op=16";
			});
			
			$("#b17").click(function(){
				document.location="admin.php?mod=panel&op=17";
			});
			
			$("#b18").click(function(){
				document.location="admin.php?mod=panel&op=18";
			});
			$("#b19").click(function(){
				document.location="admin.php?mod=panel&op=19";
			});
			
			$("#b20").click(function(){
				document.location="admin.php?mod=panel&op=20";
			});
			
			
			
			
			$("#c").click(function(){
				document.location="desconectar.php";	
			});
			
			$("#xx").click(function(){
				document.location="admin.php?mod=panel";	
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
	
		        				</head>
	<body>

		<div id="wrapper">
			
									
			<div class="header">
				<div class="header-content">
		        	<div class="left-content">
		        		<a href="index.php" title="Dobbs Propiedades"><img src="img/logo.png" width="245" height="105" alt="Dobbs Propiedades" /></a>
		        	</div>
		        	
		        	<div class="right-content">
		        		<div class="phone">
		        					        				<img src="img/icon-phone.png" width="24" height="24" alt="Tel&eacute;fono" />
		        				<a href="tel:+56971083974">+56 9 7108 3974</a>
		        					        		</div>
		        		
		        		<div class="social">
		        					        				<a href="https://www.linkedin.com/company/dobbs-propiedades?trk=ppro_cprof" target="_blank"><img src="img/icon-linkedin.png" width="24" height="24" alt="LinkedIn" /></a>
		        					        			
		        					        				<a href="https://www.facebook.com/DobbsPropiedades/" target="_blank"><img src="img/icon-facebook.png" width="24" height="24" alt="Facebook" /></a>
		        					        			
		        					        				<a href="#" target="_blank"><img src="img/icon-twitter.png" width="24" height="24" alt="Twitter" /></a>
		        					        			
		        					        				<a href="https://www.instagram.com/dobbspropiedades/" target="_blank"><img src="img/icon-instagram.png" width="24" height="24" alt="Instagram" /></a>
		        					        		</div>
		        		
		        		<div class="menu">
							<div class="rwd">
								<a href="javascript:void(0);">Men&uacute;</a>
								<div class="rwd-button"><span>&nbsp;</span><span>&nbsp;</span><span>&nbsp;</span></div>
							</div>
							<ul>
								<li class="inicio"><a class='active' href="index.php">Inicio</a></li>
								<li class="quienes"><a href="nosotros/index.php">Nosotros</a></li>
								
																
								<li class="propiedades">
									<a id="propiedades"  href="javascript:void(0);">Propiedades</a>
									<ul class="sub">
										<li><a href="propiedades-en-venta/1.html">Ventas</a></li>
										<li><a href="propiedades-en-arriendo/1.html">Arriendos</a></li>
									</ul>
								</li>
								
								<li class='servicios'><a id="servicios" href="javascript:void(0);">Servicios</a><ul class="sub">										<li><a href="servicios/arriendo-de-propiedades/1.html">Arriendo de Propiedades</a></li>
																				<li><a href="servicios/venta-de-propiedades/3.html">Venta de Propiedades</a></li>
																				<li><a href="servicios/administracion-de-arriendos/8.html">Administración de Arriendos</a></li>
																				<li><a href="servicios/tasacion-de-propiedades/18.html">Tasación de Propiedades</a></li>
										</ul></li>								
								<li class="contacto"><a href="contacto/index.php">Contacto</a></li>
							</ul>
						</div>
		        	</div>
		        </div>
			</div>
					 	
			<div id="content">
				
				<div class="slides">
				
								<?php
             
				  
			 
				  if(isset($_SESSION["auth"]["nick"])){
					    echo "<div style='margin-top:10px;margin-bottom:100px;'>";
			   echo "<div style='height:60px;'>";
				echo "<h4><span style='font-size:20px;'>Panel de Administración</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				echo " Bienvenidos, estas conectado como : Administrador &nbsp;&nbsp;&nbsp;&nbsp;<a href='desconectar.php'>Salir</a>";
				
				if(isset($_GET["op"])){
					  
					  echo "<a href='admin.php?mod=panel'>&nbsp;&nbsp;-&nbsp;&nbsp; Volver atras</a>";  
					  
				  }
				  
				echo "</h4>";
                  echo "</div>";
				  
				  
                  	$miOrbis->panelBoot();				   				  
                  }else{
                  	if(isset($_GET["mod"]) && $_GET["mod"]=="rec"){
                  		$miOrbis->recuperarContra();
                  	}else{
                  		$miOrbis->login();
                  	}                  
					 
                  }
				  echo "</div>";
                  ?>
		       	
								</div>
					 
			</div>
	    </div>
	    
	    		<div role="contentinfo" class="footer">
			<div class="f-content">
				<div class="data info">
					<span class="st">Dobbs Propiedades</span>
	        		
		        				    				<div class="correos">
	        					E-mail: <span class="correo"></span>
	        					<br />
	        				</div>
		        			        		
		        			        				Tel&eacute;fono: 
	        			
	        						    					<a href="tel:+56971083974">+56 9 7108 3974</a>
		    						    			
			    					    				<br />
		    				    			
		    			<div class="powered">
			    			<a href="http://www.programacionwebchile.cl" target="_blank" title="Soluciones para su proyecto web">dise&ntilde;o web</a> 
		    			</div>
	        		</div>
	        	
	        		<div class="data accesos">
	        			<span class="cr">Accesos</span>
	        	
		        		<div class="f-menu">
		        			<ul>
		        				<li><a href="index.php">Inicio</a></li>
			        			<li><a href="nosotros/index.php">Nosotros</a></li>
			        			<li><a href="contacto/index.php">Contacto</a></li>
			        		</ul>
		        		</div>
			        </div>
		        
		        									<div class="data servicios">
	        			<span class="cr">Servicios</span>
	        			
	        			<div class="f-menu">
			        		<ul>
			        			<li><a  href="servicios/arriendo-de-propiedades/1.html">Arriendo de Propiedades</a></li><li><a  href="servicios/venta-de-propiedades/3.html">Venta de Propiedades</a></li><li><a  href="servicios/administracion-de-arriendos/8.html">Administración de Arriendos</a></li><li><a  href="servicios/tasacion-de-propiedades/18.html">Tasación de Propiedades</a></li>							</ul>
						</div>
					</div>
						        
		        <div class="data indicadores">
	        		<span class="cr">Indicadores</span>
		        	
	        		<div class="valores">
		        		<span>UF:</span>&nbsp;&nbsp;&nbsp;$26.614,53<br /><span>USD:</span>&nbsp;&nbsp;&nbsp;$648,77<br /><span>UTM:</span>&nbsp;&nbsp;&nbsp;$46.787,00<br />		        	</div>		        	
		        </div>
	    	</div>
		</div>
	        
	   	<a href="javascript:void(0);" class="go-top"><img src="img/gotop.png" width="38" height="38" alt="Arriba" /></a>	    		<script type="text/javascript" src="js/vendor/jquery.min.1.8.3.js"></script>
		<script type="text/javascript" src="js/product/js/slides.min.jquery.js"></script>
		<script type="text/javascript" src="js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
        	<script type="text/javascript" src="js/skdslider/skdslider.min.js"></script>
	        <script type="text/javascript" src="js/easyslider/easyslider.js"></script>
        	<script type="text/javascript" src="js/validate/jquery.validate.js"></script>
		<script type="text/javascript" src="js/plugins.js"></script>
		
		     	
	</body>
 
</html>