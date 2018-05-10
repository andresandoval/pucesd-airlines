<?php
/*Author: Andres Sandoval
 * Date: May 2013
 * email: andresandoval992@gmail.com
 */
    require_once 'object.php';
    use object;
    
    class invoice extends object {
        public function invoice(){}
        
        public function new_($idReservation){
            $res = new reservation();
            ?>
            <div>
                <h2>Facturacion</h2>
                <?php $res->print_($idReservation, 'individual', true);?>
                <form name="frmBody" action="javascript:createInvoice_();" method="post" target="_self" onsubmit="return(confirmar());" onreset="return(confirmar());">
                    <input type="hidden" name="idReservation" id="idReservation" value="<?php echo $idReservation;?>"/>
                    <br/>
                    <table>
                        <tr>
                            <th>Codigo de deposito bancario</th>
                            <td><input type="text" name="bankTichet" id="bankTichet" required="required" value=""/></td>
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
            $bankTichet = $_POST['bankTichet'];
            $idReservation = $_POST['idReservation'];
            
            $query = "begin :result_ := RECORD_FACTURA('$idReservation', '$bankTichet'); end;";
            $conn = dbConnexion::connect();
            $s = oci_parse($conn, $query);
            oci_bind_by_name($s, ":result_", $return_value_, 100);
            oci_execute($s);
            oci_close($conn);
            echo ($return_value_ == 'TRUE')? 'ok': '¡ No se pudo crear el factura !';
        }
        
        public function print_($id, $type){
            $token = '';
            $title = '';
            if ($type == 'invoice'){
                $token = "factura";
                $title = 'Factura : '.$id;
            }elseif($type == 'ticket'){
                $token = "ticket";
                $title = 'Ticket de Abordaje';
            }
            $token = 'individual_'.$token;
            $query = "BUSCAR('$token','$id')";
            $report = new report('individual', $query, $title);
            unset($report);
        }
    }

?>
