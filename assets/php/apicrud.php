<?php
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        include('connsql.php');

        switch($_POST['opcion']){
            // usuarios
            case 'insertarusuario':
                // declaramos variables que recibiran los post                
                $usuarionombre = $_POST['usuarionombre'];
                $usuarioapellido = $_POST['usuarioapellido'];
                $usuariotelefono = $_POST['usuariotelefono'];                                              
                $usuariocorreo = $_POST['usuariocorreo'];                
                $usuariocontrasenia = $_POST['usuariocontrasenia'];
                $usuarionuevousuario = $_POST['usuarionuevousuario'];
                $usuarioaccesoconfiguracion = $_POST['usuarioaccesoconfiguracion'];
                $usuarioaccesobajardatos = $_POST['usuarioaccesobajardatos'];
                $usuarioaccesosubirdatos = $_POST['usuarioaccesosubirdatos'];
                $usuarioaccesoregistroproductores = $_POST['usuarioaccesoregistroproductores'];
                $usuarioaccesoregistroacopio = $_POST['usuarioaccesoregistroacopio'];
                $usuarioestado = $_POST['usuarioestado'];
                $usuariofechacreacion = $_POST['usuariofechacreacion'];
                
                // contruimos la consulta sql
                $queryvalidacion = $conxion ->prepare("SELECT * FROM TblUsuarios WHERE UsuarioCorreo = '$usuariocorreo'");
                // ejecutamos la consulta
                $queryvalidacion ->execute();

                // recorremos la consulta y convertimos en Array
                $res = $queryvalidacion->fetchAll();     
                
                if(count($res)>0){
                    try{
                        // preparamos el update
                        $sqlupateusuario = $conxion ->prepare(" UPDATE TblUsuarios SET UsuarioNombre = '$usuarionombre', UsuarioApellido = '$usuarioapellido',
                        UsuarioTelefono = '$usuariotelefono', UsuarioContrasenia = '$usuariocontrasenia', UsuarioNuevoRegistro = $usuarionuevousuario,
                        UsuarioAccesoConfiguracion = $usuarioaccesoconfiguracion, UsuarioAccesoBajarDatos =  $usuarioaccesobajardatos, UsuarioAccesoSubirDatos = $usuarioaccesosubirdatos,
                        UsuarioAccesoRegistroProductores = $usuarioaccesoregistroproductores, UsuarioAccesoRegistroAcopio = $usuarioaccesoregistroacopio,
                        UsuarioEstado = $usuarioestado, UsuarioFechaCreacion = '$usuariofechacreacion' 
                        WHERE UsuarioCorreo = '$usuariocorreo' ");
                                                
                        $sqlupateusuario ->execute();

                        $response["mensajeactintusuario"] = "Usuario actualizados";
                        //echo 'el correo existe';

                    }catch(Exception $e){
                        echo 'Excepción capturada: ',  $e->getMessage(), "\n";
                    }
                }else{

                    try{
                        // preparamos el update
                        $sqlinserusuario = $conxion ->prepare(" INSERT INTO TblUsuarios(UsuarioNombre,UsuarioApellido,UsuarioTelefono,UsuarioCorreo,UsuarioContrasenia,UsuarioNuevoRegistro,
                        UsuarioAccesoConfiguracion,UsuarioAccesoBajarDatos,UsuarioAccesoSubirDatos,UsuarioAccesoRegistroProductores,UsuarioAccesoRegistroAcopio,
                        UsuarioEstado,UsuarioFechaCreacion)
                        VALUES ('$usuarionombre','$usuarioapellido','$usuariotelefono','$usuariocorreo','$usuariocontrasenia',$usuarionuevousuario,
                        $usuarioaccesoconfiguracion,$usuarioaccesobajardatos,$usuarioaccesosubirdatos,$usuarioaccesoregistroproductores,$usuarioaccesoregistroacopio,
                        $usuarioestado,'$usuariofechacreacion' ) ");
                                                
                        $sqlinserusuario ->execute();

                        $response["mensajeactintusuario"] = "Usuario insertados";
                        //echo 'el correo no existe';

                    }catch(Exception $e){
                        echo 'Excepción capturada: ',  $e->getMessage(), "\n";
                    }
                }
            break;

            // Configuraciones
            case 'insertarconfiguraciones':
                // declaramos variables
                $ConfiguracionId = $_POST['ConfiguracionId'];
                $ConfiguracionSufijoDocumento = $_POST['ConfiguracionSufijoDocumento'];
                $ConfiguracionUltimoDocumento = $_POST['ConfiguracionUltimoDocumento'];
                $ConfiguracionUrl = $_POST['ConfiguracionUrl'];
                $ConfiguracionTipoImpresora = $_POST['ConfiguracionTipoImpresora'];

                // contruimos la consulta sql
                $queryvalidacion = $conxion ->prepare("SELECT * FROM TblConfiguraciones WHERE ConfiguracionId = '$ConfiguracionId'");
                // ejecutamos la consulta
                $queryvalidacion ->execute();

                // recorremos la consulta y convertimos en Array
                $res = $queryvalidacion->fetchAll();     
                
                if(count($res)>0){
                    try{
                        // preparamos el update
                        $sqlupateconfiguraciones = $conxion ->prepare(" UPDATE TblConfiguraciones SET ConfiguracionSufijoDocumento = '$ConfiguracionSufijoDocumento', ConfiguracionUltimoDocumento = $ConfiguracionUltimoDocumento,
                        ConfiguracionUrl = '$ConfiguracionUrl', ConfiguracionTipoImpresora = '$ConfiguracionTipoImpresora'   
                        WHERE ConfiguracionId = '$ConfiguracionId' ");
                                                
                        $sqlupateconfiguraciones ->execute();

                        $response["mensajeactintconfiguraciones"] = "Configuracion actualizada";
                        //echo 'el correo existe';

                    }catch(Exception $e){
                        echo 'Excepción capturada: ',  $e->getMessage(), "\n";
                    }

                }else{
                    try{
                        // preparamos el update
                        $sqlinserconfiguraciones = $conxion ->prepare(" INSERT INTO TblConfiguraciones(ConfiguracionId,ConfiguracionSufijoDocumento,ConfiguracionUltimoDocumento,ConfiguracionUrl,ConfiguracionTipoImpresora)
                        VALUES ('$ConfiguracionId','$ConfiguracionSufijoDocumento',$ConfiguracionUltimoDocumento,'$ConfiguracionUrl','$ConfiguracionTipoImpresora') ");
                                                
                        $sqlinserconfiguraciones ->execute();

                        $response["mensajeactintconfiguraciones"] = "Configuracion insertada";
                        //echo 'el correo no existe';

                    }catch(Exception $e){
                        echo 'Excepción capturada: ',  $e->getMessage(), "\n";
                    }
                }
            break;

            case 'obtenerusuarios':
                try{
                    // contruimos la consulta sql
                    $queryobtenerusuarios = $conxion ->prepare(" SELECT * FROM TblUsuarios ");
                    // ejecutamos la consulta
                    $queryobtenerusuarios ->execute();

                    // recorremos la consulta y convertimos en Array
                    //$res = $queryobtenerusuarios->fetchAll();     
                    $res = $queryobtenerusuarios->fetchAll(PDO::FETCH_ASSOC);
                    
                    $response["tablausuarios"] = $res;
                    $response["mensajeobtenerusuario"] = "Usuarios obtenidos con éxito";
                    
                    //var_dump($res);
                    //echo 'el correo no existe';                    
                }catch(Exception $e){
                        echo 'Excepción capturada: ',  $e->getMessage(), "\n";
                }
            break;    
            // productores
            case 'obtenerproductores':
                try{
                    // contruimos la consulta sql
                    $queryobtenerproductores = $conxion ->prepare(" SELECT CLAVE,NOMBRE,RFC,CALLE,CRUZAMIENTOS, LOCALIDAD,MUNICIPIO,TELEFONO,SALDO FROM PROV01 WHERE STATUS = 'A' ");
                    // ejecutamos la consulta
                    $queryobtenerproductores ->execute();

                    // recorremos la consulta y convertimos en Array                      
                    $res = $queryobtenerproductores->fetchAll(PDO::FETCH_ASSOC);
                    
                    $response["tablaproveedores"] = $res;
                    $response["mensajeobtenerproductores"] = "Productores obtenidos con éxito";
                }catch(Exception $e){
                        echo 'Excepción capturada: ',  $e->getMessage(), "\n";
                }
            break;

            case 'obtenerproductos':
                try{
                    // contruimos la consulta sql
                    $queryobtenerproductos = $conxion ->prepare(" SELECT CVE_ART,DESCR,ULT_COSTO, LIN_PROD 
                                                                    FROM INVE01 A
                                                                    INNER JOIN INVE_CLIB01 B ON A.CVE_ART = B.CVE_PROD
                                                                    WHERE TIPO_ELE = 'P' AND STATUS = 'A' AND B.CAMPLIB4 = 1 ");
                    // ejecutamos la consulta
                    $queryobtenerproductos ->execute();

                    // recorremos la consulta y convertimos en Array                      
                    $res = $queryobtenerproductos->fetchAll(PDO::FETCH_ASSOC);
                    
                    $response["tablaproductos"] = $res;
                    $response["mensajeobtenerproductos"] = "Productos obtenidos con éxito";
                }catch(Exception $e){
                        echo 'Excepción capturada: ',  $e->getMessage(), "\n";
                }
            break;

            case 'obteneralmacenes':
                try{
                    // contruimos la consulta sql
                    $queryobteneralmacenes = $conxion ->prepare(" SELECT 
                                                                    CVE_ALM,DESCR 
                                                                    FROM ALMACENES01
                                                                    WHERE CVE_ALM IN (1,3) ");
                    // ejecutamos la consulta
                    $queryobteneralmacenes ->execute();

                    // recorremos la consulta y convertimos en Array                      
                    $res = $queryobteneralmacenes->fetchAll(PDO::FETCH_ASSOC);
                    
                    $response["tablaalmacenes"] = $res;
                    $response["mensajeobteneralmacenes"] = "Almacenes obtenidos con éxito";
                }catch(Exception $e){
                        echo 'Excepción capturada: ',  $e->getMessage(), "\n";
                }
            break;

            case 'obtenerconfiguraciones':
                try{
                    // contruimos la consulta sql
                    $queryobtenerconfiguraciones = $conxion ->prepare(" SELECT ConfiguracionId,ConfiguracionSufijoDocumento,ConfiguracionUltimoDocumento,ConfiguracionUrl,ConfiguracionTipoImpresora FROM TblConfiguraciones ");
                    // ejecutamos la consulta
                    $queryobtenerconfiguraciones ->execute();

                    // recorremos la consulta y convertimos en Array                      
                    $res = $queryobtenerconfiguraciones->fetchAll(PDO::FETCH_ASSOC);
                    
                    $response["tablaconfiguraciones"] = $res;
                    $response["mensajeobtenerconfiguraciones"] = "Configuración obtenida con éxito";
                }catch(Exception $e){
                        echo 'Excepción capturada: ',  $e->getMessage(), "\n";
                }
            break;            

            case 'obtenerconceptoscxp':
                try{
                    // contruimos la consulta sql
                    $queryobtenerconceptoscxp = $conxion ->prepare(" SELECT NUM_CPTO,DESCR,TIPO,CON_REFER,SIGNO,ES_FMA_PAG FROM CONP01 WHERE STATUS = 'A' ");
                    // ejecutamos la consulta
                    $queryobtenerconceptoscxp ->execute();

                    // recorremos la consulta y convertimos en Array                      
                    $res = $queryobtenerconceptoscxp->fetchAll(PDO::FETCH_ASSOC);
                    
                    $response["tablaconceptoscxp"] = $res;
                    $response["mensajeobtenerconceptoscxp"] = "Conceptos obtenidos con éxito";
                }catch(Exception $e){
                        echo 'Excepción capturada: ',  $e->getMessage(), "\n";
                }
            break;                        

        }

        $conxion = null;
        echo json_encode($response);

    }
    
?>