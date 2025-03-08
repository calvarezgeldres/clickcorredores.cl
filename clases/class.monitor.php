
 
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
 
		
			$cadena=$this->sqlIngresarVisita($datos);
	 
			mysqli_query($this->link,$cadena) or die(mysqli_error($this->link));
		
	}
	public function proveedor($ip){
    
    $informacionSolicitud = file_get_contents("https://programacionwebchile.cl/webservice.php?ip=".$ip);
      
			$d = json_decode($informacionSolicitud);
      
			foreach($d as $clave=>$valor){
				$a[$clave]=$valor;
			}
	 
			return($a);		
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

		$p=$this->proveedor($this->ipReal2());

     
		$datos["isp"]=$p["isp"];
		$datos["hostname"]=$p["hostname"];
		$datos["lat2"]=$p["latitude"];
		$datos["lon2"]=$p["longitude"];
		$datos["ciudad2"]=$p["city"];
		$datos["region2"]=$p["region"];
		$datos["coneccion"]=$p["connection_type"];
		

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
 

		$sql="INSERT INTO `coti_monitor` ( `dirIp`, `pais`, `navegador`, `sistemaOperativo`, `referencia`, `userAgent`, `fecha`,`es`,`disp`,`pais2`, `ciudad`,`region`,`codigoReg`,`codigoPais`,`continente`,`latitud`,`longitud`,`nomRegion`,
		`isp`,
		`hostname`,
		`lat2`,
		`lon2`,
		`ciudad2`,
		`region2`,
		`coneccion`,
		`bot`,`bot2`) "; 
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
						'".addslashes($d["ciudad"])."',
						'".addslashes($d["region"])."',
						'".$d["codRegion"]."',
						'".$d["codPais"]."',
						'".$d["contienente"]."',
						'".$d["lat"]."',
						'".$d["lon"]."',
						'".addslashes($d["nomRegion"])."',

						'".$d["isp"]."',
						'".addslashes($d["hostname"])."',
						'".$d["lat2"]."',
						'".$d["lon2"]."',
						'".$d["ciudad2"]."',
						'".addslashes($d["region"])."',
						'".$d["coneccion"]."',



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
 
	public function detectarBot($user){
	 
		$USER_AGENT=$user;
	 
	
	
		$crawlers = array(		
		array('google', 'GoogleBot'),
		array('pinterest', 'pinterest'),
		array('mj12bot', 'mj12bot'),
		array('frog', 'frog'),
		array('semrush', 'semrush'),
		array('bing', 'Bingbot'),
		array('slurp', 'Yahoo! Slurp'),
		array('duckduckgo', 'DuckDuckBot'),
		array('baidu', 'Baidu'),
		array('yandex', 'Yandex'),
		array('sogou', 'Sogou'),
		array('exabot', 'Exabot'),
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
		echo '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css"	integrity="sha256-kLaT2GOSpHechhsozzB+flnD+zUyjE2LlfWPgU04xyI="crossorigin=""/>';
		echo ' <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"
		integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM="
		crossorigin=""></script>';
		echo '<style>
	 
		.leaflet-container {
			height: 500px;
			width: 100%;
			max-width: 100%;
			max-height: 100%;
		}
		#map { height: 400px; }
		</style>';
		$this->link=$this->conectar();
		$sql="select* from coti_monitor where idMonitor='".$id."'";
	 
		$q=mysqli_query($this->link,$sql);
		$row=mysqli_fetch_array($q);
 
	 
		echo "<div class='row' style='margin-top:30px;'>";
		
		echo "<div class='col-md-10'>";		
		echo "Detalle del visitantes";		
		echo "</div>";
		
		echo "<div class='col-md-2'>";
		echo "<a href='panel.php?mod=panel&op=14&c=5' role='button' style='margin:5px;' class='btn btn-success btn-mb'><i class='fas fa-arrow-circle-left'></i> Volver</a>";
		echo "</div>";

		echo "</div>";

		echo "<div class='row'>";
		
		echo "<div class='col-md-12'>";
		
		echo '<table class="table table-bordered">
		<thead>
		  <tr>
	 
			<th scope="col">Fecha </th>
			<th scope="col">Dirección IP</th>
			<th scope="col">Navegador</th>
			<th scope="col">Pais conf.Nav.</th>
			<th scope="col">Ciudad</th>
			<th scope="col">Registro</th>
		  </tr>
		</thead>
		<tbody>

	 

		  <tr>
			 
			<td>'.date("d-m-Y H:i:s",$row["fecha"]).'</td>
			<td>'.$row["dirIp"].'</td>
			<td>'.$row["navegador"].'</td>
			<td>'.$row["sistemaOperativo"].'</td>
			<td>'.$row["pais"].'</td>
			<td>'.$row["es"].'</td>
		  </tr>
		  



		  <tr>
	 
		  <th scope="col">Codigo Pais </th>		  
		  <th scope="col">Sistema Operativo</th>
		  <th scope="col">Dispositivo Usado</th>
		  <th scope="col">Ciudad</th>
		  <th scope="col">Región</th>
		  <th scope="col">&nbsp;</th>
		</tr>
	  </thead>
	  <tbody>
		<tr>
		   
		  <td>'.$row["codigoPais"].'</td>
		  <td>'.$row["sistemaOperativo"].'</td>
		  <td>'.$row["disp"].'</td>
		  <td>'.$row["ciudad"].'</td>
		  <td>'.$row["region2"].'</td>

		</tr>

 
		<tr>
	 
		<th scope="col">ISP(Proveedor Internet)</th>
		<th scope="col">HostName</th>
		<th scope="col">Ciudad</th>
		<th scope="col">Región</th>
		<th scope="col">Tipo Conección</th>
		<th scope="col">Latitud</th>
		<th scope="col">Longitud</th>
	  </tr>
	</thead>
	<tbody>
	  <tr>
		 
		<td>'.$row["isp"].'</td>
		<td>'.$row["hostname"].'</td>
		<td>'.$row["ciudad2"].'</td>
		<td>&nbsp;</td>
		<td>'.$row["coneccion"].'</td>
		<td>'.$row["lat2"].'</td>
		<td>'.$row["lon2"].'</td>
		
	  </tr>

<tr>
<td colspan="6">
User Agent
</td>
</tr>

<tr>
<td colspan="6">
'.$row["userAgent"].'
</td>
</tr>

<tr>
<td colspan="6">
Enlace de donde proviene este visitante
</td>
</tr>

<tr>
<td colspan="6">
<a href="'.$row["referencia"].'" target="_blank">'.$row["referencia"].'</a>
</td>
</tr>

<tr><td colspan="6">Rastreador</td></tr>';


echo "<tr><td colspan='6'>";
if(empty($row["bot"]) && empty($row["bot2"])){
	echo "No es un Bot";
}else{
	if(!empty($row["bot"])){
		echo "Se ha detectado el siguiente rastreador web <b>".$row["bot"]."</b> - <a href='https://www.google.cl/search?q=".$row["bot"]."' target='_blank'>https://www.google.cl/search?q=".$row["bot"]."</a>";
	}else{
		echo "Se ha detectado el siguiente rastreador web <b>".$row["bot2"]."</b> - <a href='https://www.google.cl/search?q=".$row["bot2"]."' target='_blank'>https://www.google.cl/search?q=".$row["bot2"]."</a>";
	}
}
echo "</td></tr>";


echo "<tr><td colspan='6'>";
if(!empty($row["latitud"])){
	echo "<div>Geolocalizador aproximada de la persona que se conecta: Latitud:".$row["lat2"]." - Longitud: ".$row["lon2"]."</div>";
	echo "<div><h4><a href='https://www.coordenadas-gps.com/latitud-longitud/".$row["latitud"]."/".$row["longitud"]."/13/roadmap' target='_blank'>Ver Geo Referencia</a></h4></div>";
	
}
echo "</td></tr>";
echo "<tr><td colspan='6'>";
if(!empty($row["lat2"])){
echo '<div>Geolocalizador ISP (IP REGISTRADA POR TU PROVEEDOR DE INTERNET)</div>';
}
echo ' <div id="map"></div>';
echo '<script>';

if(!empty($row["lat2"])){
	echo 'const map = L.map("map").setView(['.$row["lat2"].', '.$row["lon2"].'], 13);
	var marker = L.marker(['.$row["lat2"].', '.$row["lon2"].']).addTo(map);';
}else{
	echo 'const map = L.map("map").setView(['.$row["latitud"].', '.$row["longitud"].'], 13);
var marker = L.marker(['.$row["latitud"].', '.$row["longitud"].']).addTo(map);';
}



echo '
		
const tiles = L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
	maxZoom: 19,
	attribution: "&copy; <a href=\"http://www.openstreetmap.org/copyright\">OpenStreetMap</a>"
}).addTo(map);
marker.bindPopup("<b>Geo Referencia<br>'.$row["isp"].'<br>'.$row["hostname"].'").openPopup();

