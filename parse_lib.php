<?php

   function check_variable($string){
       if(preg_match("/(LF|GF|TF)@[-a-zA-Z_$&%*!?][-a-zA-Z_$&%*!?0-9]*/", $string))//identifier check
       {
         return true;
       }
       else{
           return false;
       }
   }

   function check_symbol($string){
     if(preg_match("/bool@(true|false)/", $string)){
         return true;
     }
     else if(preg_match("/int@(-|)[0-9]*/", $string)){
         return true;
     }
     else if(preg_match("/nil@nil/", $string)){
           return true;
     }

     else {
         return false;
     }


   }

   function check_label($string){

       if(preg_match("/[-a-zA-Z_$&%*!?][-a-zA-Z_$&%*!?0-9]*/", $string))//identifier check
       {
           return true;
       }
       else{
           return false;
       }
   }

   function cut_comment($line){
        global $comm_count;
        if(strpos($line, "#", 0)){

            $line= substr($line, 0, strpos($line, "#", 0)); //cut of comment

            $comm_count++;
        }
        return $line;
   }

   function convert_string($input){

       if(strpos($input, "\"")){
           str_replace("\"", "&quot", $input);
       }

       if(strpos($input, "'")){
           str_replace("'", "&apos", $input);
       }

       if(strpos($input, "&")){
           str_replace("&", "&amp", $input);
       }

       if(strpos($input, "<")){
           str_replace("&", "&lt", $input);
       }

       if(strpos($input, "<")){
           str_replace("&", "&gt", $input);
       }

   }
?>