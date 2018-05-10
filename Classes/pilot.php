<?php

/*Author: Andres Sandoval
 * Date: May 2013
 * email: andresandoval992@gmail.com
 */
    require_once 'object.php';
    use object;

    class pilot extends object{

//<editor-fold defaultstate="collapsed" desc="Utilitarios">

        function getDetails_($detailOf, $id){
            $query = "begin :ut := GETDETALLES('$detailOf','$id'); end;";
            $conn = dbConnexion::connect();
            $s = oci_parse($conn, $query);
            oci_bind_by_name($s, ":ut", $return_value_, 100);
            oci_execute($s);
            oci_close($conn);
            $this->getIdResult(($return_value_ == '')? true : false);
        }
        
        public function getPilotDetails($id){
            $query = "begin :ut := GETDETALLES('pilotDetails','$id'); end;";
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
            $s = oci_parse($conn, 'select piloto.ciPiloto "CI_" from piloto');
            oci_execute($s);
            while(oci_fetch($s)){
                $r = oci_result($s, 'CI_');
                echo '<option value="'.$r.'">'.$r.'</option>';
            }
            oci_close($conn);
        }
       
//</editor-fold>

//<editor-fold defaultstate="collapsed" desc="Basicos">
        public function new_(){
            ?>
            <div>
               <h2>Ingresar Datos de Pilotos</h2>
               <form name="frmBody" action="javascript:create_('pilot');" method="post" target="_self" onsubmit="return(confirmar());" onreset="return(confirmar());">                       
                    <table>
                        <tr>
                            <td>CI</td>
                            <td><input type="number" name="pilotCi" id="pilotCi" title="CI del Piloto" required="required" value="" onchange="getDetails('pilot','pilotCi');"/></td>
                            <td class="cellDetail" id="pilotCiDetails"></td>
                        </tr>
                        <tr>
                            <td>Nombres</td>
                            <td>
                                <input type="text" title="Nombres del Piloto" name="pilotName" id="pilotName" value="" required="required"/>
                            </td>
                        </tr>
                        <tr>
                            <td>Apellidos</td>
                            <td>
                                <input type="text" title="Apellidos del Piloto" name="pilotLastName" id="pilotLastName" value="" required="required"/>
                            </td>
                        </tr>
                        <tr>
                            <td>Fecha de Nacimiento</td>
                            <td>
                                <?php $cal = new calendar_('pilotBirthDate','1990','2013','Fecha de Nacimiento del Piloto');?>
                            </td>
                        </tr>
                        <tr>
                            <td aling="right">Nacionalidad</td><td>
                                <select name="pilotNationality" id="pilotNationality" title="Nacionalidad del Piloto" required="required">
                                    <?php $this->getNacionality()?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>E-Mail</td>
                            <td><input type="email" title="Email del Piloto" name="pilotEmail" id="pilotEmail" required="required" value=""/></td>
                        </tr>
                        <tr>
                            <td>Telefono</td>
                            <td><input type="number" title="Numero telefonico del Piloto" name="pilotPhoneNumber" id="pilotPhoneNumber" required="required" value=""/></td>
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
            $pilotCi = $_POST['pilotCi'];
            $pilotName = $_POST['pilotName'];
            $pilotLastName = $_POST['pilotLastName'];
            $pilotBirthDate = $_POST['pilotBirthDate'];
            $pilotBirthDate = "to_date('$pilotBirthDate', 'YYYY-MM-DD')";
            $pilotNationality = $_POST['pilotNationality'];
            $pilotEmail = $_POST['pilotEmail'];
            $pilotPhoneNumber = $_POST['pilotPhoneNumber'];
            $query = "begin :result_ := RECORD_PILOTO('CREATE','$pilotCi','$pilotName','$pilotLastName',$pilotBirthDate,'$pilotNationality', '$pilotEmail', '$pilotPhoneNumber'); end;";
            $conn = dbConnexion::connect();
            $s = oci_parse($conn, $query);
            oci_bind_by_name($s, ":result_", $return_value_, 100);
            oci_execute($s);
            oci_close($conn);
            echo ($return_value_ == 'TRUE')? 'ok': '¡ No se pudo crear el usuario !';
        }
        
        public function modify_($who){
            $conn = dbConnexion::connect();
            $stid = oci_parse($conn, "SELECT BUSCAR('individual_pilot', '$who') AS BRC FROM dual");
            oci_execute($stid);
            if (!($row_ = oci_fetch_array($stid, OCI_ASSOC))) return 'Ha ocurrido un error';
            $rc = $row_['BRC'];
            oci_execute($rc);
            if (!($row = oci_fetch_array($rc, OCI_NUM))){echo '<div style="text-align: center;"><br/><h3>¡ El registro buscado no existe !</h3></div>'; return;}
            ?>
            <div align="center">
                <h2>Edicion de Avión : <?php echo $who;?></h2>
                <form name='frmBody' id='frmBody' action="javascript:saveChanges_('pilot');" method='post' target='_self' onsubmit='return confirmar();' onreset='return confirmar();'>                            
                    <input type='hidden' name='pilotCi' id='pilotCi' value='<?php echo $row[0];?>'/>
                    <table>
                        <tr>
                            <td>CI</td><td><?php echo $row[0];?></td>
                        </tr>
                        <tr>
                            <td>Nombres</td>
                            <td>
                                <input type="text" title="Nombres del Piloto" name="pilotName" id="pilotName" value="<?php echo $row[1];?>" required="required"/>
                            </td>
                        </tr>
                        <tr>
                            <td>Apellidos</td>
                            <td>
                                <input type="text" title="Apellidos del Piloto" name="pilotLastName" id="pilotLastName" value="<?php echo $row[2];?>" required="required"/>
                            </td>
                        </tr>
                        <tr>
                            <td>Fecha de Nacimiento</td>
                            <td>
                                <?php echo $row[3];?>
                            </td>
                        </tr>
                        <tr>
                            <td aling="right">Nacionalidad</td><td>
                                <select name="pilotNationality" id="pilotNationality" title="Nacionalidad del Piloto" required="required">
                                    <?php $this->getNacionality($row[4])?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>E-Mail</td>
                            <td><input type="email" title="Email del Piloto" name="pilotEmail" id="pilotEmail" required="required" value="<?php echo $row[5];?>"/></td>
                        </tr>
                        <tr>
                            <td>Telefono</td>
                            <td><input type="number" title="Numero telefonico del Piloto" name="pilotPhoneNumber" id="pilotPhoneNumber" required="required" value="<?php echo $row[6];?>"/></td>
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
            $pilotCi = $_POST['pilotCi'];
            $pilotName = $_POST['pilotName'];
            $pilotLastName = $_POST['pilotLastName'];
            $pilotNationality = $_POST['pilotNationality'];
            $pilotEmail = $_POST['pilotEmail'];
            $pilotPhoneNumber = $_POST['pilotPhoneNumber'];
            $query = "begin :result_ := RECORD_PILOTO('MODIFY','$pilotCi','$pilotName','$pilotLastName',NULL,'$pilotNationality', '$pilotEmail', '$pilotPhoneNumber'); end;";
            $conn = dbConnexion::connect();
            $s = oci_parse($conn, $query);
            oci_bind_by_name($s, ":result_", $return_value_, 100);
            oci_execute($s);
            oci_close($conn);
            echo ($return_value_ == 'TRUE')? 'ok': '¡ No se pudieron guardar los cambios del piloto !';            
        }
//</editor-fold>
    }
?>
