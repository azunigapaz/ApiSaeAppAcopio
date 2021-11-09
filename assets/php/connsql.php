<?php

    
    $conxion=new PDO("sqlsrv:server=Alejandro-Alien;database=SAE80Empre01Aprosacao","sa","Tecno!1");

    if(!$conxion){        
        echo "Error (sqlsrv_connect): ".print_r(sqlsrv_errors(), true);
    }    
    
    /*
    try{
        $consulta = $conxion ->prepare("SELECT * FROM TblUsuarios");
        $consulta ->execute();
        $datos = $consulta ->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($datos);

        foreach($datos as &$valor){        
            echo($valor['UsuarioNombre']." ".$valor['UsuarioApellido']  );
        }


    }catch(Exception $e){
        echo("Error!");
    }
    */

?>