<?php
    /**
     * Project: IPP 2023 Parser
     * @file parse_lib.php
     * @brief Library for syntactic and lexical analysis of IPPcode23
     * @author Daniel Žárský <xzarsk04@stud.fit.vutbr.cz>
     */

    /**
     * Function checks whether given string is valid variable
     * @param String loaded from input
     * @return true in case of valid variable, false otherwise
     */
   function check_variable($string){
       if(preg_match("/(LF|GF|TF)@[-a-zA-Z_$&%*!?][-a-zA-Z_$&%*!?0-9]*/", trim($string)))//identifier check
       {
         return true;
       }
       else{
           return false;
       }
   }

    /**
     * Function checks whether given string is valid symbol
     * @param String loaded from input
     * @return true in case of valid symbol, false otherwise
     */
   function check_symbol($string){
     if(preg_match("/bool@(true|false)/", $string)){
         return true;
     }
     else if(preg_match("~^int@[+-]?[1-9][0-9]+$~", $string)||preg_match("~^int@[+]?(0[xX][0-9a-fA-F_]+|0[0-7_o]*)$~", $string)||preg_match("~^int@[+-]?[1-9][0-9_]+$~", $string)){ //0xF1_0F

         return true;
     }
     else if($string == "nil@nil"){
         return true;
     }
     else if(preg_match("/^string@[^#\s\\\\]*(?:\\\\[0-9][0-9][0-9][^#\s\\\\]*)*$/", trim($string))){
         return true;
     }
     else{

         return false;
     }


   }

    /**
     * Function checks whether given string is valid label
     * @param String loaded from input
     * @return true in case of valid label, false otherwise
     */
   function check_label($string){

       if(preg_match("~^[a-zA-Z_\-$&%*!?][a-zA-Z0-9_\-$&%*!?]*$~", trim($string)))//identifier check
       {
           return true;
       }
       else{
           return false;
       }
   }

    /**
    * Function process loaded text line by line, checks whether line contains comment, if it does so, it gets rid of the comment
    * @param One line from input (not processed yet)
    */
   function cut_comment($line){
        global $comm_count;
        if(strpos($line, "#", 0)){

            $line= substr($line, 0, strpos($line, "#", 0)); //cut of comment

            $comm_count++;
        }

        return trim($line);
   }

    /**
    * Function transform loaded string into correct format xml representation
    * @param One line from input (not processed yet)
    */
   function convert_string($input){

       $new = htmlspecialchars($input,ENT_QUOTES, null);
       return $new;

   }
?>