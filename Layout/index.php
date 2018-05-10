<?php

/*Author: Andres Sandoval
 * Date: May 2013
 * email: andresandoval992@gmail.com
 */
    require_once 'main.php';
    
    class index{
        private $layout_main = null;
        
        public function index(){
            session_start();
            if(isset($_SESSION['logedUserId'])){
                if(isset ($_POST['log']) && ($_POST['log']=='out')){
                    user::logOut();
                    $this->runHTML(2);
                }else $this->layout_main = new main();
            }else{
                session_destroy();
                if(isset ($_POST['log']) && ($_POST['log']=='in')){
                    if(user::logIn($_POST['IdUsuario'], $_POST['PassUsuario'])) $this->layout_main = new main();
                    else $this->runHTML(1);
                }else $this->runHTML();
            }
            $this->layout_index = null;
        }
        private function runHTML($situation = 0){
            ?>
            <!DOCTYPE html>
            <html lang="es">
                <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" charset="utf-8"/>
                    <meta http-equiv="X-UA-Compatible" content="chrome=1"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
                    <link rel="shortcut icon" href="./Images/Airplane.png"/>
                    <link rel="stylesheet" href="./Styles/style_index.css" type="text/css"/>
                    <title>AirLines</title>

                </head>    
                <body class="main-page">
                    <div style="color: #fff;">
                        <h1>AirLines</h1>
                        <pre style="font-size: 15px;">[ Administración del sistema ]</pre>
                    </div>
                    <div class="front-signin">
                        <form action="" method="post" target="_self">
                            <div class="placeholding-input username">
                                <input type="text" id="IdUsuario" class="text-input" name="IdUsuario" title="Nombre de usuario" autocomplete="on" tabindex="1" required="required"/>
                                <label for="IdUsuario" class="placeholder">Nombre de usuario</label>
                            </div>
                            <table class="flex-table password-signin">
                                <tbody>
                                    <tr>
                                        <td class="flex-table-primary">
                                            <div class="placeholding-input">
                                                <input type="password" id="PassUsuario" class="text-input flex-table-input" autocomplete="off" name="PassUsuario" title="Contraseña" tabindex="2" required="required"/>
                                                <label for="PassUsuario" class="placeholder">Contraseña</label>
                                            </div>
                                        </td>
                                        <td class="flex-table-secondary">
                                            <button type="submit" class="btn flex-table-btn" tabindex="4" name="log" value="in">
                                                Iniciar sesión
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>
                        
                        <div style="text-align: center;">
                            <?php
                            if ($situation != 0) {
                                echo '<table><tbody><tr><td class="error"><br/><br/><br/><br/>';
                                echo '<pre>';
                                if ($situation == 1) {
                                    echo '[ Error de  usuario y/o contraseña ]';
                                } elseif ($situation == 2) {
                                    echo '[ Has cerrado sesión correctamente ]';
                                }
                                echo '</pre>';
                                echo '</td></tr></tbody></table>';
                            }
                            ?>
                        </div>
                    </div>
                </body>
            </html>
            <?php
        }
    }
    
?>
