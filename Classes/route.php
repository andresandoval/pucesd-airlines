<?php

/*Author: Andres Sandoval
 * Date: May 2013
 * email: andresandoval992@gmail.com
 */

    require_once 'object.php';
    use object;
    
    class route extends object{
        
        private $pilot;
        private $plane;
        
        public function route(){
            $this->pilot = new pilot();
            $this->plane = new plane();
        }
//<editor-fold defaultstate="collapsed" desc="Utilitarios">
        
        private function checkId($id){
            $query = "begin :ut := GETDETALLES('routeId','$id'); end;";
            $conn = dbConnexion::connect();
            $s = oci_parse($conn, $query);
            oci_bind_by_name($s, ":ut", $return_value_, 100);
            oci_execute($s);
            oci_close($conn);
            $this->getIdResult(($return_value_ == '')? true : false);
        }
        
        private function getDestiny(){
            $destiny = array("Quito","Cuenca","Guaranda","Azogues","Riobamba","Tulcan","Latacunga","Machala","Esmeraldas","Puerto Baquerizo Moreno","Guayaquil","Imbarra","Loja","Babahoyo","Portoviejo","Macas","Tena","Nueva loja","Puyo","Santa Elena","Orellana","Ambato","Zamora");
            echo '<option selected value="">-- Selecciona uno --</option>';
            for($i = 0; $i < count($destiny); $i++){
                echo "<option value='$destiny[$i]'>$destiny[$i]</option>";
            }
        }
        
        private static function getDepartureSidewalk($max, $default = ''){
            echo '<option selected value="">-- Selecciona uno --</option>';
            for($i = 1; $i <= $max; $i++){
                echo "<option value='$i'";
                echo ($default == $i)? ' selected ' : '';
                echo ">$i</option>";
            }
        }
        
        public function getDetails_($detailOf, $id){
            if($detailOf == 'routeId') $this->checkId($id);
            elseif($detailOf == 'planeId') $this->plane->getPlaneDetails($id);
            elseif($detailOf == 'pilotCi') $this->pilot->getPilotDetails($id);
        }
        
        public function getLookUpButtons($value){
            echo '<img src="./Images/CSS/edit.png" class="lookUpButtons" onclick="modify_(\''.get_called_class().'\',\''.$value.'\');" title="Editar"/>';
            echo ($_SESSION['logedUserType'] != 'Gerente')? '<img src="./Images/CSS/delete.png" class="lookUpButtons" onclick="delete_(\''.get_called_class().'\', \''.$value.'\');" title="Eliminar"/>' : '';
            echo '<img src="./Images/CSS/print.png" class="lookUpButtons" onclick="print_(\'individual\',\''.$value.'\',\''.get_called_class().'\');" title="Imprimir"/>';
        }
        
        
//</editor-fold>
        
// <editor-fold defaultstate="collapsed" desc="Basicas">
        
        public function new_(){
            ?>
            <div>
               <h2>Ingresar Datos de Rutas</h2>
               <form name="frmBody" id="frmBody" action="javascript:create_('route');" method="post" target="_self" onsubmit="return confirmar();" onreset="return confirmar();">
                    <table>
                        <tr>
                            <td>Codigo de Ruta</td>
                            <td><input type="text" name="routeId" id="routeId" title="Codigo de Ruta" required="required" value="" onchange="getDetails('route','routeId');"/></td>
                            <td class='cellDetail' id="routeIdDetails"></td>
                        </tr>
                        <tr>
                            <td>Codigo de Avion</td>
                            <td>
                                <select title="Codigo de avion" name="planeId" id="planeId" onchange="getDetails('route','planeId');" required="required">
                                   <?php $this->plane->getIds(); ?>
                                </select>
                            </td>
                            <td class='cellDetail' id="planeIdDetails"></td>
                        </tr>
                        <tr>
                            <td>CI del Piloto</td>
                            <td>
                                <select title="CI del Piloto" name="pilotCi"  id="pilotCi" onchange="getDetails('route','pilotCi');" required="required">
                                    <?php $this->pilot->getIds(); ?>
                                </select>
                            </td>
                            <td class='cellDetail' id="pilotCiDetails"></td>
                        </tr>
                        <tr>
                            <td>Destino</td>
                            <td>
                                <select title="Destino" name="routeDestiny" id="routeDestiny" required="required">
                                    <?php $this->getDestiny();?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Fecha y hora de Salida</td>
                            <td>
                                <?php 
                                    $cal = new calendar_('routeDepartureDateTime','2013', '2014', 'Salida de Ruta',true);
                                    unset($cal);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Anden de Salida</td>
                            <td>
                                <select title="Anden de Salida" name="routeDepartureSidewalk" id="routeDepartureSidewalk" required="required">
                                    <?php $this->getDepartureSidewalk(15);?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Precio Asignado</td>
                            <td>
                                <input type="number" title="Precio Asignado" name="routePrice" id="routePrice" value="00" required="required"/>USD
                            </td>
                        </tr>
                    </table>
                    <br/>
                    <hr/>
                    <?php $this->getNewButtons();?>
               </form>
            </div>
            <?php
            unset($this->pilot);
            unset($this->plane);
        }
    
        public function create_(){
            $routeId = $_POST['routeId'];
            $planeId = $_POST['planeId'];
            $pilotCi = $_POST['pilotCi'];
            $routeDestiny = $_POST['routeDestiny'];
            $routeDepartureDateTime = $_POST['routeDepartureDateTime'];
            $routeDepartureDateTime = "to_date('$routeDepartureDateTime', 'YYYY-MM-DD HH24:MI:SS')";
            $routeDepartureSidewalk = $_POST['routeDepartureSidewalk'];
            $routePrice = $_POST['routePrice'];
            $query = "begin :result_ := RECORD_RUTA('CREATE','$routeId','$planeId',$pilotCi,'$routeDestiny',$routeDepartureDateTime,'$routeDepartureSidewalk','$routePrice'); end;";
            $conn = dbConnexion::connect();
            $s = oci_parse($conn, $query);
            oci_bind_by_name($s, ":result_", $return_value_, 100);
            oci_execute($s);
            oci_close($conn);
            echo ($return_value_ == 'TRUE')? 'ok': "¡ No se pudo crear la ruta !\n\n$return_value_";			
        }
        
        public function modify_($who){
            $manager = ($_SESSION['logedUserType'] == 'Gerente')? true: false;
            $conn = dbConnexion::connect();
            $stid = oci_parse($conn, "SELECT BUSCAR('individual_route', '$who') AS BRC FROM dual");
            oci_execute($stid);
            if (!($row_ = oci_fetch_array($stid, OCI_ASSOC))) return 'Ha ocurrido un error';
            $rc = $row_['BRC'];
            oci_execute($rc);
            if (!($row = oci_fetch_array($rc, OCI_NUM))){echo '<div style="text-align: center;"><br/><h3>¡ El registro buscado no existe !</h3></div>'; return;}
            ?>
            <div align="center">
                <h2>Edicion de Ruta : <?php echo $who;?></h2>
                <form name='frmBody' id='frmBody' action="javascript:saveChanges_('route');" method='post' target='_self' onsubmit='return confirmar();' onreset='return confirmar();'>                            
                    <input type='hidden' name='routeId' id='routeId' value='<?php echo $row[0];?>'/>
                    <table>
                        <tr>
                            <td>Codigo de Ruta</td><td><?php echo $row[0];?></td>
                        </tr>
                        <tr>
                            <td>Codigo de Avion</td><td><?php echo $row[1];?></td>
                        </tr>
                        <tr>
                            <td>CI del Piloto</td><td><?php echo $row[2];?></td>
                        </tr>
                        <tr>
                            <td>Destino</td><td><?php echo $row[3];?></td>
                        </tr>
                        <tr>
                            <td>Fecha y hora de Salida</td><td><?php echo $row[4];?></td>
                        </tr>
                        <tr>
                            <td>Anden de Salida</td>
                            <td><?php if (!$manager){?>
                                <select title="Anden de Salida" name="routeDepartureSidewalk" id="routeDepartureSidewalk" required="required">
                                    <?php route::getDepartureSidewalk(15,$row[5] );?>
                                </select>
                                <?php }else echo $row[5];?>
                            </td>
                            
                        </tr>
                        <tr>
                            <td>Nro. lugares disponibles</td><td><?php echo $row[6];?></td>
                        </tr>
                        <tr>
                            <td>Precio Asignado</td>
                            <td><?php echo (!$manager)? $row[7] : '<input type="number" title="Precio Asignado" name="routePrice" id="routePrice" value="'.$row[7].'" required="required"/>USD';?></td>
                        </tr>
                    </table>
                    <br/>
                    <hr/>
                    <input type="hidden" name="routeDepartureSidewalk" value="<?php echo $row[5];?>"/>
                    <input type="hidden" name="routePrice" value="<?php echo $row[7];?>"/>

                    <?php $this->getModifyButtons();?>
                </form>
            </div>
            <?php
        }
        
        public function saveChanges_(){            
            $routeId = $_POST['routeId'];
            $routePrice = $_POST['routePrice'];            
            $routeDepartureSidewalk = $_POST['routeDepartureSidewalk'];
            $query = "begin :result_ := RECORD_RUTA('MODIFY','$routeId',NULL,NULL,NULL,NULL,'$routeDepartureSidewalk','$routePrice'); end;";
            $conn = dbConnexion::connect();
            $s = oci_parse($conn, $query);
            oci_bind_by_name($s, ":result_", $return_value_, 100);
            oci_execute($s);
            oci_close($conn);
            echo ($return_value_ == 'TRUE')? 'ok': '¡ No se pudieron guardar los cambios de la ruta !';
        }
        
//</editor-fold>
     
//CLIENTE
        private static function getLookUpViewButton($id, $queryButtocks, $requireButtocks){
            echo ($queryButtocks >= $requireButtocks)? "<a href='?token=detailLookUp&routeId=$id&requireButtocks=$requireButtocks'><button style='border: 1px solid #454545;'>Ver</button></a>":"No hay $requireButtocks lugares dispoibles";
        }
        
        public static function lookUpByClient(){
            if (!isset($_GET['destiny']) || !isset($_GET['departureDate']) || !isset($_GET['passengers']) )header('location: .');
            $destiny = $_GET['destiny'];
            $departureDate = $_GET['departureDate'];
			$departureDate = "to_date('$departureDate', 'YYYY-MM-DD')";
            $passengers_require = $_GET['passengers'];
			$conn = dbConnexion::connect();
            $stid = oci_parse($conn, "SELECT buscarRutasCliente('$destiny', $departureDate) AS BRC FROM dual");
            oci_execute($stid);
            $row = oci_fetch_array($stid, OCI_ASSOC);
            $rc = $row['BRC'];
            oci_execute($rc);
            $header = true;
            $row_style = 0;
			echo '<div align="center" style="margin-top: 50px;margin-bottom: 100px;">';
			echo '<h3>Se encontro las siguentes coincidencias</h3><br/>';
            echo '<table class="lookUpTable">';
            while(($row_2 = oci_fetch_array($rc, OCI_NUM))){
                if ($header){
                    echo "<tr>";
                    for($i =2; $i<=count($row_2); $i++) echo '<th class="lookUpTableCell" style=" border-right: none;">'.oci_field_name($rc, $i)."</th>";
                    echo "</tr>";
                    $header = false;
                }
                echo "<tr class='lookUpTableRow$row_style'>";
                for($i =1; $i<count($row_2); $i++)echo "<td class='lookUpTableCell'>$row_2[$i]</td>";
                echo '<td class="lookUpTableCell">';
                echo self::getLookUpViewButton($row_2[0], $row_2[3], $passengers_require);
                echo '</td></tr>';
                $row_style = ($row_style == 0)? 1: 0;
			}
            echo "</table></div>";
			oci_free_statement($stid);
            oci_close($conn);
            if ($header) echo '<div style="text-align: center;"><br/><h3>¡ NINGUNA !</h3></div>';
			self::getBackButton();
        }
        
        public static function lookUpByClientDetail(){
            if (!isset($_GET['routeId']) || !isset($_GET['requireButtocks']))header('location: .');
            $routeId = $_GET['routeId'];
            $requireButtocks = $_GET['requireButtocks'];
			$query = "select BUSCAR('route_by_client', '$routeId') as RRC from dual";
			
			$conn = dbConnexion::connect();
            $stid = oci_parse($conn,$query);
            oci_execute($stid);
            if (!($row = oci_fetch_array($stid, OCI_ASSOC))) echo 'Ha ocurrido un error';
            $rc = $row['RRC'];
            oci_execute($rc);
			echo '<div align="center" style="margin-top: 50px;margin-bottom: 100px;">';
            if (!($row_2 = oci_fetch_array($rc, OCI_ASSOC)))echo '<div style="text-align: center;"><br/><h3>¡ No hay registros !</h3></div>';
            echo '<table>';
            while( list($key, $value) = each($row_2) ){
				echo "<tr><th class='lookUpTableCell' style='text-align: right;'>$key |</th><td class='lookUpTableCell'>$value</td></tr>";
            }
            echo "</table>
                <form action='.' method='post' target='_self'>
                    <button type='submit' class='buttonWolf' name='token' value='new_reservation'>RESERVAR ESTA RUTA</button>
                    <input type='hidden' name='routeId' value='$routeId'/>
                    <input type='hidden' name='requireButtocks' value='$requireButtocks'/>
                </form>
            </div>";
            self::getBackButton();
        }

        public static function sumary_($id){
        echo<<<SUMARY
        <style type="text/css">
            .sumary_route{
                margin-top: 20px;
                margin-bottom: 10px;
                border: 0px;
                border-radius: 6px;
                background: #ccaabb;
                width: 50%;
                padding: 10px;
            }
        </style>
        <div align="center">
        <div align="center" class="sumary_route">
            <b><pre>Datos de la ruta</pre></b>
            <table class="lookUpTable">
SUMARY;
        $conn = dbConnexion::connect();
		$stid = oci_parse($conn, "select BUSCAR('route_by_client','$id') as RRC from dual");
		oci_execute($stid);
		if (!($row = oci_fetch_array($stid, OCI_ASSOC))) echo 'Ha ocurrido un error';
		$rc = $row['RRC'];
		oci_execute($rc);
		if (!($row_2 = oci_fetch_array($rc, OCI_ASSOC)))echo '<div style="text-align: center;"><br/><h3>¡ No hay registros !</h3></div>';
		$queryResults = '<table>';
		while( list($key, $value) = each($row_2) ){
			echo "<tr><th class='lookUpTableCell' style='text-align: right;'>$key |</th><td class='lookUpTableCell'>$value</td></tr>";
		}

        echo '</table></div></div>';

    }
    }

?>
