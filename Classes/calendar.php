<?php

/*Author: Andres Sandoval
 * Date: May 2013
 * email: andresandoval992@gmail.com
 */
    class calendar_ {
        
        public function calendar_($objectName, $initYear, $finalYear,$title, $time = false, $defaultValue = ''){
            $lblMonts = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre');
            $ymdhm = '';
            $time_ = ($time)? 'true': 'false';
            if($defaultValue != ''){
                $dDate = '';
                $dTime = '';
                if ($time){
                    $var = split(' ', $defaultValue);
                    $dDate = split('-',$var[0]);
                    $dTime = split(':', $var[1]);
                }else{
                    $dDate = split('-',$defaultValue);
                }
            }
            
            ?>
            <div title='HoraFecha : <?php echo $title;?>' style='width: auto; margin: 0px; padding: 0px; border: 0px;'>
                <input type='hidden' id='<?php echo $objectName; ?>' name='<?php echo $objectName; ?>' value='' required='required'/>
                <table style='margin: 0px;padding: 0px; border: 0px;'>
                    <tr>
                        <td style='border: 0px;'>
                            <select id='<?php echo $objectName; ?>_year' onchange='setCalendarDaysValues("<?php echo $objectName;?>");setCalendarValue(<?php echo '"'.$objectName.'",'.$time_; ?>);' required='required' title='Año: <?php echo $title;?>'>
                                <option selected value=''>-- año --</option>
                                <?php for($i = $initYear; $i<=$finalYear; $i++){
                                    $ymdhm = ($i<10) ? '0'.$i:$i;
                                    echo "<option value='$ymdhm'>$ymdhm</option>";
                                }?>
                            </select>
                        </td>
                        <td style='border: 0px;'>
                            <select id='<?php echo $objectName; ?>_month' onchange='setCalendarDaysValues("<?php echo $objectName;?>");setCalendarValue(<?php echo '"'.$objectName.'",'.$time_; ?>);' required='required' title='Mes: <?php echo $title;?>'>
                                <option selected value=''>-- mes --</option>
                                <?php for($i = 1; $i <= 12; $i++){
                                    $ymdhm = ($i<10) ? '0'.$i:$i;
                                    echo "<option value='$ymdhm'>".$lblMonts[$i-1]."</option>";
                                }?>
                            </select>
                        </td>
                        <td style='border: 0px;'>
                            <select id='<?php echo $objectName; ?>_day' onchange='setCalendarValue(<?php echo '"'.$objectName.'",'.$time_; ?>);' required='required' title='Dia: <?php echo $title;?>'>
<!--                                <option value=''>-- dia --</option>-->
                            </select>
                        </td>
                        <?php if($time){?>
                        <td style='border: 0px;'> | </td>
                        <td style='border: 0px;'>
                            <select id='<?php echo $objectName; ?>_hour' onchange='setCalendarValue(<?php echo '"'.$objectName.'",'.$time_; ?>);' required='required' title='Hora: <?php echo $title;?>'>
                               <option selected value=''>-- hora --</option>
                               <?php for( $i= 0; $i<=23; $i++){
                                   $ymdhm = ($i<10) ? '0'.$i:$i;
                                   echo "<option value='$ymdhm'>$ymdhm</option>";
                               }?>
                           </select>
                        </td>
                        <td style='border: 0px;'>
                            <select id='<?php echo $objectName; ?>_minute' onchange='setCalendarValue(<?php echo '"'.$objectName.'",'.$time_; ?>);' required='required' title='Minuto: <?php echo $title;?>'>
                               <option selected value=''>-- minuto --</option>
                               <?php for( $i= 0; $i<=59; $i++){
                                   $ymdhm = ($i<10) ? '0'.$i:$i;
                                   echo "<option value='$ymdhm'>$ymdhm</option>";
                               }?>
                           </select>
                        </td>
                       <?php }?>
                        <td style='border: 0px;'>
                            <div class='help' onclick='getCalendarHelp(<?php echo $time_; ?>);'></div>
                        </td>
                    </tr>
                </table>
            </div>
            <?php
        }
    }
?>