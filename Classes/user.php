<?php

/*Author: Andres Sandoval
 * Date: May 2013
 * email: andresandoval992@gmail.com
 */
    require_once 'object.php';
    use object;
    
    class user extends object{
        
        public $logedUserId;
        public $logedUserType;
        
        public function user($u = '', $t = ''){
            $this->logedUserId = $u;
            $this->logedUserType = $t;
        }
//<editor-fold defaultstate="collapsed" desc="Utilitarios">
        
        public static function logIn($id_, $pass_){
            session_start();
            $id = dbConnexion::SqlInjection($id_);
            $pass = md5($pass_);
            
            $query = "begin :ut := loginusuario('$id', '$pass'); end;";
            $conn = dbConnexion::connect();
            $s = oci_parse($conn, $query);
            oci_bind_by_name($s, ":ut", $logedUserType, 100);
            oci_execute($s);
            oci_close($conn);
            if($logedUserType != ''){
                $_SESSION['logedUserId'] = $id;
                $_SESSION['logedUserType'] = $logedUserType;
                $_SESSION['msg'] = 'msg';
                return true;
            }
            session_destroy();
            return false;
        }
        
        public static function logOut(){
            session_start();
            if(isset ($_SESSION['logedUserId'])){
                session_destroy();
            }else{
                session_destroy();
                control::deniedAccess();
                exit();
            }
        }
        
        public function getUserImage(){
            return (file_exists('./Images/Users/'.$this->logedUserId.'.JPG')) ? './Images/Users/'.$this->logedUserId.'.JPG' : './Images/Users/null.gif';
        }
        
        protected function getLookUpButtons($value){
            $clear = '<img src="./Images/CSS/clear.png" class="lookUpButtons" title="Opcion no permitida para este usuario"/>';
            echo ($value == $this->logedUserId)? $clear :'<img src="./Images/CSS/edit.png" class="lookUpButtons" onclick="modify_(\'user\',\''.$value.'\');" title="Editar usuario"/>';
            echo ($value == $this->logedUserId)? $clear :'<img src="./Images/CSS/delete.png" class="lookUpButtons" onclick="delete_(\'user\', \''.$value.'\');" title="Eliminar usuario"/>';
            echo '<img src="./Images/CSS/print.png" class="lookUpButtons" onclick="print_(\'individual\',\''.$value.'\',\'user\');" title="Imprimir usuario"/>';
        }
        
        public function getDetails_($detailOf, $id){
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
                <h2>Ingresar Datos de Usuarios</h2>
                <form name="frmBody" id="frmBody" action="javascript:create_('user');" method="post" target="_self" onsubmit="return confirmar();" onreset="return confirmar();">
                    <table class='cellDetail'>
                        <tr>
                            <td>ID</td>
                            <td><input name="userId" id="userId" title="ID del Usuario" type="text" required="required" onchange="getDetails('user','userId');"/></td>
                            <td class='cellDetail' id="userIdDetails"></td>
                        </tr>
                        <tr>
                            <td>Nombres</td>
                            <td><input name="userName" id="userName" title="Nombres del Usuario" type="text" required="required"/></td>
                        </tr>
                        <tr>
                            <td>Apellidos</td>
                            <td><input name="userLastname" id="userLastname" title="Apellidos del Usuario" type="text" required="required"/></td>
                        </tr>
                        <tr>
                            <td>CI</td>
                            <td><input type="number" name="userCi" id="userCi" title="Ci del Usuario" required="required" onchange="getDetails('user','userCi');"/></td>
                            <td class='cellDetail' id="userCiDetails"></td>
                        </tr>
                        <tr>
                            <td>Genero</td>
                            <td>
                                <select name="userGender" id="userGender" title="Genero del Usuario" required="required">
                                    <option selected value="">-- Selecciona uno --</option>
                                    <option value="Masculino">Masculino</option>
                                    <option value="Femenino">Femenino</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Fecha de Nacimiento</td><td>
                                <?php $cal = new calendar_('userBirthDate','1990','2013','Fecha de Nacimiento del Usuario');?>
                            </td>
                        </tr>
                        <tr>
                            <td>Nacionalidad</td><td>
                                <select name="userNationality" id="userNationality" title="Nacionalidad del Usuario" required="required">
                                    <?php $this->getNacionality();?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>E-Mail</td>
                            <td><input type="email" name="userEmail" id="userEmail" title="Email del Usuario" value=''  required="required"/></td>
                        </tr>
                        <tr>
                            <td>Telefono</td>
                            <td><input type="text" name="userPhoneNumber" id="userPhoneNumber" title="Numero telefonico del Usuario" value='' required="required"/></td>
                        </tr>
                        <tr>
                            <td>Tipo</td>
                            <td>
                                <select name="userType" id="userType" title="Tipo del Usuario" required="required">
                                    <option selected value="">-- Selecciona uno --</option>
                                    <option value="Administrador">Administrador</option>
                                    <option value="Secretaria">Secretaria</option>
                                    <option value="Gerente">Gerente</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Contraseña</td>
                            <td><input name="userPassword_1" id="userPassword_1" title="Contraseña del Usuario" type="password" autocomplete="off" required="required" onkeyup='passwordsComparison();' onchange='passwordsComparison();'/></td>
                        </tr>
                        <tr>
                            <td>Repetir Contraseña</td>
                            <td><input name="userPassword_2" id="userPassword_2" title="Repetir contraseña del Usuario" type="password" autocomplete="off" required="required" onkeyup='passwordsComparison();' onchange='passwordsComparison();'/></td>
                            <td class='cellDetail' id='passwordsComparison'></td>
                        </tr>
                        <tr>
                            <td>Imagen</td>
                            <td align="center"><img src="./Images/Users/null.gif" height="50" width="50"/></td>
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
            $userId = $_POST['userId'];
            $userName = $_POST['userName'];
            $userLastname = $_POST['userLastname'];
            $userCi = $_POST['userCi'];
            $userGender = $_POST['userGender'];
            $userBirthDate = $_POST['userBirthDate'];
            $userBirthDate = "to_date('$userBirthDate', 'YYYY-MM-DD')";
            $userNationality = $_POST['userNationality'];
            $userEmail = $_POST['userEmail'];
            $userPhoneNumber = $_POST['userPhoneNumber'];
            $userType = $_POST['userType'];
            $userPassword = md5 ($_POST['userPassword']);
            $query = "begin :result_ := RECORD_USER('CREATE','$userId','$userName','$userLastname',$userCi,'$userGender',$userBirthDate,'$userNationality','$userEmail','$userPhoneNumber','$userType','$userPassword'); end;";
            $conn = dbConnexion::connect();
            $s = oci_parse($conn, $query);
            oci_bind_by_name($s, ":result_", $return_value_, 100);
            oci_execute($s);
            oci_close($conn);
            echo ($return_value_ == 'TRUE')? 'ok': '¡ No se pudo crear el usuario !';
        }
        
        public function modify_($who, $profile = false){
            $conn = dbConnexion::connect();
            $stid = oci_parse($conn, "SELECT BUSCAR('individual_user', '$who') AS BRC FROM dual");
            oci_execute($stid);
            if (!($row_ = oci_fetch_array($stid, OCI_ASSOC))) return 'Ha ocurrido un error';
            $rc = $row_['BRC'];
            oci_execute($rc);
            if (!($row = oci_fetch_array($rc, OCI_NUM))){echo '<div style="text-align: center;"><br/><h3>¡ El registro buscado no existe !</h3></div>'; return;}
            ?>
            <div align="center">
                <h2>Edicion de Usuario : <?php echo $who;?></h2>
                <form name='frmBody' id='frmBody' action="javascript:saveChanges_('user');" method='post' target='_self' onsubmit='return confirmar();' onreset='return confirmar();'>                            
                    <input type='hidden' name='userId' id='userId' value='<?php echo $row[0];?>'/>
                    <input type='hidden' name='profile' id='profile' value='<?php echo ($profile)? 'profile':'';?>'/>
                    <table>
                        <tr>
                            <th style='text-align: right;'>Id de Usuario :</th>
                            <td><?php echo $row[0];?></td>
                        </tr>
                        <tr>
                            <th style='text-align: right;'>Nombres :</th>
                            <td><input type='text' name='userName' id='userName' value='<?php echo $row[1];?>' required='required' title="Nombres del Usuario"/></td>
                        </tr>
                        <tr>
                            <th style='text-align: right;'>Apellidos :</th>
                            <td><input type='text' name='userLastname' id='userLastname' value='<?php echo $row[2];?>' required='required' title="Apellidos del Usuario"/></td>
                        </tr>
                        <tr>
                            <th style='text-align: right;'>Número de CI :</th>
                            <td><?php echo $row[3];?></td>
                        </tr>
                        <tr>
                            <th style='text-align: right;'>Genero :</th>
                            <td>
                                <select name="userGender" id="userGender" title="Genero del Usuario" required="required">
                                    <option value="">-- Selecciona uno --</option>
                                    <option <?php echo ($row[4]=='Masculino')? 'selected':'';?> value="Masculino">Masculino</option>
                                    <option <?php echo ($row[4]=='Femenino')? 'selected':'';?> value="Femenino">Femenino</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th style='text-align: right;'>Fecha de Nacimiento :</th><td><?php echo $row[5];?></td>
                        </tr>
                        <tr>
                            <th style='text-align: right;'>Nacionalidad :</th>
                            <td>
                                <select name="userNationality" id="userNationality" title="Nacionalidad del Usuario" required="required">
                                    <?php $this->getNacionality($row[6]);?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th style='text-align: right;'>e-Mail :</th>
                            <td><input type="email" name="userEmail" id="userEmail" title="Email del Usuario" value='<?php echo $row[7];?>' required="required"/></td>
                        </tr>
                        <tr>
                            <th style='text-align: right;'>Telefono :</th>
                            <td><input type="text" name="userPhoneNumber" id="userPhoneNumber" title="Numero telefonico del Usuario" value='<?php echo $row[8];?>' required="required"/></td>
                        </tr>
                        <tr>
                            <th style='text-align: right;'>Tipo de Usuario :</th>
                            <td>
                                <?php
                                if($profile){
                                    echo $row[9];
                                    echo "<input type='hidden' name='userType' value='$row[9]' id='userType'/>";
                                }else{
                                ?>
                                <select name="userType" id="userType" title="Tipo del Usuario" required="required">
                                    <option value="">-- Selecciona uno --</option>
                                    <option <?php echo ($row[9]=='Administrador')? 'selected':'';?> value="Administrador">Administrador</option>
                                    <option <?php echo ($row[9]=='Secretaria')? 'selected':'';?> value="Secretaria">Secretaria</option>
                                    <option <?php echo ($row[9]=='Gerente')? 'selected':'';?> value="Gerente">Gerente</option>
                            </select><?php }?>
                            </td>
                        </tr>
                        <tr>
                            <th style='text-align: right;'>Contraseña :</th>
                            <td><input type="password" name="userPassword_1" id="userPassword_1" title="Contraseña del Usuario" autocomplete="off" required="required" onkeyup='passwordsComparison();' onchange='passwordsComparison();' value='default'/></td>
                        </tr>
                        <tr>
                            <th style='text-align: right;'>Repetir Contraseña :</th>
                            <td><input type="password" name="userPassword_2" id="userPassword_2" title="Repetir contraseña del Usuario" autocomplete="off" required="required" onkeyup='passwordsComparison();' onchange='passwordsComparison();' value='default'/></td>
                            <td class='cellDetail' id='passwordsComparison'></td>
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
            $userId = $_POST['userId'];
            $userName = $_POST['userName'];
            $userLastname = $_POST['userLastname'];
            $userGender = $_POST['userGender'];
            $userNationality = $_POST['userNationality'];
            $userEmail = $_POST['userEmail'];
            $userPhoneNumber = $_POST['userPhoneNumber'];
            $userType = $_POST['userType'];
            $userPassword = (md5($_POST['userPassword']) == md5('default'))? '': md5($_POST['userPassword']);
            $badMsg = ($_POST['profile']=='profile')?'¡ No se pudieron guardar los cambios en su informacion personal !': '¡ No se pudieron guardar los cambios del usuario !';
            $query = "begin :result_ := RECORD_USER('MODIFY','$userId','$userName','$userLastname','0','$userGender',sysdate,'$userNationality','$userEmail','$userPhoneNumber','$userType','$userPassword'); end;";
            $conn = dbConnexion::connect();
            $s = oci_parse($conn, $query);
            oci_bind_by_name($s, ":result_", $return_value_, 100);
            oci_execute($s);
            oci_close($conn);
            echo ($return_value_ == 'TRUE')? 'ok': $badMsg."\n".oci_error();
        }
                
//</editor-fold>
    }
?>