<?php

/*Author: Andres Sandoval
 * Date: May 2013
 * email: andresandoval992@gmail.com
 */

require_once './Classes/object.php';

class main {
    
    private $user = null;
    private $object = null;

    public function main(){
        $this->user = new user($_SESSION['logedUserId'], $_SESSION['logedUserType']);

        if (isset($_POST['form'])) $this->getForm($_POST['form']);
        elseif( isset($_POST['what']) && isset($_POST['action']) ){
            switch ($_POST['what']) {
                case 'user': $this->object = $this->user;break;
                case 'route': $this->object = new route();break;
                case 'plane': $this->object = new plane();break;
                case 'pilot': $this->object = new pilot();break; 
                case 'reservation': $this->object = new reservation();break;
                case 'invoice': $this->object = new invoice();break;
                case 'res_incumplidas': $this->object = new res_incumplidas();break;
                case 'event': $this->object = new event();break;
                default: return;
            }
            switch ($_POST['action']) {
                case 'create': $this->object->create_();break;
                case 'lookUp': $this->object->lookUp_($_POST['filter']);break;
                case 'modify': $this->object->modify_($_POST['id']);break;
                case 'saveChanges':$this->object->saveChanges_(); break;
                case 'delete': $this->object->delete_($_POST['id']);break;
                case 'print': $this->object->print_($_POST['filter'], $_POST['type']);break;
                case 'detail': $this->object->getDetails_($_POST['detailOf'],$_POST['id']);break;
                case 'new_invoice': $this->object->new_($_POST['reservationId']); break;
                case 'edit_profile': $this->object->modify_($_POST['id'], true); break;
                default: return;
            }

        }else $this->runHTML();
        $this->user = null;
        $this->object = null;
    }

    private function getLookUpForm($what){
        $lavels = array('user'=>'Usuarios', 'route'=>'Rutas', 'plane'=>'Aviones', 'pilot'=>'Pilotos', 'reservation'=>'Reservaciones', 'res_incumplidas' => 'Reservaciones Incumplidas', 'event'=>'Operaciones sobre Rutas');
        ?>
        <div>
            <h2>Datos Ingresados de <?php echo $lavels[$what];?></h2>
            <form name="frmLookUp" id="frmLookUp" action="javascript:lookUp_('<?php echo $what;?>');" method="post"></form>
            <table cellspacing="8px" border="1px" width="100%" height="20px" text-align="center">
                 <tr>
                     <td width="50%" align="center" style="vertical-align: middle; background-color: #eeffee; border-radius:7px; margin: 0px;">
                         <div style="margin: 5px;">
                             <img src="./Images/CSS/search.png" class="lookUpInputButtons" onclick="document.frmLookUp.submit();" title="Iniciar busqueda / Actualizar"/>&nbsp;&nbsp;
                            <input type="text" name="filter" form="frmLookUp" class="lookUpInput" id="filter"  onchange='document.frmLookUp.submit();' autocomplete="on" value="Buscar registros almacenados" autofocus="autofocus" title="Palabras clave de busqueda." onfocus="if(this.value == 'Buscar registros almacenados'){this.value='';}" onblur="if(this.value == ''){this.value='Buscar registros almacenados';}"/>
                            <img src="./Images/CSS/cancelSearch.png" class="lookUpInputButtons" onclick="resetLookUp();" title="Cancelar busqueda."/>
                         </div>
                     </td>
                     <td width="50%" align="center" style="vertical-align: middle; background-color: #eeffee; border-radius:7px; margin: 0px;">
                         <img src="./Images/CSS/print.png" class="button" style="width: 23px;height: 23px; margin: 5px;" onclick="print_('general',document.getElementById('filter').value,'<?php echo $what;?>');" title="Imprimir todos los registros mostrados."/>                             
                     </td>
                 </tr>
            </table>
            <div id="searchResult" class="result"></div>
            <hr/>
        </div>
        <?php 
    }

    private function generateMenuItem($id, $caption){
        echo <<<MENUitem
        <button class="imgButton" onclick="getForm($id);">
            <div id="mnuItem$id" class="menuItem">$caption</div>
        </button>
MENUitem;
    }
    
