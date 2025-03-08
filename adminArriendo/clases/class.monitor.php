
 
<?php
/*
 * Autor : Programacion web chile.cl - Soluciones para su proyecto web www.programacionwebchile.cl
 * Fecha : 18/01/2015
 * Descripcion: clase que permite efectuar una monitoreo de las visitas que entran a un sitio web 
 * Revisión: 8/5/2019
 * Versión: 2.0 
 * */
 require_once("./clases/class.coneccion.php");
require_once("./clases/class.paginator.php");
 error_reporting(1);
 
 class monitor extends coneccion{
 	public $grid;
	public $coneccion;
	public $pag;
	public $link;
 	public function __construct(){
		
		$this->link=$this->conectar();
 	}
	
 	public function ipReal2(){
 	 
		if ( $_SERVER["HTTP_X_FORWARDED_FOR"] ) {
			$realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		} elseif ( $_SERVER["HTTP_CLIENT_IP"] ) {
			$realip = $_SERVER["HTTP_CLIENT_IP"];
		} else {
			$realip = $_SERVER["REMOTE_ADDR"];
		}
		return($realip);
 	}
	public function sqlConsultarVisita($datos){
			$sql="select* from coti_monitor where dirIp='".$datos["ip"]."' and navegador='".$datos["navegador"]."'";
			return($sql);
	}
	public function registrarVisita($dato=false){
		$this->link=$this->conectar();
		/* guarda la visita en la base de datos coti_monitor*/
		$datos=$this->getDatos();
	 
		$cadena0=$this->sqlConsultarVisita($datos);		 
		
		$q=mysqli_query($this->link,$cadena0);

		if(mysqli_num_rows($q)==0){
			$cadena=$this->sqlIngresarVisita($datos);
		 
			mysqli_query($this->link,$cadena) or die(mysqli_error($this->link));
		}
	}
 
	public function datosNuevos($ip){
		$informacionSolicitud = file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip);
		$d = json_decode($informacionSolicitud);
		foreach($d as $clave=>$valor){
			$a[$clave]=$valor;
		}
 
		return($a);		
	}
	private function getDatos(){
		$miMonitor=new monitor();
 		$datos["pais"]=$this->identificaPais();
		$datos["dos"]=$this->identificarDOS();
		$datos["navegador"]=$this->identificaNavegador();
		$datos["ip"]=$this->identificarIp();
		$datos["ipReal"]=$this->ipReal2();
		$datos["userAgent"]=$this->userAgent();
 		$datos["referencia"]=$this->identificarReferencia(); 
		$datos["fecha"]=strtotime(date("d-m-Y H:i:s"));
		$datos["es"]=$_SERVER["HTTP_ACCEPT_LANGUAGE"];
		$datos["disp"]=$this->detDispositivo();
		$d=$this->datosNuevos($this->ipReal2());
	 
		$datos["pais2"]=$d["geoplugin_countryName"];
		$datos["ciudad"]=$d["geoplugin_city"];
		$datos["region"]=$d["geoplugin_region"];
		$datos["codRegion"]=$d["geoplugin_regionCode"];
		$datos["nomRegion"]=$d["geoplugin_regionName"];
		$datos["codPais"]=$d["geoplugin_countryCode"];
		$datos["continente"]=$d["geoplugin_continentName"];
		$datos["lat"]=$d["geoplugin_latitude"];
		$datos["lon"]=$d["geoplugin_longitude"];
		$datos["bot"]=$this->isBot($this->userAgent());
		$datos["bot2"]=$this->detectarBot($this->userAgent());
		 
		
		return($datos);
	}
	
	public function sqlBorrarVisita(){
		$sql="delete from coti_monitor where idMonitor='".$id."'";
		return($sql);
	}
	public function sqlSeleccionarVisita(){
		$sql="select* from coti_monitor order by idMonitor desc";
		return($sql);
	}
	
	public function sqlIngresarVisita($d){
		$sql="INSERT INTO `coti_monitor` ( `dirIp`, `pais`, `navegador`, `sistemaOperativo`, `referencia`, `userAgent`, `fecha`,`es`,`disp`,`pais2`,`ciudad`,`region`,`codigoReg`,`codigoPais`,`continente`,`latitud`,`longitud`,`nomRegion`,`bot`,`bot2`) "; 
		$sql.="values ('".$d["ip"]."',
					   '".$d["pais"]."',
					   '".$d["navegador"]."',
					   '".$d["dos"]."',
					   '".$d["referencia"]."',
					   	'".$d["userAgent"]."',
					   	'".$d["fecha"]."',
						'".$d["es"]."',
						'".$d["disp"]."',						
						'".$d["pais2"]."',
						'".$d["ciudad"]."',
						'".$d["region"]."',
						'".$d["codRegion"]."',
						'".$d["codPais"]."',
						'".$d["contienente"]."',
						'".$d["lat"]."',
						'".$d["lon"]."',
						'".$d["nomRegion"]."',
						'".$d["bot"]."',
						'".$d["bot2"]."'
					   )";
				return($sql);			
		
	}
	public function sqlActualizarVisita($d,$id){
		$sql="update coti_monitor set dirIp='".$dirIp."',
								 pais='".$pais."',
								 navegador='".$navegador."',
								 sistemaOperativo='".$dos."',
								 referencia='".$ref."',
								 userAgent='".$userAgent."'";
		$sql="where idMonitor='".$id."'";
		return($sql);
	}
	public function sqlTotalVisitas(){
		$sql="select count(*) as total from coti_monitor order by idMonitor desc";
		echo $sql;
		return($sql);
	}
	public function estilo(){
		echo '
		<style>
	 
		
		</style>
		';
	}
	public function detectarBot($user){
	 
	$USER_AGENT=$user;
 
 
	  
    $crawlers = array(
    array('Google', 'Google'),
    array('msnbot', 'MSN'),
    array('Rambler', 'Rambler'),
    array('Yahoo', 'Yahoo'),
    array('AbachoBOT', 'AbachoBOT'),
    array('accoona', 'Accoona'),
    array('AcoiRobot', 'AcoiRobot'),
    array('ASPSeek', 'ASPSeek'),
    array('CrocCrawler', 'CrocCrawler'),
    array('Dumbot', 'Dumbot'),
    array('FAST-WebCrawler', 'FAST-WebCrawler'),
    array('GeonaBot', 'GeonaBot'),
    array('Gigabot', 'Gigabot'),
    array('Lycos', 'Lycos spider'),
    array('MSRBOT', 'MSRBOT'),
    array('Scooter', 'Altavista robot'),
    array('AltaVista', 'Altavista robot'),
    array('IDBot', 'ID-Search Bot'),
    array('eStyle', 'eStyle Bot'),
    array('Scrubby', 'Scrubby robot'),
	array("SeznamBot","SeznamBot"),
	array("Facebook","facebookexternalhit")
    );

    foreach ($crawlers as $c)
    {
        if (stristr($USER_AGENT, $c[0]))
        {
            return($c[1]);
        }
    }
 
    return false;
}

 

 
	 
	public function isBot($user)
	{
 
	$bot_regex = '/BotLink|bingbot|AhrefsBot|ahoy|AlkalineBOT|SeznamBot|anthill|appie|arale|araneo|AraybOt|ariadne|arks|ATN_Worldwide|Atomz|bbot|Bjaaland|Ukonline|borg\-bot\/0\.9|boxseabot|bspider|calif|christcrawler|CMC\/0\.01|combine|confuzzledbot|CoolBot|cosmos|Internet Cruiser Robot|cusco|cyberspyder|cydralspider|desertrealm, desert realm|digger|DIIbot|grabber|downloadexpress|DragonBot|dwcp|ecollector|ebiness|elfinbot|esculapio|esther|fastcrawler|FDSE|FELIX IDE|ESI|fido|H�m�h�kki|KIT\-Fireball|fouineur|Freecrawl|gammaSpider|gazz|gcreep|BLEXBot|golem|googlebot|griffon|Gromit|gulliver|gulper|hambot|havIndex|hotwired|htdig|iajabot|INGRID\/0\.1|Informant|InfoSpiders|inspectorwww|irobot|Iron33|JBot|jcrawler|Teoma|Jeeves|jobo|image\.kapsi\.net|KDD\-Explorer|ko_yappo_robot|label\-grabber|larbin|legs|Linkidator|linkwalker|Lockon|logo_gif_crawler|marvin|mattie|mediafox|MerzScope|NEC\-MeshExplorer|MindCrawler|udmsearch|moget|Motor|msnbot|muncher|muninn|MuscatFerret|MwdSearch|sharp\-info\-agent|WebMechanic|NetScoop|newscan\-online|ObjectsSearch|Occam|Orbsearch\/1\.0|packrat|pageboy|ParaSite|patric|pegasus|perlcrawler|phpdig|piltdownman|Pimptrain|pjspider|PlumtreeWebAccessor|PortalBSpider|psbot|Getterrobo\-Plus|Raven|RHCS|RixBot|roadrunner|Robbie|robi|RoboCrawl|robofox|Scooter|Search\-AU|searchprocess|Senrigan|Shagseeker|sift|SimBot|Site Valet|skymob|SLCrawler\/2\.0|slurp|ESI|snooper|solbot|speedy|spider_monkey|SpiderBot\/1\.0|spiderline|nil|suke|http:\/\/www\.sygol\.com|tach_bw|TechBOT|templeton|titin|topiclink|UdmSearch|urlck|Valkyrie libwww\-perl|verticrawl|Victoria|void\-bot|Voyager|VWbot_K|crawlpaper|wapspider|WebBandit\/1\.0|webcatcher|T\-H\-U\-N\-D\-E\-R\-S\-T\-O\-N\-E|WebMoose|webquest|webreaper|webs|webspider|WebWalker|wget|winona|whowhere|wlm|WOLP|WWWC|none|XGET|Nederland\.zoek|AISearchBot|woriobot|NetSeer|Nutch|YandexBot|YandexMobileBot|SemrushBot|FatBot|MJ12bot|DotBot|AddThis|baiduspider|SeznamBot|mod_pagespeed|CCBot|openstat.ru\/Bot|m2e/i';
    $userAgent = empty($user) ? FALSE : $user;
    $isBot = !$userAgent || preg_match($bot_regex, $userAgent,$b);
	
	$bot=$b[0];
		 
    
    return ($bot);
	}

	public function temporizador(){
		echo '<script src="../js/jquery.min"></script>';
		echo '<script>';
		echo '$(document).ready(function(){
		 
			return(false);
		});';
		echo '</script>';
	}
	public function detalleMonitor($id){
		$this->link=$this->conectar();
		$sql="select* from coti_monitor where idMonitor='".$id."'";
	 
		$q=mysqli_query($this->link,$sql);
		$row=mysqli_fetch_array($q);
 
	 
		echo "<div class='row'>";
		echo "<div class='col-md-12'>";
		echo '<div  class="table-responsive">';
		
		echo "<table width='100%' class='table-bordered'>";
		echo "<tr><td colspan=20>";
		echo "<table width='100%'>";
		echo "<tr>";
		echo "<td>Detalle del visitante</td><td><a href='panel.php?mod=panel&op=14&c=5' role='button' style='margin:5px;' class='btn btn-success btn-sm km'>Volver</a></td>";
		echo "</td>";
		echo "</table>";
		echo "</td></tr>";
		echo "<tr>";
		echo "<td>Id:</td>";
		echo "<td>Fecha</td>";
		
		echo "<td>Dirección Ip</td>";
		 
		echo "<td>Navegador</td>";
	 
		
		echo "</tr>";
		
		echo "<tr>";
		echo "<td width='8%'>";
		echo "156.000";
		echo "</td>";

		echo "<td>";
		echo date("d-m-Y H:i:s",$row["fecha"]);
		echo "</td>";
		
		
		echo "<td>";
		echo $row["dirIp"];
		echo "</td>";
		
		
		
		echo "<td>";
		echo $row["navegador"];
		echo "</td>";
		
		
		
		
	
		
		
		echo "</tr>";		
		
		
			echo "<tr><td colspan=10>&nbsp;</td></tr>";
		echo "<tr>";
		echo "<td>Pais</td>";
		echo "<td>Ciudad</td>";
		echo "<td>Region</td>";
		echo "<td>Codigo Pais</td>";	
		echo "</tr>";
		
			echo "<tr>";
		echo "<td>";
		if(empty($row["pais2"])){
			echo "desconocido";
		}else{
			echo $row["pais2"];
		}
		echo "</td>";
		echo "<td>".$row["ciudad"]."</td>";
		echo "<td>".$row["region"]."</td>";
		echo "<td>".$row["codigoPais"]."</td>";
	 
		
		echo "</tr>";
		
				echo "<tr><td colspan=10>&nbsp;</td></tr>";
		
		echo "<tr>";
		echo "<td>Idioma</td>";
		echo "<td>Idioma Navegador</td>";
		
		echo "<td>Sistema Operativo</td>";
		echo "<td>Dispositivo Usado</td>";
		echo "</tr>";
		
		echo "<tr>";
		
		
		echo "<td>";		
		echo $row["es"];
		echo "</td>";
		
		echo "<td>";
		if(empty($row["pais"])){
			echo "Desconocido";
		}else{
			echo $row["pais"];		
		}
		echo "</td>";
		
		
		echo "<td>";
		echo $row["sistemaOperativo"];
		echo "</td>";
		
		echo "<td>";
		if(empty($row["disp"])){
			echo "Desconocido";
		}else{
		echo $row["disp"];	
		}
		
		echo "</td>";
		
		echo "<td>";
		echo "&nbsp;";
		echo "</td>";
		
		echo "</tr>";
			
	 

 
		echo "<tr><td colspan=10>&nbsp;</td></tr>";
		echo "<tr><td colspan=10>Rastreador web (Bot)- <a href='https://es.wikipedia.org/wiki/Bot' target='_blank'>¿Que es un bot - Wikipedia?</a></td></tr>";
		
	
		echo "<tr>";
		echo "<td colspan=10>";
		if(empty($row["bot"]) && empty($row["bot2"])){
			echo "No es un Bot";
		}else{
			if(!empty($row["bot"])){
				echo "Se ha detectado el siguiente rastreador web <b>".$row["bot"]."</b> - <a href='https://www.google.cl/search?q=".$row["bot"]."' target='_blank'>https://www.google.cl/search?q=".$row["bot"]."</a>";
			}else{
				echo "Se ha detectado el siguiente rastreador web <b>".$row["bot2"]."</b> - <a href='https://www.google.cl/search?q=".$row["bot2"]."' target='_blank'>https://www.google.cl/search?q=".$row["bot2"]."</a>";
			}
		}
	 
		echo "</td>";
		echo "</tr>";
			echo "<tr><td colspan=10>&nbsp;</td></tr>";
					echo "<tr><td colspan=10>&nbsp;</td></tr>";
				echo "<tr>";
		
		echo "<td colspan=10>";
		echo "User-Agent";
		echo "</td>";
		
		
		
		echo "</tr>";
		
		echo "<tr>";
		echo "<td colspan=10>";
		echo $row["userAgent"];
		echo "</td>";
		echo "</tr>";
				echo "<tr><td colspan=10>&nbsp;</td></tr>";
			echo "<tr><td colspan=10>&nbsp;</td></tr>";
			
			
		echo "<tr>";
		
		echo "<td colspan=10>";
		echo "Enlace de donde proviene este visitante";
		echo "</td>";
		
		
		
		echo "</tr>";
		
		echo "<tr>";
		echo "<td colspan=10>";
		if(empty($row["referencia"])){
			echo "No proviene de ningun enlace";
		}else{
			echo "<a href='".$row["referencia"]."' target='_blank'>".$row["referencia"]."</a>";
		}
		
		echo "</td>";
		echo "</tr>";
				echo "<tr><td colspan=10>&nbsp;</td></tr>";
						echo "<tr><td colspan=10>&nbsp;</td></tr>";
			echo "<tr><td colspan=10>&nbsp;</td></tr>";
		echo "<tr>";
		echo "<td colspan=10>Geo Referencia</td>";
		echo "</tr>";
		 
		echo "</table>";
		echo "</div>";
		echo "</div>";
		echo "</div>";
	 
		
		if(!empty($row["latitud"])){
			echo "<div>Coordenadas : Latitud:".$row["latitud"]." - Longitud: ".$row["longitud"]."</div>";
			echo "<div><h4><a href='https://www.coordenadas-gps.com/latitud-longitud/".$row["latitud"]."/".$row["longitud"]."/13/roadmap' target='_blank'>Ver Geo Referencia</a></h4></div>";
			
		}
	}
	public function menu(){
		 
		echo "<form method='post' name='form1' id='form1' action=''/>";
		echo "<div class='row'>";
		echo "<div class='col-md-12'>";
		echo "<div>";
		echo "<h4>Monitor de visitas</h4>";
		echo "</div>";
		echo "<div>";
		echo "<h4>Usted ha recibido un total de :".$this->formatoNumerico($this->contardorVisitas())." visitantes</h4>";
		echo "</div>";
		echo "<div>";
		echo "&nbsp;";
		echo "</div>";
		echo "<div class='table-responsive'>";
		echo "<table width='25%' >";
		echo "<tr>";
		echo "<td>";
		
		echo "<select name='p' id='p' class='form-control input-sm' style='width:250px;margin:10px;'>";
		//echo "<option>Ver visitas por pais</option>";
		echo "<option value=1>Ver visitas por referencia</option>";
		echo "<option value=2>Ver visitas por Sistema Operativo</option>";
		echo "<option value=3>Ver visitas por Sistema Navegador</option>";
		echo "</select>";
		echo "<td>";
		echo "<td>&nbsp;</td>";
		echo "<td>";
		echo "<input type='submit' name='ver' id='ver' value='Ver' style='width:150px;margin:10px;' class='btn btn-success btn-sm'/>";
		echo "</td>";
		echo "</tr>";
		echo "</table>";
		 echo "</DIV>";
		echo "</div>";
		echo "</div>";
		echo "</form>";
	}
	public function recolectorReferencias(){
		$this->link=$this->conectar();
		 $sql="select distinctrow count(referencia) as total ,referencia as ref from coti_monitor group by referencia order by total desc ";
		$q=mysqli_query($this->link,$sql);
		while($r=mysqli_fetch_array($q)){	
			if($r["total"]>5){
				if(!preg_match("/0x5e2526/i",$r["ref"])){
					if(!preg_match("/%2/i",$r["ref"])){
						if(!preg_match("/opr/i",$r["ref"])){
					if(empty($r["ref"])){
						$d["desconocido"]=$r["total"];
					}else{
						$d[$r["ref"]]=$r["total"];
					}
						}
					}
				}
			}
		}
		
		 return($d);;
	}
	 
	public function recolectorSistemas(){
		$this->link=$this->conectar();
			$sql="select count(sistemaOperativo) as total ,sistemaOperativo as sistema from coti_monitor group by sistemaOperativo order by total desc";
		$q=mysqli_query($this->link,$sql);
		echo "<div>";
		echo "<h4>Sistemas Operativos</h4>";
		echo "</div>";
		echo "<table width='50%' class='table-bordered'>";
		echo "<tr>";
				echo "<td>Enlace Referencia</td>";
				echo "<td>Visitas</td>";
				echo "</tr>";
		while($r=mysqli_fetch_array($q)){
			echo "<tr>";
			echo "<td style='padding:5px;'>";
			echo $r["sistema"];
			echo "</td>";
			echo "<td style='padding:5px;'>";
			echo $this->formatoNumerico($r["total"]);
			echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
	public function recolectorNav(){
		$this->link=$this->conectar();
			$sql="select count(navegador) as total ,navegador as navegador from coti_monitor group by navegador order by total desc";
		$q=mysqli_query($this->link,$sql);
		echo "<div>";
			echo "<h4>Navegador <a href='panel.php?mod=panel&op=14&c=5' role='button'  class='btn btn-success btn-sm km'>Volver</a></h4>";
			echo "</div>";
			
	 
		echo "<table width='50%' class='table-bordered'>";
		echo "<tr>";
				echo "<td>Navegador</td>";
				echo "<td>Visitas</td>";
				echo "</tr>";
		while($r=mysqli_fetch_array($q)){
			echo "<tr>";
			echo "<td style='padding:5px;'>";
			echo $r["navegador"];
			echo "</td>";
			echo "<td style='padding:5px;'>";
			echo $this->formatoNumerico($r["total"]);
			echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
	public function desplegar(){
		$this->link=$this->conectar();
	 
		if(isset($_POST["p"])){
			$p=htmlentities($_POST["p"]);
			if($p==1){
			echo "<div>";
			echo "<h4>Visitas por referencias <a href='panel.php?mod=panel&op=14&c=5' role='button' class='btn btn-success btn-sm km'>Volver</a></h4>";
			echo "</div>";
			$d=$this->recolectorReferencias();
			 
			echo "<table width='100%' class='table-bordered' border=0>";
			echo "<tr>";
				echo "<td>Enlace Referencia</td>";
				echo "<td>Visitas</td>";
				echo "</tr>";
			foreach($d as $clave=>$valor){
				
			echo "<tr>";
			echo "<td style='padding:5px;'>";
			if($clave=="desconocido"){
				echo $clave;
			}else{
				echo "<a href='".$clave."' target='_blank'>".$clave."</a>";
			}
			
			echo "<td>";
	 
			echo $this->formatoNumerico($valor); 
			echo "</td>";
		 
			echo "</tr>";
			}
			echo "</table>";
			}else if($p==2){
					echo "<div>";
			echo "<h4>Visitas por referencias <a href='panel.php?mod=panel&op=14&c=5' role='button' class='btn btn-success btn-sm km'>Volver</a></h4>";
			echo "</div>";
			$d=$this->recolectorSistemas();
			 
			echo "<table width='100%' class='table-bordered' border=0>";
			foreach($d as $clave=>$valor){
			echo "<tr>";
			echo "<td style='padding:5px;'>";
			echo $clave." :".$this->formatoNumerico($valor); 
			echo "<td>";
			echo "</td>";
			echo "</tr>";
			}
			echo "</table>";
			}else if($p==3){
				$d=$this->recolectorNav();
			}
		}else{
		$this->menu();
		if(isset($_GET["tx"])){
			$id=htmlentities($_GET["idMon"]);
			$this->detalleMonitor($id);
		}else{
			
		echo "<div class='row'>";
		echo "<div class='col-md-12'>";
		echo '<div class="table-responsive">';
		echo '<table class="table-bordered table-striped" width="100%" border=0>';
			echo "<tr  bgcolor='gray;'>";
			 echo "<td style='padding:5px;font-size:12px;'>#</td>";
			 echo "<td width='100px;'  style='padding:5px;font-size:12px;'>Pais</td>";
	
			  echo "<td width='120px;' style='padding:5px;font-size:12px;'>";
			  echo "Región";
			  echo "</td>";
			 echo "<td style='width:150px;padding:5px;font-size:12px;'>Ciudad</td>"; 
			  echo "<td width='120px;'   style='padding:5px; font-size:12px;' width='5%'>Direccióp</td>";
			   		  echo "<td width='80px;'  style='padding:2px;font-size:12px;'>Rastreador</td>";
			 echo "<td width='220px;' style='padding:5px;font-size:12px;'>Fecha</td>";
			 echo "<td width='120px;'  style='padding:5px;font-size:12px;'>Navegador</td>";
			  echo "<td width='70px;'   style='padding:5px;font-size:12px;'>Dispositivo</td>";
			
			 echo "<td style='padding:5px;font-size:12px;'>Sistema&nbsp;Operativo</td>";
			
		
			 echo "<td style='padding:5px;font-size:12px;'>referencia</td>";
			 echo "<td>&nbsp;</td>";
			 	echo "</tr>";
				
		$this->pag=new paginator(200,100);
		$sql=$this->sqlSeleccionarVisita();	 
	 	
		$this->pag->agregarConsulta($sql);
        $this->pag->estableceIndex("panel.php?mod=panel&op=14&c=5");
        $total=$this->pag->obtenerTotalReg();
		
	 
	 while($row=$this->pag->devolverResultados()){
          	$k++; 
			 echo "<tr>";
			 
			 echo "<td style='padding:2px;' width='1%'>";
			 if(empty($row["bot"]) && empty($row["bot2"])){
				 echo "<img src='./imagen/imgMonitor/32/".$row["pais2"].".png' width='22'/>";
			 }else{
				 echo "";
			 }
			 echo "</td>";
 
			 echo "<td style='padding-left:3px;font-size:12px;'>";
			
			 if(empty($row["pais2"])){
				 echo "Desconocido";
			 }else{
				echo $row["pais2"];
			 }
			 echo "</td>";

			 echo "<td style='padding-left:3px;font-size:12px;'>";
			 if(empty($row["ciudad"])){
				 echo "Desconocida";
			 }else{
				echo $row["ciudad"];
			 }
			 
			 echo "</td>";
			 echo "<td style='padding-left:3px;font-size:12px;'>";
			 if(empty($row["region"])){
				 echo "Desconocida";
			 }else{
			 echo $row["region"];
			 
			 }
			 
			 echo "</td>";
	 
			 echo "<td style='padding-left:3px;font-size:12px;'>".$row["dirIp"]."</td>";
			  			 echo "<td style='padding-left:3px;font-size:12px;'><b>".$row["bot"]."</b></td>";
			  echo "<td style='padding-left:3px;font-size:12px;'>".date("d-m-Y H:i:s",$row["fecha"])."</td>";
			 echo "<td style='padding-left:3px;font-size:12px;'>".$row["navegador"]."</td>";
			 echo "<td style='padding-left:3px;font-size:12px;'>".$row["disp"]."</td>";
			 echo "<td style='padding-left:3px;font-size:12px;'>".$row["sistemaOperativo"]."</td>";

			 echo "<td style='padding-left:3px;font-size:12px;'>".substr($row["referencia"],0,10)."...</td>";
			 echo "<td style='padding-left:3px;font-size:12px;'><a href='panel.php?mod=panel&op=14&c=5&tx=1&idMon=".$row["idMonitor"]."' role='button' style='font-size:12px;padding-left:5px;padding-top:1px; padding-bottom:1px;padding-right:5px;margin:1px;height:20px;' class='btn btn-success btn-sm km'>Ver Detalles</a></td>";
			 echo "</tr>";
			 if($s==1){
			 	$s=0;
			 }else{
			  	$s++;
			 }
          }
 
		  echo "<tr><td colspan=30 align='center'>";
	 	if($total>1){
			echo "<div style='margin-top:3px;'>";
			$this->pag->navegacion();
			echo "</div>";
		}
		echo "</td></tr>";
		echo "</table>";
		echo '</div>';
		
		
		echo "</div>";
		echo "</div>";
		echo "<div align='center'>";
		echo "Monitor de visitas versión 2.5 - Actualizado mayo 2019";
		echo "</div>";
		}
		
	}
	
	}
	public function total1(){
		$this->link=$this->conectar();
		$sql="select count(*) as total from coti_monitor order by idMonitor desc";
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
		$t=$r["total"];
		return($t);
		
	}
	public function actualizarLista(){
		$this->link=$this->conectar();
		 //$sql="select* from coti_monitor order by idMonitor desc limit 900,1000";
		// $sql="select* from coti_monitor order by idMonitor desc limit 1800,2000";
		$sql="select* from coti_monitor order by idMonitor desc limit 0,100";
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
		$m=1;
		while($r=mysqli_fetch_array($q)){
		 
		 
			$d1=$this->datosNuevos($r["dirIp"]);	
			$d["pais2"]=$d1["geoplugin_countryName"];
		$d["ciudad"]=$d1["geoplugin_city"];
		$d["region"]=$d1["geoplugin_region"];
		$d["codRegion"]=$d1["geoplugin_regionCode"];
		$d["nomRegion"]=$d1["geoplugin_regionName"];
		$d["codPais"]=$d1["geoplugin_countryCode"];
		$d["continente"]=$d1["geoplugin_continentName"];
		$d["lat"]=$d1["geoplugin_latitude"];
		$d["lon"]=$d1["geoplugin_longitude"];
		$d["bot"]=$this->isBot($r["userAgent"]);
			$d["bot2"]=$this->detectarBot($r["userAgent"]);
		
		$sql1="update coti_monitor set `pais2`='".$d["pais2"]."',
									   `ciudad`='".addslashes($d["ciudad"])."',
									   `region`='".addslashes($d["region"])."',
									   `codigoReg`='".$d["codRegion"]."',
									   `codigoPais`='".$d["codPais"]."',
									   `continente`='".$d["continente"]."',
									   `latitud`='".$d["lat"]."',
									   `longitud`='".$d["lon"]."',
									   `nomRegion`='".addslashes($d["nomRegion"])."',
									   `bot`='".$d["bot"]."',
									   `bot2`='".$d["bot2"]."' where idMonitor='".$r["idMonitor"]."'";
									
									 echo "[".$m."] ".$sql1."<br>";
									 $m++;
									   mysqli_query($this->link,$sql1) or die(mysqli_error($link));
									   }
		 echo "Datos Actualizador...";
	}
	public function detDispositivo(){
		
		$tablet_browser = 0;
		$mobile_browser = 0;
		$body_class = 'desktop';
	if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
		$tablet_browser++;
		$body_class = "tablet";
	}
 
	if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
		$mobile_browser++;
		$body_class = "mobile";
	}
 
	if ((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') > 0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
		$mobile_browser++;
		$body_class = "mobile";
	}
 
	$mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
	$mobile_agents = array(
    'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
    'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
    'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
    'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
    'newt','noki','palm','pana','pant','phil','play','port','prox',
    'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
    'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
    'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
    'wapr','webc','winw','winw','xda ','xda-');
 
	if (in_array($mobile_ua,$mobile_agents)) {
		$mobile_browser++;
	}
 
	if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'opera mini') > 0) {
		$mobile_browser++;
    //Check for tablets on opera mini alternative headers
    $stock_ua = strtolower(isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA'])?$_SERVER['HTTP_X_OPERAMINI_PHONE_UA']:(isset($_SERVER['HTTP_DEVICE_STOCK_UA'])?$_SERVER['HTTP_DEVICE_STOCK_UA']:''));
    if (preg_match('/(tablet|ipad|playbook)|(android(?!.*mobile))/i', $stock_ua)) {
      $tablet_browser++;
    }
}
if ($tablet_browser > 0) {
// Si es tablet has lo que necesites
   $k= 'Tablet';
}
else if ($mobile_browser > 0) {
// Si es dispositivo mobil has lo que necesites
   $k= 'Movil';
}
else {
// Si es ordenador de escritorio has lo que necesites
   $k= 'Portatil';
}  

return($k);
	}
	public function contardorVisitas(){
		$this->link=$this->conectar();
		$cadena="select count(*) as total from coti_monitor order by idMonitor desc";
		$q=mysqli_query($this->link,$cadena);
		$r=mysqli_fetch_array($q);
		$total=$r["total"];
		
		 
		return($total);
	}
	public function formatoNumerico($num){
		$n=number_format($num, 0,",",".");
		return($n);
	}
	public function identificaPais(){ 		 
		$accept = strtolower($_SERVER["HTTP_ACCEPT_LANGUAGE"]); 
		$codigo= explode( ",", $accept);	 
		$pais=$this->devolverPais($codigo[0]);
		return($pais);
	}
	public function identificaNavegador(){
		 $u_agent = $_SERVER['HTTP_USER_AGENT'];
		$bname = 'Unknown';
		$platform = 'Unknown';
		$version= "";


		// Next get the name of the useragent yes seperately and for good reason
		if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
		{
		$bname = 'Internet Explorer';
		$ub = "MSIE";
		}
		elseif(preg_match('/Firefox/i',$u_agent))
		{		
		$bname = 'Mozilla Firefox';
		$ub = "Firefox";
		}
		elseif(preg_match('/Chrome/i',$u_agent))
		{
		$bname = 'Google Chrome';
		$ub = "Chrome";
		}
		elseif(preg_match('/Safari/i',$u_agent))
		{
		$bname = 'Apple Safari';
		$ub = "Safari";
		}
		elseif(preg_match('/Version/10.0 Mobile/i',$u_agent))
		{
		$bname = 'Apple Safari 10';
		$ub = "Safari 10";
		
		}
		elseif(preg_match('/Opera/i',$u_agent))
		{
		$bname = 'Opera';
		$ub = "Opera";
		}
		elseif(preg_match('/Netscape/i',$u_agent))
		{
		$bname = 'Netscape';
		$ub = "Netscape";
		} elseif(preg_match('/maxthon/i',$u_agent))
		{
		$bname = 'maxthon';
		$ub = "maxthon";
		} elseif(preg_match('/AppleWebKit/i',$u_agent))
		{
		$bname = 'Webkit based browser';
		$ub = "Webkit based browser";
		
		} elseif(preg_match('/mobile/i',$u_agent))
		{
		$bname = 'Handheld Browser';
		$ub = "Handheld Browser";
		}else{
			$bname="Desconocido";
		}
		 
		return($bname);
	}
	public function identificarIp(){
		/* identifica la IP REAL del visitante*/
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      		 $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    	}
   		 elseif (isset($_SERVER['HTTP_VIA'])) {
     		  $ip = $_SERVER['HTTP_VIA'];
 		}
  		  elseif (isset($_SERVER['REMOTE_ADDR'])) {
   		 		   $ip = $_SERVER['REMOTE_ADDR'];
   		 }
    	else {
       	$ip = "desconocido";
    }
		  return($ip);
	}
	public function identificarDOS(){
		/*
		 * Identificar sistema operativo
		 * */
		 $useragent= strtolower($_SERVER['HTTP_USER_AGENT']);
 
		//winxp
		if (strpos($useragent, 'windows nt 5.1') !== FALSE) {
			return 'Windows XP';
		}
		elseif (strpos($useragent, 'windows nt 6.1') !== FALSE) {
		return 'Windows 7';
		}
		elseif (strpos($useragent, 'windows nt 6.0') !== FALSE) {
		return 'Windows Vista';
		}
		elseif (strpos($useragent, 'windows 98') !== FALSE) {
		return 'Windows 98';
		}
		elseif (strpos($useragent, 'windows nt 5.0') !== FALSE) {
		return 'Windows 2000';
		}
		elseif (strpos($useragent, 'windows nt 5.2') !== FALSE) {
		return 'Windows 2003 Server';
		}
		 
		elseif (strpos($useragent, 'win 9x 4.90') !== FALSE && strpos($useragent, 'win me')) {
		return 'Windows ME';
		}
		elseif (strpos($useragent, 'win ce') !== FALSE) {
		return 'Windows CE';
		}
		elseif (strpos($useragent, 'win 9x 4.90') !== FALSE) {
		return 'Windows ME';
		}
		elseif (strpos($useragent, 'windows phone') !== FALSE) {
		return 'Windows Phone';
		}
		elseif (strpos($useragent, 'iphone') !== FALSE) {
		return 'iPhone';
		}
		// experimental
		elseif (strpos($useragent, 'ipad') !== FALSE) {
		return 'iPad';
		}
		elseif (strpos($useragent, 'webos') !== FALSE) {
		return 'webOS';
		}
		elseif (strpos($useragent, 'symbian') !== FALSE) {
		return 'Symbian';
		}
		elseif (strpos($useragent, 'android') !== FALSE) {
		return 'Android';
			}
		elseif (strpos($useragent, 'blackberry') !== FALSE) {
		return 'Blackberry';
		}
		elseif (strpos($useragent, 'mac os x') !== FALSE) {
		return 'Mac OS X';
		}
		elseif (strpos($useragent, 'macintosh') !== FALSE) {
		return 'Macintosh';
		}
		elseif (strpos($useragent, 'linux') !== FALSE) {
		return 'Linux';
		}
		elseif (strpos($useragent, 'freebsd') !== FALSE) {
		return 'Free BSD';
		}
		elseif (strpos($useragent, 'symbian') !== FALSE) {
		return 'Symbian';
		}
		else if(strpos($useragent,"windows nt 6.3")!==FALSE){
			return("Windows 8.1");
		}
		else if(strpos($useragent,"windows nt 6.2")!==FALSE){
			return("Windows 8");
		}
		else if(strpos($useragent,"windows nt 6.1")!==FALSE){
			return("Windows 7");
		}
		else if(strpos($useragent,"windows nt 6.0")!==FALSE){
			return("windows vista");
		}
		else if(strpos($useragent,"windows nt 5.2")!==FALSE){
			return("Windows Server 2003/XP x64");
		}
		else if(strpos($useragent,"windows nt 5.1")!==FALSE){
			return("Windows XP");
		}
		else if(strpos($useragent,"windows xp")!==FALSE){
			return("windows xp");
		}
		else if(strpos($useragent,"windows nt 5.0")!==FALSE){
			return("Windows 2000");
		}
		else if(strpos($useragent,"windows me")!==FALSE){
			return("Windows ME");
		}
		else if(strpos($useragent,"win95")!==FALSE){
			return("Windows 95");
		}
		else if(strpos($useragent,"win16")!==FALSE){
			return("Windows 3.11");
		}
		else if(strpos($useragent,"macintosh|mac os x")!==FALSE){
			return("Windows 3.11");
		}
		else if(strpos($useragent,"mac_powerpc")!==FALSE){
			return("Windows 3.11");
		}
		else if(strpos($useragent,"ubuntu")!==FALSE){
			return("UBUNTU");
		}
		else if(strpos($useragent,"linux")!==FALSE){
			return("Linux");
		}
		else if(strpos($useragent,"android")!==FALSE){
			return("Android");
		}else if(strpos($useragent,"blackberry")!==FALSE){
			return("blackberry");
		}else if(strpos($useragent,"ipod")!==FALSE){
			return("Ipod");
		}else if(strpos($useragent,"ipad")!==FALSE){
			return("ipad");
		}else if(strpos($useragent,"webos")!==FALSE){
			return("Webos");
		  
		}else{
		 
		return 'Desconocido';
		 
		}
	}
	public function identificarReferencia(){
	 
		$server =  $_SERVER['HTTP_REFERER'];
		return($server);
	}
 
	public function userAgent(){
		 return($_SERVER["HTTP_USER_AGENT"]);
	}
	public function piePagina(){
		echo "<br><span style='font-family:arial; font-size:12px;'>";
		echo "Desarrollado por Programación Web Chile.cl - 2015";
		echo "</span>";
	}
	 
	public function devolverPais($c){
 			$code["af"]="Africa";
 			$code["sq"]="Albania";
 			$code["ar"]="Arabia";
 			$code["ar-dz"]="Algeria";
			$code["ar-bh"]="Bahrain";
			$code["ar-eg"]="Egipto";
			$code["ar-iq"]="Iraq";
			$code["ar-jo"]="Jordania";
			$code["ar-kw"]="Kuwait";
			$code["ar-lb"]="Libano";
			$code["ar-ly"]="Libia";
			$code["ar-ma"]="Marruecos";
			$code["ar-om"]="Oman";
			$code["ar-qa"]="Qatar";
			$code["ar-sa"]="Arabia Saudita";
			$code["ar-sy"]="Siria";
			$code["ar-tn"]="Tunisia";
			$code["ar-ae"]="U.A.E";
			$code["ar-ye"]="Yemen";
			$code["ar"]="Aragonese";
			$code["hy"]="Armenia";
			$code["bg"]="Bulgaria";
			$code["be"]="Belorusia";
			$code["bs"]="Bosnia";
			$code["br"]="Breton";
			$code["zh"]="China";
			$code["zh-hk"]="Honk-Kong";
			$code["zh-sg"]="Singapur";
			$code["zh-tw"]="Taiwan";
			$code["hr"]="Croacia";
			$code["cs"]="Checoslovaquia";
			$code["en"]="Inglaterra";
			$code["nl"]="Alemania";
			$code["en-au"]="Australia";
			$code["en-bz"]="Belice";
			$code["en-ca"]="Canada";
			$code["en-jm"]="Jamaica";
			$code["en-nz"]="Nueva Zelanda";
			$code["ez-ph"]="Filipinas";
			$code["en-za"]="SurAfrica";
			$code["en-tt"]="Trinidad y Tobago";
			$code["en-gb"]="Reino Unido";
			$code["en-us"]="EEUU";
			$code["en-zw"]="Simbawe";
			$code["et"]="Etonian";
			$code["fr"]="Francia";
			$code["fr-ca"]="Canada Francesa";
			$code["fr-fr"]="Francia";
			$code["el"]="Grecia";
			$code["ht"]="Haiti";
			$code["he"]="Israel";
			$code["hu"]="Hungria";
			$code["is"]="Islandia";
			$code["id"]="Indonecia";
			$code["it"]="Italia";
			$code["it-ch"]="Suiza";
			$code["ja"]="Japon";
			$code["ko"]="Corea";
			$code["ko-kp"]="Corea del Norte";
			$code["ko-kr"]="Corea del Sur";
			$code["lb"]="Luxemburgo";
			$code["fa"]="Iran";
			$code["fa-ir"]="Iran";
			$code["pl"]="Polonia";
			$code["pt"]="Portugal";
			$code["pt-br"]="Brasil";
			$code["en-ph"]="Filipinas";
			$code["en-es"]="Irlanda";
			$code["de"]="Alemania";
			$code["de-at"]="Austria";
			$code["pa-pk"]="Pakeestan";
			$code["ro"]="Rumania";
			$code["ro-mo"]="Moldavia";
			$code["ru"]="Rusia";
			$code["sl"]="Eslovenia";
			$code["es-ar"]="Argentina";
			$code["es-bo"]="Bolivia";
			$code["es-cl"]="Chile";
			$code["es-co"]="Colombia";
			$code["es-cr"]="Costa Rica";
			$code["es-do"]="Republica Dominicana";
			$code["es-ec"]="Ecuador";
			$code["es-sv"]="El Salvador";
			$code["es-gt"]="Guatemala";
			$code["es-hn"]="Honduras";
			$code["es-mx"]="Mexico";
			$code["es-ni"]="Nicaragua";
			$code["es-pa"]="Panama";
			$code["es-py"]="Paraguay";
			$code["es-pe"]="Peru";
			$code["es-pr"]="Puerto Rico";
			$code["es-es"]="España";
			$code["es-uy"]="Uruguay";
			$code["es-ve"]="Venenzuela";
			$code["zu"]="zulu";
			return($code[$c]);
			}
 }
 
 
?>

