<?php

require_once("./clases/class.coneccion.php");
 
class sql{
  	public $miCon;
    public $tbl;
	public function __construct(){
	  // include("config.php");
		$this->miCon=new coneccion();
        $this->tbl=$tbl;        
	}
    public function ejecutaSentencia($sql){
        mysql_query($sql);
        return(true);        
    }
    public function ejecutaSql($sql){
        
        $query=mysql_query($sql) or die(mysql_error());
        return($query);        
    }
  
}

?>