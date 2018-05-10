<?php

/*Author: Andres Sandoval
 * Date: May 2013
 * email: andresandoval992@gmail.com
 */

    require_once 'object.php';
    use object;
    
    class plane extends object{
        
//<editor-fold defaultstate="collapsed" desc="Utilitarios">
        
        public function getPlaneDetails($id){
            $query = "begin :ut := GETDETALLES('planeDetails','$id'); end;";
            $conn = dbConnexion::connect();
            $s = oci_parse($conn, $query);
            oci_bind_by_name($s, ":ut", $return_value_, 100);
            oci_execute($s);
            oci_close($conn);
            echo '<pre>';
            echo ($return_value_ == '')? '! No hay deatalles ¡': $return_value_;
            echo '</pre>';
        }
        
        public function getIds(){            
            echo '<option selected value="">-- Selecciona uno --</option>';
            $conn = dbConnexion::connect();
            $s = oci_parse($conn, 'select avion.idAvion "ID_" from avion');
            oci_execute($s);
            while(oci_fetch($s)){
                $r = oci_result($s, 'ID_');
                echo '<option value="'.$r.'">'.$r.'</option>';
            }
            oci_close($conn);
        }
        
        public  function getDetails_($detailOf, $id){
            $query = "begin :ut := GETDETALLES('$detailOf','$id'); end;";
            $conn = dbConnexion::connect();
            $s = oci_parse($conn, $query);
            oci_bind_by_name($s, ":ut", $return_value_, 100);
            oci_execute($s);
            oci_close($conn);
            $this->getIdResult(($return_value_ == '')? true : false);
        }


//</editor-fold>        
 
//<editor-fold defaultstate="collapsed" desc="Basicas">
        
        public function new_(){
            ?>
            <div>
               <h2>Ingresar Datos de Aviones</h2>
               <form name="frmBody" action="javascript:create_('plane');" method="post" target="_self" onsubmit="return(confirmar());" onreset="return(confirmar());">                       
                    <table>
                        <tr>
                            <td>Codigo</td>
                            <td><input type="text" name="planeId" id="planeId" title="Codigo del Avion" required="required" value="" onchange="getDetails('plane','planeId');"/></td>
                            <td class='cellDetail' id="planeIdDetails"></td>
                        </tr>
                        <tr>
                            <td>Marca</td>
                            <td>
                                <input type="text" title="Marca del Avion" name="planeBrand" id="planeBrand" value="" required="required"/>
                            </td>
                        </tr>
                        <tr>
                            <td>Modelo</td>
                            <td>
                                <input type="text" title="Modelo del Avion" name="planeModel" id="planeModel" value="" required="required"/>
                            </td>
                        </tr>
                        <tr>
                            <td>Capacidad</td>
                            <td>
                                <input type="number" title="Capacidad del Avion" name="planeCapacity" id="planeCapacity" value="" required="required"/> 
                            </td>
                        </tr>
                        <tr>
                            <td>Observaciones</td>
                            <td><input type="text" title="Observaciones del Avion" name="planeObservations" id="planeObservations" required="required" value="Ninguna"/></td>
                        </tr>
                    </table>
                    <br/>
                    <hr/>
                    <?php $this->getNewButtons();?>
               </form>
            </div>
            <?php
        }
    
        public function create_(){
            $planeId = $_POST['planeId'];
            $planeBrand = $_POST['planeBrand'];
            $planeModel = $_POST['planeModel'];
            $planeCapacity = $_POST['planeCapacity'];
            $planeObservations = $_POST['planeObservations'];
            $query = "begin :result_ := RECORD_AVION('CREATE','$planeId', '$planeBrand', '$planeModel', '$planeCapacity', '$planeObservations'); end;";
            $conn = dbConnexion::connect();
            $s = oci_parse($conn, $query);
            oci_bind_by_name($s, ":result_", $return_value_, 100);
            oci_execute($s);
            oci_close($conn);
            echo ($return_value_ == 'TRUE')? 'ok': '¡ No se pudo crear el avion !';
        }
        
        public function modify_($who){
            $conn = dbConnexion::connect();
            $stid = oci_parse($conn, "SELECT BUSCAR('individual_plane', '$who') AS BRC FROM dual");
            oci_execute($stid);
            if (!($row_ = oci_fetch_array($stid, OCI_ASSOC))) return 'Ha ocurrido un error';
            $rc = $row_['BRC'];
            oci_execute($rc);
            if (!($row = oci_fetch_array($rc, OCI_NUM))){echo '<div style="text-align: center;"><br/><h3>¡ El registro buscado no existe !</h3></div>'; return;}
            ?>
            <div align="center">
                <h2>Edicion de Avión : <?php echo $who;?></h2>
                <form name='frmBody' id='frmBody' action="javascript:saveChanges_('plane');" method='post' target='_self' onsubmit='return confirmar();' onreset='return confirmar();'>                            
                    <input type='hidden' name='planeId' id='planeId' value='<?php echo $row[0];?>'/>
                    <table>
                        <tr>
                            <td>Codigo</td>
                            <td><?php echo $row[0];?></td>
                        </tr>
                        <tr>
                            <td>Marca</td>
                            <td><?php echo $row[1];?></td>
                        </tr>
                        <tr>
                            <td>Modelo</td>
                            <td><?php echo $row[2];?></td>
                        </tr>
                        <tr>
                            <td>Capacidad</td>
                            <td><?php echo $row[3];?></td>
                        </tr>
                        <tr>
                            <td>Observaciones</td>
                            <td><input type="text" title="Observaciones del Avion" name="planeObservations" id="planeObservations" required="required" value="<?php echo $row[4];?>"/></td>
                        </tr>
                    </table>
                    <br/>
                    <hr/>
                    <?php $this->getModifyButtons();?>
                </form>
            </div>
            <?php
        }
        
        public function saveChanges_(){
            $planeId = $_POST['planeId'];
            $planeObservations = $_POST['planeObservations'];
            
            $query = "begin :result_ := RECORD_AVION('MODIFY','$planeId', NULL, NULL, NULL, '$planeObservations'); end;";
            $conn = dbConnexion::connect();
            $s = oci_parse($conn, $query);
            oci_bind_by_name($s, ":result_", $return_value_, 100);
            oci_execute($s);
            oci_close($conn);
            echo ($return_value_ == 'TRUE')? 'ok': '¡ No se pudieron guardar los cambios del avión !';
        }
                
//</editor-fold>
        
    }

?>
