<?php

/*Author: Andres Sandoval
 * Date: May 2013
 * email: andresandoval992@gmail.com
 */

    class dbConnexion{
        
        public static function connect(){
            $myconn = oci_connect('AIRLINES', 'AIRLINES', 'localhost/XE');
            if(!$myconn) return null;
            return $myconn;
        }
        
        public static function backUp(){
            ?>
            <style>
                .buttton{
                    background: #99ccff;
                    border-radius: 6px;
                    width: 50px;
                    height: auto;
                    cursor: pointer;
                }
                .buttton:hover{
                    background: #ccffcc;
                }
            </style>
            <div align="left">
                <h2 style="font-family:monospace;">[ BackUp de base de datos ]</h2>
                <?php
                system('cmd.exe /c exp SYSTEM/root file=./backUp/script.dmp log=./backUp/log.log owner=AIRLINES', $return_var);
                if (file_exists('./backUp/script.dmp') && file_exists('./backUp/log.log')) {
                ?>
                <br/>
                <table style="border: 0px;">
                    <tr>
                        <td style="border: 0px;"><img class="buttton" src="./Images/CSS/logFile.png" /></td>
                        <td style="border: 0px;"><a href="./backUp/log.log" target="_new" style="font-family: monospace;font-size: 19px; font-weight: bold;color: #ff0000; text-decoration: none;">Decargar Log File..[&dArr;]</a></td>
                    </tr>
                    <tr>
                        <td style="border: 0px;"><img class="buttton" src="./Images/CSS/scriptFile.png" /></td>
                        <td style="border: 0px;"><content><a href="./backUp/script.dmp" target="_new" style="font-family: monospace;font-size: 19px; font-weight: bold;color: #ff0000; text-decoration: none;">Descargar Script File..[&dArr;]</a></content></td>
                    </tr>
                </table>        
                <?php }else echo 'A ocurrido un error mientras se generaba el backUp, por favor intenta nuevamente...<br/>Si el problema continua contacte al administrador...'; ?>
                <hr/>                
            </div>
            <?php
        }
        
        public static function SqlInjection($str){
            return str_replace("'", "\'", $str);
        }
    }
?>
