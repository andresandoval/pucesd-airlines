<?php
    
/*Author: Andres Sandoval
 * Date: May 2013
 * email: andresandoval992@gmail.com
 */
    require_once 'dbConnexion.php';
    require_once 'report.php';
    require_once 'calendar.php';
    require_once 'pilot.php';
    require_once 'plane.php';
    require_once 'user.php';
    require_once 'route.php';
    require_once 'reservation.php';
    require_once 'client.php';
    require_once 'invoice.php';
    require_once 'res_incumplidas.php';
    require_once 'event.php';
    
    class object {
        
        private $report = null;
        public function object(){}

//<editor-fold defaultstate="collapsed" desc="Utilitarios">
        public static function getBackButton(){
            echo "<div class='wrapper'><p>&nbsp;</p><button class='button2' onclick='window.history.back();'>Atras!</button></div>";
        }
        
        public function getIdResult($result){
            if($result == false) {
                echo "<div align=\"center\" class=\"invalidValue\">[ Valor duplicado, ya existe. ]</div>";
            }elseif($result == true){
                echo "<div align=\"center\" class=\"validValue\">[ OK ]</div>";
            }else{
                echo "<div align=\"center\" class=\"invalidValue\">[ ERROR ]</div>";
            }
        }
        
        public function getIdDetails($result){
            echo '<pre>';
            if(mysql_num_rows($result) != 1) {
                    echo '! No hay deatalles ¡';
            }else{
               $row = mysql_fetch_row($result, MYSQL_ASSOC);            
               while( list($key, $value) = each($row) ){
                   echo "[ <b>$key</b> : $value ]&nbsp;";
               }
            }
            echo '</pre>';
        }
        
        public function getNewButtons(){
            ?>
            <table  style="border:1px solid #999999;text-align: center;">
                <tr>                            
                    <td>
                        <button type="submit" class="noButton" title="Guardar">
                            <img src="./Images/CSS/save.png" class="button"/>
                        </button>                                
                    </td>
                    <td>
                        <button type="reset" class="noButton" title="Cancelar">
                            <img src="./Images/CSS/cancel.png" class="button"/>
                        </button>                                
                    </td>
                </tr>
            </table>
            <?php
        }
        
        public function getModifyButtons(){
            ?>
            <table  style="border:1px solid #999999;text-align: center;">
                <tr>                            
                    <td>
                        <button type="submit" class="noButton" title="Guardar Cambios">
                            <img src="./Images/CSS/save.png" class="button"/>
                        </button>
                    </td>
                    <td>
                        <button type="reset" class="noButton" title="Restablecer valores">
                            <img src="./Images/CSS/restore.png" class="button"/>
                        </button>                                
                    </td>
                </tr>
            </table>
            <?php
        }
        
        public function getNacionality($default = ''){
            $options = array("Ecuatoriana","Colombiana","Peruana","Chilena","Argentina","Otra");
            echo "<option selected value=''>-- Selecciona uno --</option>";
            for ($i = 0; $i < count($options); $i++){
                echo "<option value='$options[$i]'";
                echo ($default == $options[$i])? ' selected ':'';
                echo ">$options[$i]</option>";
            }
        }
        
        protected function getLookUpButtons($value, $arg = ''){
            echo '<img src="./Images/CSS/edit.png" class="lookUpButtons" onclick="modify_(\''.get_called_class().'\',\''.$value.'\');" title="Editar"/>';
            echo '<img src="./Images/CSS/delete.png" class="lookUpButtons" onclick="delete_(\''.get_called_class().'\', \''.$value.'\');" title="Eliminar"/>';
            echo '<img src="./Images/CSS/print.png" class="lookUpButtons" onclick="print_(\'individual\',\''.$value.'\',\''.get_called_class().'\');" title="Imprimir"/>';
        }
        
        protected function getDetails_(){}
//</editor-fold>       

//<editor-fold defaultstate="collapsed" desc="Basicas">
        
        public function new_(){}
        
        public function create_(){}
        
        public function lookUp_($filter){
            $token = 'general_'.get_called_class();
            $conn = dbConnexion::connect();
            if(get_called_class() == 'res_incumplidas'){
                $ss = oci_parse($conn, "begin actualizarReservaciones; end;");
                oci_execute($ss);
            }
            $stid = oci_parse($conn, "SELECT BUSCAR('$token', '$filter') AS BRC FROM dual");
            oci_execute($stid);
            if (!($row = oci_fetch_array($stid, OCI_ASSOC))) return;
            $rc = $row['BRC'];
            oci_execute($rc);
            $header = true;
            $row_style = 0;
            echo '<table class="lookUpTable">';
            while(($row_2 = oci_fetch_array($rc, OCI_NUM))){
                if ($header){
                    echo "<tr>";
                    for($i =1; $i<=count($row_2); $i++) echo '<th class="lookUpTableCell" style=" border-right: none;">'.oci_field_name($rc, $i)."</th>";
                    echo "</tr>";
                    $header = false;
                }
                echo "<tr class='lookUpTableRow$row_style'>";
                for($i =0; $i<count($row_2); $i++)echo "<td class='lookUpTableCell'>$row_2[$i]</td>";
                echo '<td class="lookUpTableCell">';
                echo (get_called_class() != 'reservation')? $this->getLookUpButtons($row_2[0]) : $this->getLookUpButtons($row_2[0], $row_2[5]);
                echo '</td></tr>';
                $row_style = ($row_style == 0)? 1: 0;
            }
            echo "</table>";
            oci_free_statement($stid);
            oci_close($conn);
            if ($header) echo '<div style="text-align: center;"><br/><h3>¡ No hay registros !</h3></div>';
        }
        
        public function modify_(){}
        
        public function saveChanges_(){}
        
        public function delete_($id){
            $token = get_called_class();
            $query = "begin :result_ := ELIMINAR('$token','$id'); end;";
            $conn = dbConnexion::connect();
            $s = oci_parse($conn, $query);
            oci_bind_by_name($s, ":result_", $return_value_, 100);
            oci_execute($s);
            oci_close($conn);
            echo ($return_value_ == 'TRUE')? 'ok': '¡ No se pudo eliminar el registro !';
        }
        
        public function print_($filter, $type_, $no_print=false){
            $token = $type_.'_'.  get_called_class();
            $individualTitle = array('user'=>"Usuario : $filter",'route' => "Ruta : $filter",'plane' => "Avion : $filter",'pilot' => "Piloto : $filter", 'reservation'=>"Reservaciones : $filter", 'res_incumplidas'=>"Reservaciones incumplidas: $filter", 'event'=>"Operacion  sobre ruta");
            $generalTitle = array('user'=>'Usuarios registrados','route' => 'Rutas registradas','plane' => 'Aviones registrados','pilot' => 'Pilotos registrados', 'reservation'=>"Reservaciones registadas", 'res_incumplidas'=>"Reservaciones incumplidas registradas" ,'event'=>"Operaciones sobre rutas");
            $query_ = "BUSCAR('$token','$filter')";
            $title_ = ($type_=='individual')? $individualTitle[get_called_class()] : $generalTitle[get_called_class()];
            $this->report = new report($type_, $query_, $title_, $no_print);
            unset($this->report);
        }
        
//</editor-fold>
 
    }
?>