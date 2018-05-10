<?php

/*Author: Andres Sandoval
 * Date: May 2013
 * email: andresandoval992@gmail.com
 */

    require_once 'dbConnexion.php';
    
    class report {
        
        private $type;
        private $query;
        private $title;
        private $noPrint;
        
        public function report($type_, $query_, $title_, $no_print = false){
            $this->type = $type_;
            $this->query  = "SELECT $query_ AS RRC FROM dual";
            $this->title = $title_;
            $this->noPrint = $no_print;
            $this->runHTML();
        }
        
        private function generalResults(){
            $conn = dbConnexion::connect();
            $stid = oci_parse($conn, $this->query);
            oci_execute($stid);
            if (!($row = oci_fetch_array($stid, OCI_ASSOC))) return 'Ha ocurrido un error';
            $rc = $row['RRC'];
            oci_execute($rc);
            $header = true;
            $queryResults = '<table>';
            while(($row_2 = oci_fetch_array($rc, OCI_NUM))){
                if ($header){
                    $queryResults .= '<tr>';
                    for($i =1; $i<=count($row_2); $i++) $queryResults .= '<th>'.oci_field_name($rc, $i).'</th>';
                    $queryResults .= '</tr>';
                    $header = false;
                }
                $queryResults .= '<tr>';
                for($i =0; $i<count($row_2); $i++)$queryResults .= "<td>$row_2[$i]</td>";
                $queryResults .= '</tr>';
            }
            $queryResults .= "</table>";
            oci_free_statement($stid);
            oci_close($conn);
            return $queryResults;            
            if ($header) return '<div style="text-align: center;"><br/><h3>¡ No hay registros !</h3></div>';
        }
        
        private function individualResults(){
            $conn = dbConnexion::connect();
            $stid = oci_parse($conn, $this->query);
            oci_execute($stid);
            if (!($row = oci_fetch_array($stid, OCI_ASSOC))) return 'Ha ocurrido un error...';
            $rc = $row['RRC'];
            oci_execute($rc);
            if (!($row_2 = oci_fetch_array($rc, OCI_ASSOC)))return '<div style="text-align: center;"><br/><h3>¡ No hay registros !</h3></div>';
            $queryResults = '<table>';
            while( list($key, $value) = each($row_2) ){
                $queryResults .= "<tr><th style=\" text-align: right;\">$key  :</th><td>$value</td></tr>";
            }
            $queryResults .= "</table>";
            return $queryResults;
        }
        
        private function runHTML(){
            ?>
            <div>
                <div name="impresion" align="center">
                    <h2><?php echo $this->title;?></h2>
                    <div>
                        <?php echo ($this->type == 'individual')? $this->individualResults(): $this->generalResults();?>
                    </div>
                </div>
                <?php if(!$this->noPrint){?>
                <div name="noImpresion" class="noPrint">
                    <br/>
                    <hr style="border-color: #999999;"/>
                    <button class="noButton" onclick="print();">
                        <img src="./Images/CSS/print.png" class="button" title="Imprimir pagina actual"/>
                    </button>
                </div>            
                <?php 
                }
     echo '</div>';
        }        
    }
    
?>
