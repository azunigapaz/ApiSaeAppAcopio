<?php

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    if($_SERVER['REQUEST_METHOD'] == 'POST'){

        include('connsql.php');

        // recibimos los datos del movil y lo convertimos en un array asociativo
        $data   = json_decode(file_get_contents( "php://input" ),true);

        try{
            // recorremos los datos
            foreach($data["Compra"] AS $row){
                // aqui insertaremos los datos en la db
                // encabezado
                $encabezadoDocumento = $row["Documento"];
                $encabezadoTipoDocumento = $row["TipoDocumento"];
                $EncabezadoProveedor = $row["Proveedor"];
                $EncabezadoEstado = $row["Estado"];
                $encabezadoFecha = $row["Fecha"];
                $encabezadoSubTotal = $row["SubTotal"];
                $encabezadoImpuesto = $row["Impuesto"];
                $encabezadoTotal = $row["Total"];
                $encabezadoAlmacen = $row["Almacen"];
                $encabezadoFechaHora = $row["FechaHora"];            
                
                
                // insertamos el encabezado de compras
                $sqlInsertarEncabezadoCompra = $conxion ->prepare(" SET LANGUAGE US_ENGLISH INSERT INTO COMPC01(TIP_DOC,CVE_DOC,CVE_CLPV,STATUS,SU_REFER,FECHA_DOC,FECHA_REC,FECHA_PAG,CAN_TOT,IMP_TOT1,IMP_TOT2,IMP_TOT3,IMP_TOT4,DES_TOT,DES_FIN,TOT_IND,OBS_COND,CVE_OBS,NUM_ALMA,ACT_CXP,ACT_COI,ENLAZADO,TIP_DOC_E,NUM_MONED,TIPCAMB,NUM_PAGOS,FECHAELAB,SERIE,FOLIO,CTLPOL,ESCFD,CONTADO,BLOQ,DES_FIN_PORC,DES_TOT_PORC,IMPORTE,TIP_DOC_ANT,DOC_ANT)
                VALUES('c','$encabezadoDocumento','$EncabezadoProveedor','$EncabezadoEstado','','$encabezadoFecha','$encabezadoFecha','$encabezadoFecha',$encabezadoSubTotal,0,0,0,$encabezadoImpuesto,0,0,0,'',0,$encabezadoAlmacen,'S','N','O','O',1,1,1,'$encabezadoFechaHora','',0,0,'N','N','N',0,0,$encabezadoTotal,'','') ");                                    
                $sqlInsertarEncabezadoCompra ->execute();          
                
                // insertamos los campos libres de compras
                $sqlInsertarEncabezadoComprasClib = $conxion ->prepare(" INSERT INTO COMPC_CLIB01(CLAVE_DOC)
                VALUES('$encabezadoDocumento') ");                                    
                $sqlInsertarEncabezadoComprasClib ->execute();                                                

                // insertamos el encabezado de cuentas por pagar
                $sqlInsertarEncabezadoCuentasPorPagar = $conxion ->prepare(" SET LANGUAGE US_ENGLISH INSERT INTO PAGA_M01 (CVE_PROV,REFER,NUM_CARGO,NUM_CPTO,CVE_OBS,NO_FACTURA,DOCTO,IMPORTE,FECHA_APLI,FECHA_VENC,AFEC_COI,NUM_MONED,TCAMBIO,IMPMON_EXT,FECHAELAB,TIPO_MOV,SIGNO,USUARIO,STATUS)
                VALUES('$EncabezadoProveedor','$encabezadoDocumento',1,1,0,'','$encabezadoDocumento',$encabezadoTotal,'$encabezadoFecha','$encabezadoFecha','A',1,1,$encabezadoTotal,'$encabezadoFechaHora','C',1,0,'A') ");                                    
                $sqlInsertarEncabezadoCuentasPorPagar ->execute();

                // actualizamos el saldo del proveedor
                $sqlActualizarSaldoProveedor = $conxion ->prepare(" UPDATE PROV01 SET SALDO = SALDO+$encabezadoTotal WHERE CLAVE = '$EncabezadoProveedor' ");                                    
                $sqlActualizarSaldoProveedor ->execute();  

                // obtenemos el siguiente numero de folio minve
                $queryObtenerNumeroFolioMovimiento = $conxion ->prepare(" SELECT ULT_CVE+1 AS NumeroFolioMovimiento FROM TBLCONTROL01 WHERE ID_TABLA = 32 ");
                // ejecutamos la consulta
                $queryObtenerNumeroFolioMovimiento ->execute();
                // recorremos la consulta y convertimos en Array                      
                $numeroFolioMovimiento = $queryObtenerNumeroFolioMovimiento->fetchAll(PDO::FETCH_ASSOC);                
                foreach ($numeroFolioMovimiento AS $valor) {
                    $numFolioMovSig = $valor["NumeroFolioMovimiento"];                    
                }
                
                // Recorremos el detalle de la compra
                foreach($row["DetalleCompra"] AS $rowDetalles){
                    // partidas
                    $detalleTipoDocumento = $rowDetalles["TipoDocumento"];
                    $detalleDocumento = $rowDetalles["Documento"];
                    $detalleFilaNumero = $rowDetalles["FilaNumero"];
                    $detalleProducto = $rowDetalles["Producto"];
                    $detalleCantidad = $rowDetalles["Cantidad"];
                    $detallePrecio = $rowDetalles["Precio"];
                    $detalleImpuesto = $rowDetalles["Impuesto"];
                    $detalleTotalPartida = $rowDetalles["TotalPartida"];
                    $detalleAlmacen = $rowDetalles["Almacen"];
                    
                    // obtenemos el siguiente numero de movimiento para la partida
                    $queryObtenerNumeroMovimiento = $conxion ->prepare(" SELECT ULT_CVE+1 AS NumeroMovimiento FROM TBLCONTROL01 WHERE ID_TABLA = 44 ");
                    // ejecutamos la consulta
                    $queryObtenerNumeroMovimiento ->execute();
                    // recorremos la consulta y convertimos en Array                      
                    $numeroMovimiento = $queryObtenerNumeroMovimiento->fetchAll(PDO::FETCH_ASSOC);                
                    foreach ($numeroMovimiento AS $valorNumeroMovimiento) {
                        $numMovSig = $valorNumeroMovimiento["NumeroMovimiento"];                    
                    }

                    // insertamos la partida de compras
                    $sqlInsertarPartidaCompras = $conxion ->prepare(" INSERT PAR_COMPC01(CVE_DOC,NUM_PAR,CVE_ART,CANT,PXR,PREC,COST,IMPU1,IMPU2,IMPU3,IMPU4,IMP1APLA,IMP2APLA,IMP3APLA,IMP4APLA,TOTIMP1,TOTIMP2,TOTIMP3,TOTIMP4,DESCU,ACT_INV,TIP_CAM,UNI_VENTA,TIPO_ELEM,TIPO_PROD,CVE_OBS,REG_SERIE,E_LTPD,FACTCONV,COST_DEV,NUM_ALM,MINDIRECTO,NUM_MOV,TOT_PARTIDA,MAN_IEPS,APL_MAN_IMP,CUOTA_IEPS,APL_MAN_IEPS,MTO_PORC,MTO_CUOTA,CVE_ESQ)
                    VALUES('$detalleDocumento',$detalleFilaNumero,'$detalleProducto',$detalleCantidad,$detalleCantidad,0,$detallePrecio,0,0,0,0,6,6,6,4,0,0,0,0,0,'S',1,'pz','N','P',0,0,0,1,$detallePrecio,$detalleAlmacen,0,$numMovSig,$detalleTotalPartida,'N',1,0,'C',0,0,3) ");                                    
                    $sqlInsertarPartidaCompras ->execute();   

                    // insertamos los campos libres de partidas de compras
                    $sqlInsertarDetalleComprasClib = $conxion ->prepare(" INSERT INTO PAR_COMPC_CLIB01 (CLAVE_DOC,NUM_PART)
                    VALUES('$detalleDocumento',$detalleFilaNumero) ");                                    
                    $sqlInsertarDetalleComprasClib ->execute();                                                      

                    // obtenemos el costo promedio inicial (Costo final del movimiento anterior)
                    $queryObtenerCostoPromInicial = $conxion ->prepare(" SELECT TOP 1 COSTO_PROM_FIN AS CostoPromedioIncial FROM MINVE01 WHERE CVE_ART = '$detalleProducto' AND ALMACEN = $detalleAlmacen ORDER BY NUM_MOV DESC ");
                    // ejecutamos la consulta
                    $queryObtenerCostoPromInicial ->execute();
                    // recorremos la consulta y convertimos en Array                      
                    $costoPromedioIncial = $queryObtenerCostoPromInicial->fetchAll(PDO::FETCH_ASSOC);                
                    foreach ($costoPromedioIncial AS $valorCostoPromedioInicial) {
                        $costPromedioIni = $valorCostoPromedioInicial["CostoPromedioIncial"];                    
                    }

                    // obtenemos el costo promedio final (calculado) y general (el costo promedio anterior de inve)
                    $queryObtenerCostoPromFinal = $conxion ->prepare(" SELECT ROUND( ((EXIST * COSTO_PROM) + ($detalleCantidad * $detallePrecio)) / (EXIST + $detalleCantidad), 13) AS CostoPromedioFinal, COSTO_PROM AS CostoPromedioGral FROM INVE01 WHERE CVE_ART = '$detalleProducto' ");
                    // ejecutamos la consulta
                    $queryObtenerCostoPromFinal ->execute();
                    // recorremos la consulta y convertimos en Array                      
                    $costoPromedioFinal = $queryObtenerCostoPromFinal->fetchAll(PDO::FETCH_ASSOC);                
                    foreach ($costoPromedioFinal AS $valorCostoPromedioFinal) {
                        $costPromedioFin = $valorCostoPromedioFinal["CostoPromedioFinal"];                    
                        $costPromedioGral = $valorCostoPromedioFinal["CostoPromedioGral"];
                    }                

                    // actualizamos la existencia de inve
                    $sqlActualizarExistenciaInve = $conxion ->prepare(" UPDATE INVE01 SET EXIST = EXIST+$detalleCantidad WHERE CVE_ART = '$detalleProducto' ");                                    
                    $sqlActualizarExistenciaInve ->execute();                                

                    // actualizamos la existencia de multi
                    $sqlActualizarExistenciaMulti = $conxion ->prepare(" UPDATE MULT01 SET EXIST = EXIST+$detalleCantidad WHERE CVE_ART = '$detalleProducto' AND CVE_ALM = $detalleAlmacen ");                                    
                    $sqlActualizarExistenciaMulti ->execute();     

                    // obtenemos la nueva existencia de inve
                    $queryObtenerExistenciaInve = $conxion ->prepare(" SELECT EXIST AS ExistenciaGral FROM INVE01 WHERE CVE_ART = '$detalleProducto' ");
                    // ejecutamos la consulta
                    $queryObtenerExistenciaInve ->execute();
                    // recorremos la consulta y convertimos en Array                      
                    $existenciaInve = $queryObtenerExistenciaInve->fetchAll(PDO::FETCH_ASSOC);                
                    foreach ($existenciaInve AS $valorExistenciaInve) {
                        $nuevaExistenciaInve = $valorExistenciaInve["ExistenciaGral"];                    
                    }

                    // obtenemos la nueva existencia de mult
                    $queryObtenerExistenciaMult = $conxion ->prepare(" SELECT EXIST AS Existencia FROM MULT01 WHERE CVE_ART = '$detalleProducto' AND CVE_ALM = $detalleAlmacen ");
                    // ejecutamos la consulta
                    $queryObtenerExistenciaMult ->execute();
                    // recorremos la consulta y convertimos en Array                      
                    $existenciaMult = $queryObtenerExistenciaMult->fetchAll(PDO::FETCH_ASSOC);                
                    foreach ($existenciaMult AS $valorExistenciaMult) {
                        $nuevaExistenciaMult = $valorExistenciaMult["Existencia"];                    
                    }                

                    // insertamos el movimiento al inventario
                    $sqlInsertarMinve = $conxion ->prepare(" SET LANGUAGE US_ENGLISH INSERT INTO MINVE01(CVE_ART,ALMACEN,NUM_MOV,CVE_CPTO,FECHA_DOCU,TIPO_DOC,REFER,CLAVE_CLPV,VEND,CANT,CANT_COST,PRECIO,COSTO,REG_SERIE,UNI_VENTA,E_LTPD,EXIST_G,EXISTENCIA,FACTOR_CON,FECHAELAB,CVE_FOLIO,SIGNO,COSTEADO,COSTO_PROM_INI,COSTO_PROM_FIN,COSTO_PROM_GRAL,DESDE_INVE,MOV_ENLAZADO)
                    VALUES('$detalleProducto',$detalleAlmacen,$numMovSig,1,'$encabezadoFecha','c','$detalleDocumento','$EncabezadoProveedor','',$detalleCantidad,0,0,$detallePrecio,0,'pz',0,$nuevaExistenciaInve,$nuevaExistenciaMult,1,'$encabezadoFechaHora',$numFolioMovSig,1,'S',$costPromedioIni,$costPromedioFin,$costPromedioGral,'N',0) ");
                    $sqlInsertarMinve ->execute();

                    // actualizamos el costo promedio y ultimo costo
                    $sqlActualizarPromedioUltimoCosto = $conxion ->prepare(" UPDATE INVE01 SET COSTO_PROM = $costPromedioFin, ULT_COSTO = $detallePrecio WHERE CVE_ART = '$detalleProducto' ");                                    
                    $sqlActualizarPromedioUltimoCosto ->execute();                

                    // actualizamos el numero de minve
                    $sqlActualizarNumeroMinve = $conxion ->prepare(" UPDATE TBLCONTROL01 SET ULT_CVE = $numMovSig WHERE ID_TABLA = 44 ");                                    
                    $sqlActualizarNumeroMinve ->execute();            
                    
                }
                
                // actualizamos el folio de minve
                $sqlActualizarFolioMinve = $conxion ->prepare(" UPDATE TBLCONTROL01 SET ULT_CVE = $numFolioMovSig WHERE ID_TABLA = 32 ");                                    
                $sqlActualizarFolioMinve ->execute();                
            }

            // preparamos el mensaje como objeto json
            $response["respuestacompras"] = "Compras insertadas";            
            
        }catch(Exception $e){
            // preparamos el mensaje como objeto json
            $response["respuestacompras"] = " No se insertaron compras";
        }        

        // asignamos un tag y convertimos el mensaje en un array
        $respuesta["mensaje"] = array($response);

        // enviamos la respuesta
        echo json_encode($respuesta);

    }
   
?>