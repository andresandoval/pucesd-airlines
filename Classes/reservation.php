<?php
/*Author: Andres Sandoval
 * Date: June 2013
 * email: andresandoval992@gmail.com
 */
    //require_once 'client.php';
    require_once 'object.php';
    
    
    class reservation extends object{
        
        public function reservation(){}
        
        public function getLookUpButtons($value, $invoiced){
            echo '<img src="./Images/CSS/print.png" class="lookUpButtons" onclick="print_(\'individual\',\''.$value.'\',\''.get_called_class().'\');" title="Imprimir reservacion"/>';
            echo ($invoiced == 'No facturada') ? '<img src="./Images/CSS/invoice.png" class="lookUpButtons" onclick="newInvoice_(\''.$value.'\');" title="Facturar"/>': '<img src="./Images/CSS/clear.png" class="lookUpButtons" title="Esta reservacion ya ha sido facturada"/>';
            echo ($invoiced == 'No facturada') ? '<img src="./Images/CSS/clear.png" class="lookUpButtons" title="Esta reservacion aun no ha sido facturada"/>':'<img src="./Images/CSS/print_ticket.png" class="lookUpButtons" onclick="print_(\'ticket\',\''.$value.'\',\'invoice\');" title="Imprimir ticket de abordaje"/>';
            echo ($invoiced == 'No facturada') ? '<img src="./Images/CSS/clear.png" class="lookUpButtons" title="Esta reservacion aun no ha sido facturada"/>':'<img src="./Images/CSS/print_invoice.png" class="lookUpButtons" onclick="print_(\'invoice\',\''.$value.'\',\'invoice\');" title="Imprimir factura"/>';
        }
        
        public function new_(){
            $client = new client();
            $client->login();
            unset($client);
        }
        
        public static function sumary_(){
            $clientId = $_POST['idClient'];
            $routeId = $_POST['routeId'];
            $requireButtocks = $_POST['requireButtocks'];
            echo '<h2 align="center">¡ Bienvenido !</h2>';
            client::sumary_($clientId);
            route::sumary_($routeId);
            echo<<<SUMARY
            <style type="text/css">
                .sumary_reservation{
                    margin-top: 20px;
                    margin-bottom: 10px;
                    border: 0px;
                    border-radius: 6px;
                    background: #bbaacc;
                    width: 50%;
                    padding: 10px;
                }
            </style>
            <div align="center" style="margin-bottom: 10px;">
                <div align="center" class="sumary_reservation">
                    <b><pre>Datos de la reservacion</pre></b>
                    <table class="lookUpTable">
                        <tr><th class="lookUpTableCell" style="text-align: right;">Numero de asientos a reservar |</th><td class="lookUpTableCell">$requireButtocks</td></tr>
                    </table>
                </div>
                <form action="." method="post" target="_self" onsubmit="return confirm('¿ Estas seguro/a ?');">
                    <button type="submit" class="buttonWolf" name="token" value="create_reservation">RESERVAR</button>
                    <input type="hidden" name="routeId" value="$routeId"/>
                    <input type="hidden" name="clientId" value="$clientId"/>
                    <input type="hidden" name="requireButtocks" value="$requireButtocks"/>
                </form>
            </div>
SUMARY;
        }
        
        public function create_(){
            if (!isset($_POST['clientId']) || !isset($_POST['routeId']) || !isset($_POST['requireButtocks']))header('location: .');
            $clientId = $_POST['clientId'];
            $routeId = $_POST['routeId'];
            $requireButtocks = $_POST['requireButtocks'];
            $conn = dbConnexion::connect();
			$s = oci_parse($conn, "begin :result_ := crearReservacion('$routeId', '$clientId','$requireButtocks'); end;");
			oci_bind_by_name($s, ":result_", $return_value_, 100);
			oci_execute($s);
			oci_close($conn);
			return ($return_value_ != 'FALSE')? $return_value_ : false;
        }
        
        public function create_result_($success, $res_code = ''){
            if ($success) 
                echo<<<DIV
                <div class="noPrint" align="center">
                    <h2>Felicidades, la reservacion se ha creado</h2>
                    <style type="text/css">
                        .sumary_reservation{
                            font-family: arial;
                            font-size: 20px;
                            color: #000000;
                            margin-top: 20px;
                            margin-bottom: 10px;
                            border: 0px;
                            border-radius: 6px;
                            background: #bbaacc;
                            width: 50%;
                            padding: 10px;
                        }
                    </style>
                <div class="sumary_reservation">
                    Tu codigo de reservacion es <div class="buttonWolf2">$res_code</div><br/>
                    Recuerda que puedes revisar tu reservacion siempre que quieras desde nuestra pagina principal.<br/>                                          
                </div>
                </div>
DIV;
            else 
                echo '<h3>Ha ocurrido un error inesperado, por favor intenta nuevamente</h3><br/>';
        }
        
    }
    
    

?>
