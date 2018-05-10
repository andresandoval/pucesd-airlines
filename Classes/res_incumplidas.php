<?php
require_once 'object.php';
/*Author: Andres Sandoval
 * Date: May 2013
 * email: andresandoval992@gmail.com
 */
class res_incumplidas  extends object{
    public function getLookUpButtons($value){
            echo '<img src="./Images/CSS/print.png" class="lookUpButtons" onclick="print_(\'individual\',\''.$value.'\',\''.get_called_class().'\');" title="Imprimir reservacion"/>';
    }
}
?>
