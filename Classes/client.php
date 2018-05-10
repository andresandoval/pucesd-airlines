<?php

/*Author: Andres Sandoval
 * Date: June 2013
 * email: andresandoval992@gmail.com
 */
require_once 'object.php';
class client extends object{
    
    public function client(){}
    
    public function new_(){
            ?>
            <div align="center" style="margin:20px; background: #cccccc; border:0px; border-radius: 10px; padding: 10px;">
                <h2>Registro de usuario</h2>
                <form action="." method="post" target="_self" onsubmit="return confirm('¿ Seguro/a ?');" onreset="return confirm('¿ Seguro/a ?');">
                    <input type="hidden" name="token" value="createClient"/>
                    <table class="lookUpTable" style="border: 15px solid #ffffff; border-radius: 10px;">
                        <tr>
                            <th class="lookUpTableCell" style="text-align: right;">Numero de CI</th>
                            <td class="lookUpTableCell"><input type="text" name="ciClient" style="width: 190px; height: 17px;border: 1px solid #999999;" value="" required="required"/></td>
                        </tr>
                        <tr>
                            <th class="lookUpTableCell" style="text-align: right;">Nombres</th>
                            <td class="lookUpTableCell"><input type="text" name="nameClient" style="width: 190px; height: 17px;border: 1px solid #999999;" value="" required="required"/></td>
                        </tr>
                        <tr>
                            <th class="lookUpTableCell" style="text-align: right;">Apellidos</th>
                            <td class="lookUpTableCell"><input type="text" name="appClient" style="width: 190px; height: 17px;border: 1px solid #999999;" value="" required="required"/></td>
                        </tr>
                        <tr>
                            <th class="lookUpTableCell" style="text-align: right;">e-mail</th>
                            <td class="lookUpTableCell"><input type="email" name="mailClient" style="width: 190px; height: 17px;border: 1px solid #999999;" value="" required="required"/></td>
                        </tr>
                    </table>
                    <br/>
                    <br/>
                    <?php $this->getNewButtons();?>
                </form>
                <pre style="color: red; font-size: 20px;"><?php echo (!$error)? '': "[ $error ]";?></pre>
            </div>
            <?php
    }
    
    public function create_(){
        $ciClient = $_POST['ciClient'];
        $nameClient = $_POST['nameClient'];
        $appClient = $_POST['appClient'];
        $mailClient = $_POST['mailClient'];
		$pw = rand(10000, 999999).'-'.rand(8, 278);
		$pw_ = md5($pw);
		
		$conn = dbConnexion::connect();
		$s = oci_parse($conn, "begin :result_ := RECORD_CLIENTE('$ciClient','$nameClient','$appClient','$mailClient','$pw_'); end;");
		oci_bind_by_name($s, ":result_", $return_value_, 100);
		oci_execute($s);
		oci_close($conn);
		if($return_value_ == 'TRUE'){
                echo<<<MSG
				<script>alert('Su contraseña es: $pw');</script>
                <div align="center" style="margin-top: 50px;margin-bottom: 100px;">
                    <h2>El cliente ha sido creado exitosamente</h2>
                    <div style="padding: 10px; border-radius: 10px; background:#dddddd;margin: 10px;">
                        El Cliente se ha creado de manera exitosa:<br/>
                        Su contraseña de cliente ha sido enviada a la direccion: $mailClient<br/>
                        <br/>
                        Gracias por usar nuestros servicios<br/>
                    </div>
                </div>
                <a href="."><button class="buttonWolf" name="token" value="create_reservation">¡ LISTO !</button></a>
MSG;
		}else echo $return_value_;        
        
    }
    
    public function login($error = false){
        $_error = ($error)? '<div style="color:#ff0000; font-weight: bolder;">Error de Usuario y/o contraseña</div>': '';
        if (!isset($_POST['routeId'])) header('location: .');
            $routeId = $_POST['routeId'];
            $requireButtocks = $_POST['requireButtocks'];
            echo<<<LOGIN
            <div align="center" style="margin-top: 50px;margin-bottom: 100px;">
                <h3>Datos del Cliente</h3><br/>
                <p>Para continuar necesitas ingresar tus datos de cliente:</p>
                <form action='.' method='post' target='_self'>
                    <input type='hidden' name='routeId' value='$routeId'/>
                    <input type='hidden' name='requireButtocks' value='$requireButtocks'/>
                    <table class="lookUpTable" style="border: 15px solid #ffffff; border-radius: 10px;">
                        <tr>
                            <th class="lookUpTableCell" style="text-align: right;">Id de Cliente o email</th>
                            <td class="lookUpTableCell"><input type="text" name="idClient" style="width: 190px; height: 17px;border: 1px solid #999999;" value="" required="required"/></td>
                        </tr>
                        <tr>
                            <th class="lookUpTableCell" style="text-align: right;">Contraseña</th>
                            <td class="lookUpTableCell"><input type="password" name="passClient" style="width: 190px; height: 17px;border: 1px solid #999999;" value="" required="required"/></td>
                        </tr>
                    </table>
                    $_error
                    <button type='submit' class='buttonWolf' name='token' value='validate_user'>CONTINUAR</button>
                </form>
                <br/><br/>
                <pre>¿ Aún no te has registrado ?, has click <b><a href="?token=registerClient" target="_new"> AQUI</a></b></pre>
            </div><small>Advertencia: Por cuestiones de seguridad, no se mantendra una sesion activa.</small>
LOGIN;
    }
    
    public static function sumary_($id){
        echo<<<SUMARY
        <style type="text/css">
            .sumary_client{
                margin-top: 20px;
                margin-bottom: 10px;
                border: 0px;
                border-radius: 6px;
                background: #cccccc;
                width: 50%;
                padding: 10px;
            }
        </style>
        <div align="center">
        <div align="center" class="sumary_client">
            <b><pre>Datos del cliente</pre></b>
            <table class="lookUpTable">
SUMARY;


		$conn = dbConnexion::connect();
		$stid = oci_parse($conn, "select BUSCAR('client','$id') as RRC from dual");
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
    
    public static function validate(){
        if (!isset($_POST['idClient']) || !isset($_POST['passClient']) || !isset($_POST['routeId'])) return false;;
        $idClient = $_POST['idClient'];
        $passClient = md5($_POST['passClient']);
        $conn = dbConnexion::connect();
		$s = oci_parse($conn, "begin :result_ := logincliente('$idClient','$passClient'); end;");
		oci_bind_by_name($s, ":result_", $return_value_, 100);
		oci_execute($s);
		oci_close($conn);
		return ($return_value_ == 'TRUE')? true : false;
    }
}

?>