</script>';	
echo "</td></tr>";
echo '</tbody>
	  </table>';

		echo "</div>";
		
	 


		echo "</div>"; 
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
	public function obtenerVisitasMesActual() {
		$this->link=$this->conectar();
	
		// Obtener el primer y último día del mes actual en formato UNIX timestamp
		$primerDiaMes = strtotime('first day of this month');
		$ultimoDiaMes = strtotime('last day of this month');
	
		// Preparar la consulta SQL para contar las visitas en el mes actual
		$sql = "SELECT COUNT(*) AS cantidad_visitas 
				FROM coti_monitor 
				WHERE fecha >= $primerDiaMes AND fecha <= $ultimoDiaMes";
	
		// Ejecutar la consulta
		$result = mysqli_query($this->link, $sql);
	
		if ($result) {
			// Obtener el resultado
			$row = mysqli_fetch_assoc($result);
			$cantidadVisitas = $row['cantidad_visitas'];
			return $cantidadVisitas;
		} else {
			echo "Error al consultar las visitas: " . mysqli_error($this->link);
			return false;
		}
	}
	public function desplegar(){
		echo '<div class="container">
        	  <div class="row">
              <div class="col-md-12">';
		echo '<div><h4>Monitor de visitas</h4></div>
			  <div style="margin-bottom:5px;"><h5>Total visitantes : ';
		$this->totalGeneral();
		echo ' visitantes | Total Visitantes del mes : '.$this->formatoNumerico($this-> obtenerVisitasMesActual()).'</h5></div>';

		echo '<div class="btn-group" role="group" aria-label="Basic outlined example">
		<a href="panel.php?mod=panel&op=14&c=5" type="button" class="btn btn-outline-primary">Portada</a>
		<a href="panel.php?mod=panel&op=14&c=5&g=1" type="button" class="btn btn-outline-primary">Visitantes de Chile</a>
		<a href="panel.php?mod=panel&op=14&c=5&g=2" type="button" class="btn btn-outline-primary">Otros Visitantes</a>
		<a href="panel.php?mod=panel&op=14&c=5&g=3" type="button" class="btn btn-outline-primary">Rastreadores</a>
	  </div>';
	  echo '<div class="row">
      <div class="col-md-12">';
        
        if(isset($_GET["g"])){
          $g=$_GET["g"];
          if($g==1){
            $this->desplegarChile();
          }else if($g==2){
            $this->desplegarInternacional();            
          }else if($g==3){
            $this->desplegarRas();            
          }else if($g==5){            
            if(isset($_GET["tx"])){
              $id=htmlentities($_GET["idMon"]);
              $this->detalleMonitor($id);
            }

          }else{
            $this->desplegarPortada();
          }
        }else{
          $this->desplegarPortada();
        }
        echo '</div>';

		echo "</div></div></div>";

	}
 
	public function desplegar33(){
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
				
				 $this->pag=new paginator(200,25);
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

	public function totalGeneral(){
		echo $this->formatoNumerico($this->contardorVisitas());
	}



	public function desplegarChile($id=false){
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
		//$this->menu();
		if(isset($_GET["tx"])){
			$id=htmlentities($_GET["idMon"]);
			$this->detalleMonitor($id);
		}else{
			echo '<style>
					tr,td,th{font-size:12px !important;}				
				  </style>';
		echo "<div class='row' style='margin-top:10px;'>";
		echo "<div class='col-md-12'>";

		echo '<div class="table-responsive">';

echo '<table class="table table-striped table-bordered">
<thead class="table-primary table-primary-sm">
  <tr >
  
	
  <th scope="col">#</th>
	<th scope="col">Pais</th>
 
	<th scope="col">Ciudad</th>
	<th scope="col">Dirección</th>
	<th scope="col">ISP</th>
	<th scope="col">Conección</th>
	
	<th scope="col">Fecha</th>
	<th scope="col">Navegador</th>
	<th scope="col">Dispositivo</th>
	<th scope="col">Sistema Operativo</th>
	<th scope="col">Referencia</th>
	<th scope="col">Ver</th>
  </tr>
</thead>
<tbody>';

if($id!=false){
	
	$this->pag=new paginator(3,1);
	$sql="select* from coti_monitor where pais2='Chile' and codigoPais='CL' order by idMonitor desc ";	 	
}else{
	$this->pag=new paginator(200,25);

	$sql="select* from coti_monitor where pais2='Chile' and codigoPais='CL' order by idMonitor desc";	 	
}

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
		echo $row["ciudad2"];
	 }else{
		echo $row["ciudad"];
	 }
	 
	 echo "</td>";
 

	 echo "<td style='padding-left:3px;font-size:12px;'>".$row["dirIp"]."</td>";
				   echo "<td style='padding-left:3px;font-size:12px;'>".$row["isp"]."</td>";
				   echo "<td style='padding-left:3px;font-size:12px;'>".$row["coneccion"]."</td>";
	  echo "<td style='padding-left:3px;font-size:12px;'>".date("d-m-Y H:i:s",$row["fecha"])."</td>";
	 echo "<td style='padding-left:3px;font-size:12px;'>".$row["navegador"]."</td>";
	 echo "<td style='padding-left:3px;font-size:12px;'>".$row["disp"]."</td>";
	 echo "<td style='padding-left:3px;font-size:12px;'>".$row["sistemaOperativo"]."</td>";

	 echo "<td style='padding-left:3px;font-size:12px;'>".substr($row["referencia"],0,10)."...</td>";
	 
	 echo "<td style='padding-left:3px;font-size:12px;'><a href='panel.php?mod=panel&op=14&c=5&tx=1&g=5&idMon=".$row["idMonitor"]."' role='button' style='font-size:12px;padding-left:5px;padding-top:1px; padding-bottom:1px;padding-right:5px;margin:1px;height:20px;' class='btn btn-success btn-sm km'>Ver info</a></td>";
	 echo "</tr>";
	 if($s==1){
		 $s=0;
	 }else{
		  $s++;
	 }
  }
  if($id==false){
  echo "<tr><td colspan=30 align='center'>";
  if($total>1){
	 echo "<div style='margin-top:3px;'>";
	 $this->pag->navegacion();
	 echo "</div>";
 }
 echo "</td></tr>";
}
echo '</tbody>
</table></div></div></div>';

	 
	  
	  
		}
		
	}
	
	}


	
	public function desplegarRas(){
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
		//$this->menu();
		if(isset($_GET["tx"])){
			$id=htmlentities($_GET["idMon"]);
			$this->detalleMonitor($id);
		}else{
			echo '<style>
					tr,td,th{font-size:12px !important;}				
				  </style>';
		echo "<div class='row' style='margin-top:10px;'>";
		echo "<div class='col-md-12'>";

		echo '<div class="table-responsive">';

echo '<table class="table table-striped table-bordered">
<thead class="table-primary table-primary-sm">
  <tr >
  
	
  <th scope="col">#</th>
	<th scope="col">Pais</th>
 
	<th scope="col">Dirección</th>
	<th scope="col">bot</th>
 
	
	<th scope="col">Fecha</th>
	<th scope="col">Navegador</th>
	<th scope="col">Dispositivo</th>
	<th scope="col">Sistema Operativo</th>
	<th scope="col">Referencia</th>
	<th scope="col">Ver</th>
  </tr>
</thead>
<tbody>';

$this->pag=new paginator(200,25);

$sql="select* from coti_monitor where pais2!='Chile' and bot!='' and codigoPais!='CL' order by idMonitor desc";	 	
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

 
 

	 echo "<td style='padding-left:3px;font-size:12px;'>".$row["dirIp"]."</td>";
				   echo "<td style='padding-left:3px;font-size:12px;'>".$row["bot"]."</td>";
 
	  echo "<td style='padding-left:3px;font-size:12px;'>".date("d-m-Y H:i:s",$row["fecha"])."</td>";
	 echo "<td style='padding-left:3px;font-size:12px;'>".$row["navegador"]."</td>";
	 echo "<td style='padding-left:3px;font-size:12px;'>".$row["disp"]."</td>";
	 echo "<td style='padding-left:3px;font-size:12px;'>".$row["sistemaOperativo"]."</td>";

	 echo "<td style='padding-left:3px;font-size:12px;'>".substr($row["referencia"],0,10)."...</td>";
	 echo "<td style='padding-left:3px;font-size:12px;'><a href='panel.php?mod=panel&op=14&c=5&tx=1&g=5&idMon=".$row["idMonitor"]."' role='button' style='font-size:12px;padding-left:5px;padding-top:1px; padding-bottom:1px;padding-right:5px;margin:1px;height:20px;' class='btn btn-success btn-sm km'>Ver Detalles</a></td>";
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
	 echo "</td></tr>";
 }

  
echo '</tbody>
</table></div></div></div>';	 
	  
	  
		}
		
	}
	
	}
	public function desplegarInternacional(){
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
		//$this->menu();
		if(isset($_GET["tx"])){
			$id=htmlentities($_GET["idMon"]);
			$this->detalleMonitor($id);
		}else{
			echo '<style>
					tr,td,th{font-size:12px !important;}				
				  </style>';
		echo "<div class='row' style='margin-top:10px;'>";
		echo "<div class='col-md-12'>";

		echo '<div class="table-responsive">';

echo '<table class="table table-striped table-bordered">
<thead class="table-primary table-primary-sm">
  <tr >
  
	
  <th scope="col">#</th>
	<th scope="col">Pais</th>
 
	<th scope="col">Ciudad</th>
	<th scope="col">Dirección</th>
	 
 
	
	<th scope="col">Fecha</th>
	<th scope="col">Navegador</th>
	<th scope="col">Dispositivo</th>
	<th scope="col">Sistema Operativo</th>
	<th scope="col">Referencia</th>
	<th scope="col">Ver</th>
  </tr>
</thead>
<tbody>';

$this->pag=new paginator(200,25);

$sql="select* from coti_monitor where pais2!='Chile' and bot='' and codigoPais!='CL' order by idMonitor desc";	 	
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
		echo $row["ciudad2"];
	 }else{
		echo $row["ciudad"];
	 }
	 
	 echo "</td>";
 

	 echo "<td style='padding-left:3px;font-size:12px;'>".$row["dirIp"]."</td>";
 
		 
	  echo "<td style='padding-left:3px;font-size:12px;'>".date("d-m-Y H:i:s",$row["fecha"])."</td>";
	 echo "<td style='padding-left:3px;font-size:12px;'>".$row["navegador"]."</td>";
	 echo "<td style='padding-left:3px;font-size:12px;'>".$row["disp"]."</td>";
	 echo "<td style='padding-left:3px;font-size:12px;'>".$row["sistemaOperativo"]."</td>";

	 echo "<td style='padding-left:3px;font-size:12px;'>".substr($row["referencia"],0,10)."...</td>";
	 echo "<td style='padding-left:3px;font-size:12px;'><a href='panel.php?mod=panel&op=14&c=5&tx=1&g=5&idMon=".$row["idMonitor"]."' role='button' style='font-size:12px;padding-left:5px;padding-top:1px; padding-bottom:1px;padding-right:5px;margin:1px;height:20px;' class='btn btn-success btn-sm km'>Ver Detalles</a></td>";
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
	 echo "</td></tr>";
 }

  
