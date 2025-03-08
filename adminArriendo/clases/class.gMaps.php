 
<?php
/*
 * autor: Luis Olguin - Programador
 * Empresa: www.programacionwebchile.cl - programacion web chile 
 * Descripción: Mapa georeferencial 1.0
 * Revisión: 24/3/2022
 * */
 
 
 

class miniGmaps{
	
	public function jMaps($cor){
		 echo '<script src="https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyDNpX_3El_MOS7bQnn3jPbDGXiPPnKIiV0"></script>';
 
echo ' <script>
var geocoder;
var map;
var infowindow = new google.maps.InfoWindow();
var marker = new google.maps.Marker();
 
function closeInfoWindow() {
        infowindow.close();
   }
 
function initialize1() {
  geocoder = new google.maps.Geocoder();
   var latlng = new google.maps.LatLng('.$cor.');
  var mapOptions = {
    zoom: 12,
    center: latlng,';
	
	echo "
    mapTypeId: 'roadmap'
  }
  map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
 
  google.maps.event.addListener(map, 'click', function(){
            closeInfoWindow();
          });

 
  geocoder.geocode({'latLng': latlng}, function(results, status) {
    if (status == google.maps.GeocoderStatus.OK) {
      if (results[0]) {
        map.fitBounds(results[0].geometry.viewport);
                marker.setMap(map);
                marker.setPosition(latlng);
        $('#address').text(results[0].formatted_address);
        infowindow.setContent(results[0].formatted_address);
        infowindow.open(map, marker);
        google.maps.event.addListener(marker, 'click', function(){
            infowindow.setContent(results[0].formatted_address);
            infowindow.open(map, marker);
        });
      } else {
        alert('No results found');
      }
    } else {
      alert('No ha encontrado resultados, intente nuevamente !!!');
    }
  });
}
 
function codeLatLng() {
  
}
 
$(document).ready(function(){
   initialize1();
});

</script>";
	}	 
	public function devolverRegion($id){
		$r=array(4=>"I Región de Tarapacá",
2=>"II Región de Antofagasta",
3=>"III Región de Atacama",
5=>"IV Región de Coquimbo",
11=>"IX Región de la Araucanía",
7=>"Región Metropolitana Santiago",
6=>"V Región de Valparaíso",
8=>"VI Región del Libertador General Bernardo OHiggins",
9=>"VII Región del Maule",
10=>"VIII Región del Biobío",
13=>"X Región de Los Lagos",
14=>"XI Región de Aysén ",
15=>"XII Región de Magallanes y de la Antártica Chilena",
12=>"XIV Región de Los Ríos",1=>"1XV Región de Arica y Parinacota");
return($r[$id]);
	}
	public function jQueryMaps(){
		 echo '<script src="https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyDNpX_3El_MOS7bQnn3jPbDGXiPPnKIiV0"></script>';
		$r=$_SESSION["c"];
		$region=$this->devolverRegion($r);
		echo ' <style type="text/css">
      
      body { height: 100%; margin: 0; padding: 0 }
      #map_canvas { height: 100% }
    </style>
  
   <script src="//code.jquery.com/jquery-1.10.2.js"></script>';
   echo '<script>
	$(document).ready(function() {
    load_map();    
$("#search").click(function(){
 	 var address = $("#address").val(); 
 	 if(address.length==0){
 	 	alert("Ingrese a dirección de su propiedad");
 	 	$("#address").focus();
 	 }else{
 	 	address+="Chile";
		
 	 	var geocoder = new google.maps.Geocoder();  
 	  geocoder.geocode({ "address": address}, geocodeResult);
 	 }
});
return(false);
});
 
var map;
 
function load_map() {
	 
     var myLatlng = new google.maps.LatLng(-33.4488897,-70.6692655);
    var myOptions = {
        zoom: 12,
        center: myLatlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map($("#map_canvas").get(0), myOptions);
}
 
    
 
 
function geocodeResult(results, status) {
    // Verificamos el estatus
    if (status == "OK") {
        // Si hay resultados encontrados, centramos y repintamos el mapa
        // esto para eliminar cualquier pin antes puesto
        
        
    var myLatlng = new google.maps.LatLng(results[0].geometry.location);
    var myOptions = {
        zoom: 15,
        center: myLatlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map($("#map_canvas").get(0), myOptions);
        
        
        map.fitBounds(results[0].geometry.viewport);
        
        var markerOptions = { position: results[0].geometry.location }

        $("#cordenada").attr("value", results[0].geometry.location);
       const n=$("#address").val();
        const contentString ="<div><p>"+n+"</p></div>";


        const infowindow = new google.maps.InfoWindow({
          content: contentString 

        });

        
        var marker = new google.maps.Marker(markerOptions);
        infowindow.open(map, marker);
       
      
      
        marker.setMap(map);
    } else {
        // En caso de no haber resultados o que haya ocurrido un error
        // lanzamos un mensaje con el error
        alert("Geocoding no tuvo éxito debido a: " + status);
    }
}
</script>
';
	}
	public function gDivs(){
		echo '
<table class="width2">
   <tr><td class="unitx1" style="height:30px;"><strong>Dirección:&nbsp;</strong></td><td><div id="address"></div></td></tr>
</table>
<div id="map_canvas" style="width: 690px; height: 300px"></div>';
	}
	public function mapaHtml(){
		echo '<div><input type="text" maxlength="100" id="address" value="" placeholder="Dirección" /> <input type="button" id="search" value="Buscar" /></div><br/>
<input type="text" style="margin-top:2px; margin-bottom:2px;" class="form-control input-sm"  id="cordenada" name="cordenada" value=""/></div>
<div id="map_canvas" style="margin-left:15px; margin-top:5px; margin-bottom:5px; width:200px; height:200px;"></div>';
	}
	 
}

/*
$gMaps=new miniGmaps();
$cor="-33.4418666, -70.6619756";
$gMaps->jMaps($cor);
$gMaps->gDivs();*/
?>

 
