<?php
class sql{
	public function sqlIngresarCiudad2($d){
		$sql="insert into ciudad (ciudad) values ('".$d["ciudad"]."')";
		return($sql);
	}
	public function sqlActualizarCiudad2($d,$id){
		$sql="update ciudad set ciudad='".$d["ciudad"]."' where idCiudad='".$id."'"; 
		return($sql);
	}
	public function sqlBorrarCiudad2($id){
		$sql="delete from ciudad where idCiudad='".$id."'";
		return($sql);
	}
	public function sqlConsultarCiudad2(){
		$sql="select * from ciudad order by idCiudad desc";
		return($sql);
	}
	public function sqlConsultarIdCiudad2($id){
		$sql="select* from ciudad where idCiudad='".$id."'";
		return($sql);
	}
	public function sqlCrearAdmin($d){
		  $fecha=strtotime(date("Y-m-d H:i:s"));
		$sql="insert into admin (fecha, nombre, email, pass) values('".$fecha."','".$d["nombre"]."','".$d["email"]."','".$d["pass"]."')";
		return($sql);
	}
	public function sqlConsultarAdmin(){
		$sql="select* from admin order by idAdmin desc";
		return($sql);
	}
	public function sqlConsultarAdminId($id){
		$sql="select* from admin where idAdmin='".$id."'";
		return($sql);
	}
	public function sqlBorrarAdmin($id){
		$sql="delete from admin where idAdmin='".$id."'";
		return($sql);
	}
	public function sqlActualizarAdmin($id,$d){
		$sql="update admin set nombre='".$d["nombre"]."', email='".$d["email"]."',pass='".$d["pass"]."' where idAdmin='".$id."'";
		return($sql);
	}
		public function sqlIngresarVivienda($d,$arch){
		$cor=substr($d["cordenada"],1);
		$cor=substr($cor,0,-1);
		$m2Totales=str_replace(".","",$d["m2Totales"]);
		$m2Construidos=str_replace(".","",$d["m2Construidos"]);		
		$precio=str_replace(".","",$d["precio"]);
		$sql="insert into propiedad (idCategoria ,titulo ,operacion ,estadoProp ,direccionProp,tipoProp ,ciudad ,codigo ,precio ,m2Construido ,mt2Totales,piscina ,servicios ,dormitorios ,banos ,cocina   ,comedor ,destacar,estacionamiento ,bodega ,logia ,pieza,fecha ,descripcion,PrecioUf,cordenadas) ";
		 $fecha=strtotime(date("d-m-Y H:i:s"));
		$sql.=" values ('".$d["idCategoria"]."',
					   '".$d["titulo"]."',
					   '".$d["operacion"]."',
					   '".$d["estadoProp"]."',
					   '".$d["address"]."',
					   '".$d["tipoProp"]."',
					   '".$d["ciudad"]."',
					   '".$d["codigo"]."',
					   '".$precio."',
					   '".$m2Construidos."',
					   '".$m2Totales."',
					   '".$d["piscina"]."',
					   '".$d["piezaServicio"]."',
					   '".$d["numDor"]."',
					   '".$d["numBanos"]."',
					   '".$d["cocina"]."',			 
					   '".$d["comedor"]."',
					   '".$d["destacar"]."',
					   '".$d["numEstacionamientos"]."',
					   '".$d["bodega"]."',
					   '".$d["logia"]."',
					    '".$d["pieza"]."',
					   '".$fecha."',
					   '".$d["des"]."','".$d["precioUf"]."','".$cor."')";
					  
					 
		return($sql);
	}
	public function sqlModificarVivienda($d,$id,$c){
	 $m2Totales=str_replace(".","",$d["m2Totales"]);
		$m2Construidos=str_replace(".","",$d["m2Construidos"]);		
		$precio=str_replace(".","",$d["precio"]);
		$sql="update propiedad set  idCategoria ='".$d["ciudad"]."',
								    titulo ='".$d["titulo"]."',
								    direccionProp='".$d["address"]."',
								    operacion ='".$d["operacion"]."',
								    estadoProp ='".$d["estadoProp"]."',
								    tipoProp ='".$d["tipoProp"]."',
								    ciudad ='".$d["ciudad"]."',
								    codigo ='".$d["codigo"]."',
								    precio ='".$precio."',
								    precioUf='".$d["precioUf"]."',
								    m2Construido ='".$m2Construidos."',
								    mt2Totales='".$m2Totales."',
								    piscina ='".$d["piscina"]."',
								    servicios ='".$d["piezaServicio"]."',
								    dormitorios ='".$d["numDor"]."',
								    banos ='".$d["numBanos"]."',
								    cocina ='".$d["cocina"]."',
								 	destacar='".$d["destacar"]."',
								    estacionamiento ='".$d["numEstacionamientos"]."',
								    bodega ='".$d["bodega"]."',
								    logia ='".$d["logia"]."',
								    pieza='".$d["pieza"]."',
								    descripcion='".$d["des"]."',
								    
									cordenadas='".$c."'
									 where idProp ='".$id."'";
								     
		return($sql);
	}
	public function sqlEliminarVivienda($id){
		$sql="delete from propiedad where idProp='".$id."'";
		return($sql);
	}
	public function sqlConsultarVivienda(){
		$sql="select * from propiedad order by idProp desc";
		return($sql);
	}
	public function sqlConsultarVivienda2(){
		$sql="SELECT * FROM `propiedad` INNER JOIN cape_fotos on propiedad.idProp=cape_fotos.idProp order by propiedad.idProp desc";
		return($sql);
	}
	public function sqlConsultarViviendaCiudad($idCiudad){
		$sql="select * from propiedad where ciudad='".$idCiudad."' order by idProp desc";
		return($sql);
	}
	public function sqlConsultarViviendaTipo($id){
		$sql="select * from propiedad where tipoProp='".$id."' order by idProp desc";
		return($sql);
	}
	public function sqlConsultarViviendaDestacadas(){
		$sql="select * from propiedad where destacar='Si' order by idProp desc";
		return($sql);
	}
	public function sqlConsultarUnaVivienda($id){
		$sql="select* from  propiedad  where idProp ='".$id."' order by idProp desc";
		return($sql);
	}
	public function sqlConsultarUnaViviendaOperacion($id){
		$sql="select* from  propiedad  where operacion ='".$id."' order by idProp desc";
		return($sql);
	}
	public function sqlIngresarCiudad($d){
		$sql="insert into ciudad (ciudad) values ('".$d["ciudad"]."')";
		return($sql);
	}
	public function sqlActualizarCiudad($d,$id){
		$sql="update ciudad set where ='".$id."'"; 
		return($sql);
	}
	public function sqlBorrarCiudad($id){
		$sql="delete from ciudad where idCiudad ='".$id."'";
		return($sql);
	}
	public function sqlConsultarCiudad(){
		$sql="select * from ciudad order by idCiudad desc";
		return($sql);
	}
	public function sqlTotalFotos($id){
		$sql="select count(*) as total from cape_fotos where idProp='".$id."'";
		return($sql);
	}
	public function sqlConsultarIdCiudad($id){
		$sql="select* from 'ciudad' where ='".$id."'";
		return($sql);
	}
	public function sqlIngresarFotos($arch,$idFoto){
		foreach($arch as $clave=>$valor){
				$sql="insert into cape_fotos (idProp,ruta) values ('".$idFoto."','".$valor."')";
				mysql_query($sql);
		}
 	
	return(true);
	}
	public function sqlBorrarFotos($id){
		$sql="delete from cape_fotos where ='".$id."'"; 
		return($sql);
	}
	public function sqlActualizarFotos($d,$id){
		$sql="update cape_fotos set idFoto='".$d["idFoto"]."',idProp='".$d["idProp"]."',ruta='".$d["ruta"]."' where idFoto='".$id."'";
		return($sql);
	}
	public function sqlConsultarFotos(){
		$sql="select * from cape_fotos order by idFoto desc";
		return($sql);
	}
	public function sqlConsultarIdFotos($id){
		$sql="select* from cape_fotos  where idProp='".$id."' order by idFoto desc";
		return($sql);
	}
	public function sqlConsultarIdFotos1($id){
		$sql="select* from cape_fotos  where idProp='".$id."' order by idFoto asc";
		return($sql);
	}
	public function sqlIngresarDatosContacto($d){
		$sql="insert into datoscontacto (nombreEmpresa, direccion ,telefono ,celular, email) values (
		'".$d["nombre"]."',
		'".$d["direccion"]."', 
		'".$d["telefono"]."',
		'".$d["celular"]."',
		'".$d["email"]."')";
		return($sql);
	}
	public function sqlBorrarDatosContacto($d){
		$sql="delete from datoscontacto where idContacto='".$id."'";
		return($sql);
	}
	public function sqlActualizarDatosContacto($d,$id){
		$sql="update datoscontacto set nombreEmpresa='".$d["nombre"]."',
									   direccion='".$d["direccion"]."', 
									   telefono='".$d["telefono"]."',
								       celular='".$d["celular"]."', 
									   email='".$d["email"]."' where idContacto='".$id."'";
									   echo $sql;
		return($sql);
	}
	public function sqlConsultarDatosContacto(){
		$sql="select * from datoscontacto";
		return($sql);
	}
	public function sqlConsultarDatosIdContacto($id){
		$sql="select* from datoscontacto where idContacto='".$id."'";
		return($sql);
	}
	
	public function sqlIngresarRedesSociales($d){
		$sql="insert into redessociales (twitter, 
										 facebook, 
										 google, 
										 painterest) values ('".$d["twitter"]."',
										 					 '".$d["facebook"]."',
										 					 '".$d["google"]."',
										 					 '".$d["painterest"]."')";
		return($sql);
	}
	public function sqlBorrarRedesSociales($id){
		$sql="delete from redessociales where idRed='".$id."'";
		return($sql);
	}
	public function sqlActualizaRedesSociales($id,$d){
		$sql="update redessociales set twitter='".$d["twitter"]."',
								       facebook='".$d["facebook"]."',
									   google='".$d["google"]."',
									   painterest='".$d["painterest"]."' where idRed='".$id."'";
		return($sql);
	}
	public function sqlConsultarRedes(){
		$sql="select * from redessociales";
		return($sql);
	}
	public function sqlConsultarIdRedesSociales($id){
		$sql="select* from redessociales where idRed='".$id."'";
		return($sql);		
	}
	public function sqlIngresarContenido($d,$arch){
		$sql="insert into contenido (titulo,idSeccion,des,urlImagen) values ('".$d["titulo"]."','".$d["seccion"]."','".$d["des"]."','".$arch[0]."')";
		return($sql);
	}
	public function sqlActualizarContenido($id,$d,$arch){
		$sql="update contenido set titulo='".$d["titulo"]."',idSeccion='".$d["seccion"]."' ,des='".$d["des"]."'";
		if(!empty($arch[0])){
			$sql.=",urlImagen='".$arch[0]."'";
		}		
		$sql.=" where idContenido='".$id."'";
		return($sql);
	}
	public function sqlBorrarContenido($id){
		$sql="delete from contenido where idContenido='".$id."'";
		return($sql);
	}
	public function sqlConsultarContenido(){
		$sql="select * from contenido";
		return($sql);
	}
	public function sqlConsultarContenidoEmpresa(){
		$sql="select* from contenido where idContenido='2'";
		return($sql);
	}
	public function sqlConsultarContenidoServicios(){
		$sql="select* from contenido where idContenido='1'";
		return($sql);
	}
	public function sqlConsultarIdContenido($id){
		$sql="select* from contenido where idContenido='".$id."' order by idContenido desc";
		return($sql);
	}
	
}
/*
class sqlPlus{
	
	
	
	
	
	
	public function sqlIngresarUbicacion($d){
		$sql="insert into ubicacion (idUbicacion,direccion,mapGeo,) values ('".$d["idUbicacion"]."','".$d["direccion"]."','".$d["mapGeo"]."')";
		
		return($sql);
	}
	public function sqlBorrarUbicacion($id){
		$sql="delete from ubicacion where ='".$id."'";
		return($sql);
	}
	public function sqlActualizarUbicacion($d,$id){
		$sql="update ubicacion set idUbicacion='".$d["idUbicacion"]."',direccion='".$d["direccion"]."',mapGeo='".$d["mapGeo"]."' where idUbicacion='".$id."'";
		return($sql);
	}
	public function sqlConsultarUbicacion(){
		$sql="select * from ubicacion";
		return($sql);
	}
	public function sqlConsultarIdUbicacion($id){
		$sql="select* from 'ubicacion' where idUbicacion='".$id."'";
		return($sql);
	}
}
**/

?>