    private function getMenu(){
            $menus = array('Principal','Ingresar Usuarios', 'Usuarios Ingresados','Generar respaldo de BD','Ingresar Rutas','Rutas ingresadas','Ingresar Aviones','Aviones Ingresados','Ingresar Pilotos','Pilotos Ingresados','Reservaciones','Reservaciones Incumplidas','Eventos sobre rutas','Rutas ingresadas');
            $min = 0;
            $max = 0;
            switch ($this->user->logedUserType){
                case 'Administrador': $min = 1; $max = 4; break;
                case 'Secretaria': $min = 4; $max = 13; break;
                case 'Gerente': $min = 5; $max = 6; break;
                default:return;
            }
            $this->generateMenuItem(0, $menus[0]);
            for ($i=$min; $i<$max; $i++)$this->generateMenuItem($i, $menus[$i]);
        }

//<editor-fold defaultstate="collapsed" desc="Users Forms">
    private function getAdminForm_1(){
            echo '<div><h2>Menu Principal de Administrador..</h2><br/><br/>Desde tu cuenta en AirLines tu podras:<br/><ul><li><h3>Como usuario Administrador</h3>
                  <ul><li>Crear usuarios nuevos para el sistema..</li><li>Modificar la informacion de los usuarios registrados en el sistema..</li>
                  <li>Recuperar contrase&ntilde;as olvidadas..</li><li>Generar reportes, tanto General, como Individual, de los usuarios registrados al sistema..</li>
                  </ul></li><li><h3>Como usuario registrado</h3><ul><li>Modificar tu informacion personal..</li><li>Cambiar tu contrase&ntilde;a..</li><li>Cambiar tu imagen de usuario..</li>
                  </ul></li></ul><br/>Te recomendamos cambiar tu contrase&ntilde;a de forma periodica, asi como usar caracteres especiales y numeros en su composicion..
                  <br/>Revisa el manual de usuario del sistema para enterarte de caracteristicas y particularidades extras del sistema..!!</div>';
    }

    private function getAdminForm_2(){
        $this->object = new user();
        $this->object->new_();
        unset ($this->object);
    }

    private function getSecreForm_1(){
            echo '<div><h2>Menu Principal de Secretaria..</h2><br/><br/>Desde tu cuenta en AirLines tu podras:<br/>
                  <ul><li><h3>Como usuario Secretaria</h3><ul><li>Ingresar nuevas rutas al sistema..</li><li>Actualizar la informacion de las rutas ingresadas al sistema</li>
                  <li>Eliminar rutas ingresadas al sistema</li><li>Generar reportes, tanto General, como Individual, de las rutas ingresadas al sistema..</li><li>Facturacion de reservaciones..</li>
                  <li>Generar tickets de abordaje para las reservaciones facturadas..</li><li>Generar reportes, tanto General, como Individual, de las reservaciones existentes en el sistema..</li>                        
                  </ul></li><li><h3>Como usuario registrado</h3><ul><li>Modificar tu informacion personal..</li><li>Cambiar tu contrase&ntilde;a..</li><li>Cambiar tu imagen de usuario..</li>
                  </ul></li></ul><br/>Te recomendamos cambiar tu contrase&ntilde;a de forma periodica, asi como usar caracteres especiales y numeros en su composicion..
                  <br/>Revisa el manual de usuario del sistema para enterarte de caracteristicas y particularidades extras del sistema..!!</div>';
    }

    private function getSecreForm_2(){
        $this->object = new route();
        $this->object->new_();
        unset($this->object);
    }

    private function getSecreForm_4(){
        $this->object = new plane();
        $this->object->new_();
        unset($this->object);
    }

    private function getSecreForm_6(){
        $this->object = new pilot();
        $this->object->new_();
        unset($this->object);
    }