echo '</tbody>
</table></div></div></div>';	 
	  
	  
		}
		
	}
	
	}
	public function totalChile(){
		$sql="select count(*) as total from coti_monitor where pais2='Chile' and codigoPais='CL' order by idMonitor desc limit 0,15";	 	
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
		return($r["total"]);
	}
	public function devolverLat(){
		$sql="select* from coti_monitor where pais2='Chile' and codigoPais='CL' order by idMonitor desc limit 0,15";	 	
		$q=mysqli_query($this->link,$sql);
		$r=mysqli_fetch_array($q);
		$d["lat"]=$r["lat2"];
		$d["lon"]=$r["lon2"];
		$d["latitud"]=$r["latitud"];
		$d["longitud"]=$r["longitud"];
		$d["hostname"]=$r["hostname"];
		$d["isp"]=$r["isp"];
		$d["fecha"]=$r["fecha"];
		$d["dirIp"]=$r["dirIp"];
		$d["nav"]=$r["navegador"];
		$d["so"]=$r["sistemaOperativo"];
		$d["region"]=$r["region2"];
		$d["ciudad"]=$r["ciudad2"];
		$d["disp"]=$r["disp"];
		$d["coneccion"]=$r["coneccion"];
		return($d);
	}
	public function resumenChile(){
		$this->link=$this->conectar();
	 
		 
		//$this->menu();
 
			echo '<style>
					tr,td,th{font-size:12px !important;}				
				  </style>';
 

		echo '<div class="table-responsive">';

echo '<table class="table table-striped table-bordered">
<thead class="table-primary table-primary-sm">
  <tr >
  
	
  <th scope="col">#</th>
	<th scope="col">Pais</th>
	
	<th scope="col">Ciudad</th>	
 
 
	<th scope="col">Dirección</th>
 
	<th scope="col">Fecha</th>
 
	<th scope="col">Dispositivo</th>
	 
	<th scope="col">Ver</th>
  </tr>
</thead>
<tbody>';

$this->pag=new paginator(200,25);
$sql="select* from coti_monitor where pais2='Chile' and codigoPais='CL' order by idMonitor desc limit 0,15";	 	

$q=mysqli_query($this->link,$sql);
 
 
while($row=mysqli_fetch_array($q)){
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
  
	 echo "<td style='padding-left:3px;font-size:12px;'>".$row["dirIp"]."</td>";
 
	  echo "<td style='padding-left:3px;font-size:12px;'>".date("d-m-Y H:i:s",$row["fecha"])."</td>";
 
	 echo "<td style='padding-left:3px;font-size:12px;'>".$row["disp"]."</td>";
	  
	 echo "<td style='padding-left:3px;font-size:12px;'><a href='?mod=panel&op=14&c=5&tx=1&g=5&idMon=".$row["idMonitor"]."' role='button' style='font-size:12px;padding-left:5px;padding-top:1px; padding-bottom:1px;padding-right:5px;margin:1px;height:20px;' class='btn btn-success btn-sm km'>Ver Detalles</a></td>";
	 echo "</tr>";
	  
	}
  
echo '</tbody>
</table></div>'; 
		
 
	
	}
	public function desplegarPortada(){
		
		echo '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css"	integrity="sha256-kLaT2GOSpHechhsozzB+flnD+zUyjE2LlfWPgU04xyI="crossorigin=""/>';
		echo ' <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"
		integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM="
		crossorigin=""></script>';
		echo '<style>
	 
		.leaflet-container {
			height: 400px;
			width: 600px;
			max-width: 100%;
			max-height: 100%;
		}
		#map,#map1 { height: 400px; }
		#map { height: 200px; }
		</style>';
		$l=$this->devolverLat();
		echo "<div class='row'>";
		
		echo "<div class='col-md-6'>";
		echo "<div style='margin-top:30px;'><h5>Ultimos Visitantes de Chile</h5></div>";
		echo "<div>Total Visitantes ".$this->formatoNumerico($this->totalChile())." | Total Visitantes de Chile del mes : ".$this->formatoNumerico($this->obtenerVisitasMesActualChile())."</div>";
		echo "<div>";
		$this->resumenChile();
		echo "</div>";

		echo "</div>";

		echo "<div class='col-md-6'>";


		echo "<div style='margin-top:30px;margin-bottom:30px;'><h5>Ultima Geo Referencia del visitante</h5></div>";
		echo ' <div id="map1"></div>';
		echo '<script>
		
		const map1= L.map("map1").setView(['.$l["latitud"].','.$l["longitud"].'], 13);
		var marker1 = L.marker(['.$l["latitud"].','.$l["longitud"].']).addTo(map1);
				
		const tiles1 = L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
			maxZoom: 19,
			attribution: "&copy; <a href=\"http://www.openstreetmap.org/copyright\">OpenStreetMap</a>"
		}).addTo(map1);
		marker1.bindPopup("<b>Geo Referencia<br>'.$l["dirIp"].'").openPopup();
		
		</script>';


		echo "<div style='margin-top:30px;'><h5>Ultima Geo Referencia del visitante por ISP (Internet Service Provider)</h5></div>";
		echo "<div style='margin-top:30px;'>";
		echo ' <div id="map"></div>';
		echo '<script>
	
		

		const map = L.map("map").setView(['.$l["lat"].', '.$l["lon"].'], 13);
		var marker = L.marker(['.$l["lat"].', '.$l["lon"].']).addTo(map);
				
		const tiles = L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
			maxZoom: 19,
			attribution: "&copy; <a href=\"http://www.openstreetmap.org/copyright\">OpenStreetMap</a>"
		}).addTo(map);
		marker.bindPopup("<b>Geo Referencia<br>'.$l["isp"].'<br>'.$l["hostname"].'").openPopup();
		
		
 

	</script>';	
		echo "</div>";
		echo "<div style='margin-top:30px;'><h5>Datos del visitante</h5></div>";
		echo "<div>";
 
		echo "<table width='100%'>";
		echo "<tr>";
		echo "<td width='50%'>Fecha: ".date("d-m-Y H:i:s",$l["fecha"])."</td><td>DirIp:".$l["dirIp"]."</td><td>HostName:".$l["hostname"]."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>ISP: ".$l["isp"]."</td><td>S.O.:".$l["so"]."</td><td>&nbsp;</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td>Ciudad: ".$l["ciudad"]."</td><td>Dispositivo:".$l["disp"]."</td><td>Conección:".$l["coneccion"]."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td colspan='3'>Navegador: ".$l["nav"]."</td>";
		echo "</tr>";
		echo "</table>";
		echo "</div>";




		echo "</div>";


		echo "</div>";
	}
	public function obtenerVisitasMesActualChile() {
		$this->link=$this->conectar();
	
		// Obtener el primer y último día del mes actual en formato UNIX timestamp
		$primerDiaMes = strtotime('first day of this month');
		$ultimoDiaMes = strtotime('last day of this month');
	
		// Preparar la consulta SQL para contar las visitas en el mes actual y país Chile
		$sql = "SELECT COUNT(*) AS cantidad_visitas 
				FROM coti_monitor 
				WHERE fecha >= $primerDiaMes 
				AND fecha <= $ultimoDiaMes 
				AND pais2 = 'Chile'";
	
		// Ejecutar la consulta
		$result = mysqli_query($this->link, $sql);
	
		if ($result) {
			// Obtener el resultado
			$row = mysqli_fetch_assoc($result);
			$cantidadVisitas = $row['cantidad_visitas'];
			return $cantidadVisitas;
		} else {
			echo "Error al consultar las visitas: " . mysqli_error($this->link);
			return false;
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
	 
	public function detDispositivo() {
		$tablet_browser = 0;
		$mobile_browser = 0;
		$body_class = 'desktop';
	
		$user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
	
		// Detectar tabletas
		if (preg_match('/(tablet|ipad|playbook|silk)|(android(?!.*(mobi|opera mini)))/i', $user_agent)) {
			$tablet_browser++;
			$body_class = "tablet";
		}
	
		// Detectar dispositivos móviles
		if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile|iphone|ipod|blackberry|opera mini|opera mobi)/i', $user_agent)) {
			$mobile_browser++;
			$body_class = "mobile";
		}
	
		// Detectar WAP perfil
		if ((strpos($user_agent, 'application/vnd.wap.xhtml+xml') > 0) || (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE']))) {
			$mobile_browser++;
			$body_class = "mobile";
		}
	
		// Detectar agentes móviles comunes
		$mobile_ua = substr($user_agent, 0, 4);
		$mobile_agents = array(
			'w3c ', 'acs-', 'alav', 'alca', 'amoi', 'audi', 'avan', 'benq', 'bird', 'blac',
			'blaz', 'brew', 'cell', 'cldc', 'cmd-', 'dang', 'doco', 'eric', 'hipt', 'inno',
			'ipaq', 'java', 'jigs', 'kddi', 'keji', 'leno', 'lg-c', 'lg-d', 'lg-g', 'lge-',
			'maui', 'maxo', 'midp', 'mits', 'mmef', 'mobi', 'mot-', 'moto', 'mwbp', 'nec-',
			'newt', 'noki', 'palm', 'pana', 'pant', 'phil', 'play', 'port', 'prox', 'qwap',
			'sage', 'sams', 'sany', 'sch-', 'sec-', 'send', 'seri', 'sgh-', 'shar', 'sie-',
			'siem', 'smal', 'smar', 'sony', 'sph-', 'symb', 't-mo', 'teli', 'tim-', 'tosh',
			'tsm-', 'upg1', 'upsi', 'vk-v', 'voda', 'wap-', 'wapa', 'wapi', 'wapp', 'wapr',
			'webc', 'winw', 'xda ', 'xda-'
		);
	
		if (in_array($mobile_ua, $mobile_agents)) {
			$mobile_browser++;
		}
	
		// Detectar Opera Mini
		if (strpos($user_agent, 'opera mini') > 0) {
			$mobile_browser++;
			$stock_ua = strtolower(isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA']) ? $_SERVER['HTTP_X_OPERAMINI_PHONE_UA'] : (isset($_SERVER['HTTP_DEVICE_STOCK_UA']) ? $_SERVER['HTTP_DEVICE_STOCK_UA'] : ''));
			if (preg_match('/(tablet|ipad|playbook)|(android(?!.*mobile))/i', $stock_ua)) {
				$tablet_browser++;
			}
		}
	
		if ($tablet_browser > 0) {
			$body_class = "tablet";
		} else if ($mobile_browser > 0) {
			$body_class = "mobile";
		}
	
		return $body_class;
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
	
		if(preg_match('/Arora/i',$u_agent)){
			$bname = 'Arora';
			$ub = "Arora";
		}else if(preg_match('/Galeon/i',$u_agent)){
			$bname = 'Galeon';
			$ub = "Galeon";
		}else if(preg_match('/Iceape/i',$u_agent)){
			$bname = 'Iceape';
			$ub = "Iceape";
		}else if(preg_match('/Iceweasel/i',$u_agent)){
			$bname = 'Iceweasel';
			$ub = "Iceweasel";
		}else if(preg_match('/Midori/i',$u_agent)){
			$bname = 'Midori';
			$ub = "Midori";
		}else if(preg_match('/QupZilla/i',$u_agent)){
			$bname = 'QupZilla';
			$ub = "QupZilla";
		}else if(preg_match('/NetSurf/i',$u_agent)){
			$bname = 'NetSurf';
			$ub = "NetSurf";
		}else if(preg_match('/BOLT/i',$u_agent)){
			$bname = 'BOLT';
			$ub = "BOLT";
		}else if(preg_match('/EudoraWeb/i',$u_agent)){
			$bname = 'EudoraWeb';
			$ub = "EudoraWeb";
		}else if(preg_match('/shadowfox/i',$u_agent)){
			$bname = 'shadowfox';
			$ub = "shadowfox";
		}else if(preg_match('/Swiftfox/i',$u_agent)){
			$bname = 'Swiftfox';
			$ub = "Swiftfox";
		}else if(preg_match('/Uzbl/i',$u_agent)){
			$bname = 'Uzbl';
			$ub = "Uzbl";
		}else if(preg_match('/UCBrowser/i',$u_agent)){
			$bname = 'UCBrowser';
			$ub = "UCBrowser";
		}else if(preg_match('/Kindle/i',$u_agent)){
			$bname = 'Kindle';
			$ub = "Kindle";
		}else if(preg_match('/wOSBrowser/i',$u_agent)){
			$bname = 'wOSBrowser';
			$ub = "wOSBrowser";
		}else if(preg_match('/Epiphany/i',$u_agent)){
			$bname = 'Epiphany';
			$ub = "Epiphany";
		}else if(preg_match('/SeaMonkey/i',$u_agent)){
			$bname = 'SeaMonkey';
			$ub = "SeaMonkey";
		}else if(preg_match('/Avant Browser/i',$u_agent)){
			$bname = 'Avant Browser';
			$ub = "Avant Browser";
		}else if(preg_match('/Konqueror/i',$u_agent)){
			$bname = 'Konqueror';
			$ub = "Konqueror";
		}else if(preg_match('/icab/i',$u_agent)){
			$bname = 'icab';
			$ub = "icab";
		}else if(preg_match('/Lynx/i',$u_agent)){
			$bname = 'Lynx';
			$ub = "Lynx";
		}else if(preg_match('/hotjava/i',$u_agent)){
			$bname = 'hotjava';
			$ub = "hotjava";
		}else if(preg_match('/amaya/i',$u_agent)){
			$bname = 'amaya';
			$ub = "amaya";
		}else if(preg_match('/IBrowse/i',$u_agent)){
			$bname = 'IBrowse';
			$ub = "IBrowse";
		}else if(preg_match('/iTunes/i',$u_agent)){
			$bname = 'iTunes';
			$ub = "iTunes";
		}else if(preg_match('/Silk/i',$u_agent)){
			$bname = 'Silk';
			$ub = "Silk";
		}else if(preg_match('/Dillo/i',$u_agent)){
			$bname = 'Dillo';
			$ub = "Dillo";
		}else if(preg_match('/Maxthon/i',$u_agent)){
			$bname = 'Maxthon';
			$ub = "Maxthon";
		}else if(preg_match('/OmniWeb/i',$u_agent)){
			$bname = 'OmniWeb';
			$ub = "OmniWeb";
		}else if(preg_match('/Camino/i',$u_agent)){
			$bname = 'Camino';
			$ub = "Camino";
		}else if(preg_match('/Firebird/i',$u_agent)){
			$bname = 'Firebird';
			$ub = "Firebird";
		}else if(preg_match('/Phoenix/i',$u_agent)){
			$bname = 'Phoenix';
			$ub = "Phoenix";
		}else if(preg_match('/Chimera/i',$u_agent)){
			$bname = 'Chimera';
			$ub = "Chimera";
		}else if(preg_match('/Shiira/i',$u_agent)){
			$bname = 'Shiira';
			$ub = "Shiira MAC OS";
		}else if(preg_match('/OPR/i',$u_agent)){
			$bname = 'Opera';
			$ub = "OPR";
		}else if(preg_match('/Beamrise/i',$u_agent)){
			$bname = 'Beamrise';
			$ub = "Beamrise";
		}else if(preg_match('/MSIE/i',$u_agent)){
			$bname = 'Internet Explorer';
			$ub = "MSIE";
		}else if(preg_match('/Edg/i',$u_agent)){
			$bname = 'Edge';
			$ub = "Edge";
		}else if(preg_match('/Opera Mini/i',$u_agent)){
			$bname = 'Opera Mini';
			$ub = "Opera Mini";    
		}else if(preg_match('/Firefox/i',$u_agent)){        
			$bname = 'Mozilla Firefox';
			$ub = "Firefox";
		}else if(preg_match('/Chrome/i',$u_agent)){
			$bname = 'Google Chrome';
			$ub = "Chrome";
		}else if(preg_match('/Safari/i',$u_agent)){
			$bname = 'Apple Safari';
			$ub = "Safari";
		}else if(preg_match('/Version\/10.0 Mobile/i',$u_agent)){
			$bname = 'Apple Safari 10';
			$ub = "Safari 10";
		}else if(preg_match('/Opera/i',$u_agent)){
			$bname = 'Opera';
			$ub = "Opera";
		}else if(preg_match('/Netscape/i',$u_agent)){
			$bname = 'Netscape';
			$ub = "Netscape";
		}else if(preg_match('/maxthon/i',$u_agent)){
			$bname = 'maxthon';
			$ub = "maxthon";
		}else if(preg_match('/AppleWebKit/i',$u_agent)){
			$bname = 'Webkit based browser';
			$ub = "Webkit based browser";
		}else if(preg_match('/mobile/i',$u_agent)){
			$bname = 'Handheld Browser';
			$ub = "Handheld Browser";
		}else if(preg_match('/Edge/i',$u_agent)){
			$bname = 'Edge';
			$ub = "Edge";
		}else if(preg_match('/Trident/i',$u_agent)){
			$bname = 'Internet Explorer 11';
			$ub = "MSIE";
		}else if(preg_match('/Brave/i',$u_agent)){
			$bname = 'Brave';
			$ub = "Brave";
		}else if(preg_match('/Vivaldi/i',$u_agent)){
			$bname = 'Vivaldi';
			$ub = "Vivaldi";
		}else if(preg_match('/SamsungBrowser/i',$u_agent)){
			$bname = 'Samsung Browser';
			$ub = "SamsungBrowser";
		}else if(preg_match('/DuckDuckGo/i',$u_agent)){
			$bname = 'DuckDuckGo';
			$ub = "DuckDuckGo";
		}else if(preg_match('/Puffin/i',$u_agent)){
			$bname = 'Puffin';
			$ub = "Puffin";
		}else if(preg_match('/TorBrowser/i',$u_agent)){
			$bname = 'Tor Browser';
			$ub = "TorBrowser";
		}else{
			$bname = "Desconocido";
		}
	
		return $bname;
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
		$u_agent = $_SERVER['HTTP_USER_AGENT'];
	
		if (preg_match('/windows nt 10.0/i', $u_agent)) {
			return 'Windows 10';
		} else if (preg_match('/windows nt 6.3/i', $u_agent)) {
			return 'Windows 8.1';
		} else if (preg_match('/windows nt 6.2/i', $u_agent)) {
			return 'Windows 8';
		} else if (preg_match('/windows nt 6.1/i', $u_agent)) {
			return 'Windows 7';
		} else if (preg_match('/windows nt 6.0/i', $u_agent)) {
			return 'Windows Vista';
		} else if (preg_match('/windows nt 5.2/i', $u_agent)) {
			return 'Windows Server 2003/XP x64';
		} else if (preg_match('/windows nt 5.1/i', $u_agent)) {
			return 'Windows XP';
		} else if (preg_match('/windows xp/i', $u_agent)) {
			return 'Windows XP';
		} else if (preg_match('/windows nt 5.0/i', $u_agent)) {
			return 'Windows 2000';
		} else if (preg_match('/windows me/i', $u_agent)) {
			return 'Windows ME';
		} else if (preg_match('/win98/i', $u_agent)) {
			return 'Windows 98';
		} else if (preg_match('/win95/i', $u_agent)) {
			return 'Windows 95';
		} else if (preg_match('/win16/i', $u_agent)) {
			return 'Windows 3.11';
		} else if (preg_match('/windows phone/i', $u_agent)) {
			return 'Windows Phone';
		} else if (preg_match('/windows ce/i', $u_agent)) {
			return 'Windows CE';
		} else if (preg_match('/iphone/i', $u_agent)) {
			return 'iPhone';
		} else if (preg_match('/ipad/i', $u_agent)) {
			return 'iPad';
		} else if (preg_match('/macintosh|mac os x/i', $u_agent)) {
			return 'Mac OS X';
		} else if (preg_match('/mac_powerpc/i', $u_agent)) {
			return 'Mac OS 9';
		} else if (preg_match('/android/i', $u_agent)) {
			return 'Android';
		} else if (preg_match('/blackberry/i', $u_agent)) {
			return 'BlackBerry';
		} else if (preg_match('/webos/i', $u_agent)) {
			return 'Mobile';
		} else if (preg_match('/symbian/i', $u_agent)) {
			return 'Symbian';
		} else if (preg_match('/linux/i', $u_agent)) {
			return 'Linux';
		} else if (preg_match('/nokia/i', $u_agent)) {
			return 'Nokia';
		} else if (preg_match('/ubuntu/i', $u_agent)) {
			return 'Ubuntu';
		} else if (preg_match('/freebsd/i', $u_agent)) {
			return 'FreeBSD';
		} else if (preg_match('/openbsd/i', $u_agent)) {
			return 'OpenBSD';
		} else if (preg_match('/netbsd/i', $u_agent)) {
			return 'NetBSD';
		} else if (preg_match('/sunos/i', $u_agent)) {
			return 'SunOS';
		} else if (preg_match('/opensolaris/i', $u_agent)) {
			return 'OpenSolaris';
		} else if (preg_match('/dragonfly/i', $u_agent)) {
			return 'DragonFly';
		} else if (preg_match('/os2/i', $u_agent)) {
			return 'OS/2';
		} else if (preg_match('/beos/i', $u_agent)) {
			return 'BeOS';
		} else if (preg_match('/palm os/i', $u_agent)) {
			return 'Palm OS';
		} else if (preg_match('/roko/i', $u_agent)) {
			return 'ROKO';
		} else if (preg_match('/aix/i', $u_agent)) {
			return 'AIX';
		} else if (preg_match('/hp-ux/i', $u_agent)) {
			return 'HP-UX';
		} else if (preg_match('/irix/i', $u_agent)) {
			return 'IRIX';
		} else if (preg_match('/digital/i', $u_agent)) {
			return 'Digital Unix';
		} else if (preg_match('/plan9/i', $u_agent)) {
			return 'Plan 9';
		} else if (preg_match('/unix/i', $u_agent)) {
			return 'UNIX';
		} else if (preg_match('/symbos/i', $u_agent)) {
			return 'SymbOS';
		} else if (preg_match('/inferno/i', $u_agent)) {
			return 'Inferno';
		} else if (preg_match('/solaris/i', $u_agent)) {
			return 'Solaris';
		} else if (preg_match('/elementary os/i', $u_agent)) {
			return 'elementary OS';
		} else if (preg_match('/fedora/i', $u_agent)) {
			return 'Fedora';
		} else if (preg_match('/red hat/i', $u_agent)) {
			return 'Red Hat';
		} else if (preg_match('/debian/i', $u_agent)) {
			return 'Debian';
		} else if (preg_match('/mint/i', $u_agent)) {
			return 'Linux Mint';
		} else if (preg_match('/centos/i', $u_agent)) {
			return 'CentOS';
		} else if (preg_match('/arch linux/i', $u_agent)) {
			return 'Arch Linux';
		} else if (preg_match('/gentoo/i', $u_agent)) {
			return 'Gentoo';
		} else if (preg_match('/chrome os/i', $u_agent)) {
			return 'Chrome OS';
		} else if (preg_match('/qnx/i', $u_agent)) {
			return 'QNX';
		} else if (preg_match('/haiku/i', $u_agent)) {
			return 'Haiku';
		} else if (preg_match('/raspbian/i', $u_agent)) {
			return 'Raspbian';
		} else if (preg_match('/kubuntu/i', $u_agent)) {
			return 'Kubuntu';
		} else if (preg_match('/xubuntu/i', $u_agent)) {
			return 'Xubuntu';
		} else if (preg_match('/lubuntu/i', $u_agent)) {
			return 'Lubuntu';
		} else if (preg_match('/manjaro/i', $u_agent)) {
			return 'Manjaro';
		} else {
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

