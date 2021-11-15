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
            
            /*
            // insertamos el encabezado de compras
            $sqlInsertarEncabezadoCompra = $conxion ->prepare(" INSERT INTO COMPC01(TIP_DOC,CVE_DOC,CVE_CLPV,STATUS,SU_REFER,FECHA_DOC,FECHA_REC,FECHA_PAG,CAN_TOT,IMP_TOT1,IMP_TOT2,IMP_TOT3,IMP_TOT4,DES_TOT,DES_FIN,TOT_IND,OBS_COND,CVE_OBS,NUM_ALMA,ACT_CXP,ACT_COI,ENLAZADO,TIP_DOC_E,NUM_MONED,TIPCAMB,NUM_PAGOS,FECHAELAB,SERIE,FOLIO,CTLPOL,ESCFD,CONTADO,BLOQ,DES_FIN_PORC,DES_TOT_PORC,IMPORTE,TIP_DOC_ANT,DOC_ANT)
            VALUES('c','$encabezadoDocumento','$EncabezadoProveedor','$EncabezadoEstado','','$encabezadoFecha','$encabezadoFecha','$encabezadoFecha',$encabezadoSubTotal,0,0,0,$encabezadoImpuesto,0,0,0,'',0,$encabezadoAlmacen,'S','N','O','O',1,1,1,$encabezadoFechaHora,'',0,0,'N','N','N',0,0,$encabezadoTotal,'','') ");                                    
            $sqlInsertarEncabezadoCompra ->execute();          
            
            // insertamos los campos libres de compras
            $sqlInsertarEncabezadoComprasClib = $conxion ->prepare(" INSERT INTO COMPC_CLIB01(CLAVE_DOC)
            VALUES('$encabezadoDocumento') ");                                    
            $sqlInsertarEncabezadoComprasClib ->execute();                                                

            // insertamos el encabezado de cuentas por pagar
            $sqlInsertarEncabezadoCuentasPorPagar = $conxion ->prepare(" INSERT INTO PAGA_M01 (CVE_PROV,REFER,NUM_CARGO,NUM_CPTO,CVE_OBS,NO_FACTURA,DOCTO,IMPORTE,FECHA_APLI,FECHA_VENC,AFEC_COI,NUM_MONED,TCAMBIO,IMPMON_EXT,FECHAELAB,TIPO_MOV,SIGNO,USUARIO,STATUS)
            VALUES('$EncabezadoProveedor','$encabezadoDocumento',1,1,0,'','$encabezadoDocumento',$encabezadoTotal,'$encabezadoFecha','$encabezadoFecha','A',1,1,$encabezadoTotal,$encabezadoFechaHora,'C',1,0,'A') ");                                    
            $sqlInsertarEncabezadoCuentasPorPagar ->execute();

            // actualizamos el saldo del proveedor
            $sqlActualizarSaldoProveedor = $conxion ->prepare(" UPDATE PROV01 SET SALDO = SALDO+$encabezadoTotal WHERE CLAVE = '$EncabezadoProveedor' ");                                    
            $sqlActualizarSaldoProveedor ->execute();     
            */       
            
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

                /*
                // obtenemos el siguiente numero de movimiento para la partida
                $queryObtenerNumeroMovimiento = $conxion ->prepare(" SELECT ULT_CVE+1 AS NumeroMovimiento FROM TBLCONTROL01 WHERE ID_TABLA = 44 ");
                // ejecutamos la consulta
                $queryObtenerNumeroMovimiento ->execute();
                // recorremos la consulta y convertimos en Array                      
                $numeroMovimiento = $queryObtenerNumeroMovimiento->fetchAll(PDO::FETCH_ASSOC);                
                foreach ($numeroMovimiento AS $valor) {
                    $numMovSig = $valor["NumeroMovimiento"];                    
                }

                // insertamos la partida de compras
                $sqlInsertarPartidaCompras = $conxion ->prepare(" INSERT PAR_COMPC01(CVE_DOC,NUM_PAR,CVE_ART,CANT,PXR,PREC,COST,IMPU1,IMPU2,IMPU3,IMPU4,IMP1APLA,IMP2APLA,IMP3APLA,IMP4APLA,TOTIMP1,TOTIMP2,TOTIMP3,TOTIMP4,DESCU,ACT_INV,TIP_CAM,UNI_VENTA,TIPO_ELEM,TIPO_PROD,CVE_OBS,REG_SERIE,E_LTPD,FACTCONV,COST_DEV,NUM_ALM,MINDIRECTO,NUM_MOV,TOT_PARTIDA,MAN_IEPS,APL_MAN_IMP,CUOTA_IEPS,APL_MAN_IEPS,MTO_PORC,MTO_CUOTA,CVE_ESQ)
                VALUES('$detalleDocumento',$detalleFilaNumero,'$detalleProducto',$detalleCantidad,$detalleCantidad,0,$detallePrecio,0,0,0,0,6,6,6,4,0,0,0,0,0,'S',1,'pz','N','P',0,0,0,1,$detallePrecio,$detalleAlmacen,0,$numMovSig,$detalleTotalPartida,'N',1,0,'C',0,0,3) ");                                    
                $sqlInsertarPartidaCompras ->execute();        
                
                */

            }
            
        }
        
        // preparamos el mensaje como objeto json
        $response["respuestacompras"] = "Datos insert";
        // asignamos un tag y convertimos el mensaje en un array
        $respuesta["mensaje"] = array($response);

        // enviamos la respuesta
        echo json_encode($respuesta);

    }
   
?>