    private function getManagForm_1(){
            echo '<div><h2>Menu Principal de Gerente..</h2><br/><br/>Desde tu cuenta en AirLines tu podras:<br/>
                  <ul><li><h3>Como usuario Gerente</h3><ul><li>Asignar precios a rutas nuevas ingresadas al sistema..</li><li>Actualizar los precios de las rutas existentes..</li>
                  <li>Generar reportes, tanto General, como Individual, de las rutas ingresadas al sistema..</li></ul></li><li><h3>Como usuario registrado</h3>
                  <ul><li>Modificar tu informacion personal..</li><li>Cambiar tu contrase&ntilde;a..</li><li>Cambiar tu imagen de usuario..</li></ul>                
                  </li></ul><br/>Te recomendamos cambiar tu contrase&ntilde;a de forma periodica, asi como usar caracteres especiales y numeros en su composicion..
                  <br/>Revisa el manual de usuario del sistema para enterarte de caracteristicas y particularidades extras del sistema..!!</body>';
    }
//</editor-fold>

    private function getForm($formId){
            switch ($this->user->logedUserType) {
                case  'Administrador':
                    switch ($formId) {
                        case 0:$this->getAdminForm_1();break;
                        case 1:$this->getAdminForm_2();break;
                        case 2:$this->getLookUpForm('user');break;
                        case 3:dbConnexion::backUp();break;
                        default:break;
                    }
                    break;
                case 'Secretaria':
                    switch ($formId) {
                        case 0:$this->getSecreForm_1();break;
                        case 4:$this->getSecreForm_2();break;
                        case 5:$this->getLookUpForm('route');break;
                        case 6:$this->getSecreForm_4();break;
                        case 7:$this->getLookUpForm('plane');break;
                        case 8:$this->getSecreForm_6();break;
                        case 9:$this->getLookUpForm('pilot');break;
                        case 10:$this->getLookUpForm('reservation');break;
                        case 11:$this->getLookUpForm('res_incumplidas');break;
                        case 12:$this->getLookUpForm('event');break;
                        default:break;
                    }
                    break;
                case 'Gerente':
                    switch ($formId) {
                        case 0:$this->getManagForm_1();break;
                        case 5:$this->getLookUpForm('route');break;
                        default:break;
                    }break;
                default:break;
            }
        }

    private function runHTML(){
        ?>
        <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" charset="utf-8"/>
                <meta http-equiv="X-UA-Compatible" content="chrome=1"/>
                <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
                <link rel="shortcut icon" href="./Images/CSS/Airplane.png"/>
                <link rel="stylesheet" href="./Styles/style.css" type="text/css" media="all"/>
                <link rel="stylesheet" href="./Styles/styleCalendar.css" type="text/css" media="all"/>
                <script type="text/javascript" src="./Scripts/script.js"></script>                    
                <script type="text/javascript" src="./Scripts/calendarScript.js"></script>
                <title>Pagina principal | <?php echo $this->user->logedUserType;?></title>
            </head>    
            <body>
                <form id="frmLogOut" action="" method="post" target="_self"></form>
                <div class="header">
                        <a class="userLink" title="Configura tu informacion personal." onclick="edit_profile('<?php echo $this->user->logedUserId;?>');">
                            <img src="./Images/CSS/config.png" class="userConfigurationImage"/>
                            <?php echo $this->user->logedUserId;?>
                        </a>                    
                        <strong class="userType">( <?php echo $this->user->logedUserType;?> )</strong>
                        <button type="submit" form="frmLogOut" name="log" value="out" class="imgButton" title="Cierra la sesión actual.">
                             <span class="userCloseSession">&nbsp;[ Cerrar sesión ]&nbsp</span>
                        </button>
                        <img class="userImg" src="<?php echo $this->user->getUserImage();?>"/>
                </div>
                <hr/>
                <div class="menu" align="right"><?php $this->getMenu();?></div>
                <div id="bodyMenu" class="bodyMenu"></div>
                <div class="foot" align="center">AirLines &reg;, desarrollado por Andres Sandoval</div>                
                <div id="overlay" class="overlay" align="center"></div>
                <script type="text/javascript">getForm(0);</script>
            </body>
        </html>
        <?php
        if(isset($_SESSION['msg'])){
            unset($_SESSION['msg']);
            echo '<script>welcomeMessage();</script>';
        }
    }
}

?>
