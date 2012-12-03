<?php

class html
{
    private static function do_offset($level)
    {
        $offset = "";             // offset for subarry 
        for ($i=1; $i<$level;$i++){
        $offset = $offset . "<td></td>";
        }
        return $offset;
    }
    
    private static function show_array($array, $level, $sub){
        if (is_array($array) == 1){          // check if input is an array
           foreach($array as $key_val => $value) {
               $offset = "";
               if (is_array($value) == 1){   // array is multidimensional
               echo "<tr>";
               $offset = self::do_offset($level);
               echo $offset . "<td>" . $key_val . "</td>";
               self::show_array($value, $level+1, 1);
               }
               else{                        // (sub)array is not multidim
               if ($sub != 1){          // first entry for subarray
                   echo "<tr nosub>";
                   $offset = self::do_offset($level);
               }
               $sub = 0;
               echo $offset . "<td main ".$sub." width=\"120\">" . $key_val . 
                   "</td><td width=\"120\">" . $value . "</td>"; 
               echo "</tr>\n";
               }
           } //foreach $array
        }  
        else{ // argument $array is not an array
            return;
        }
    }

    public static function table($array){
        echo "<table cellspacing=\"0\" border=\"2\">\n";
        self::show_array($array, 1, 0);
        echo "</table>\n";
    }
}



