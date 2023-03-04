<?php
        /**
        * Project: IPP 2023 Parser
        * @file parse.php
        * @brief Script for syntactic and lexical analysis of IPPcode23.
        * @author Daniel Žárský <xzarsk04@stud.fit.vutbr.cz>
        */
        include 'parse_lib.php';
       ini_set('display_errors', 'stderr');

       echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");

        // argument  check
        if ($argc > 1) {

            if ($argv[1] == "--help" && $argc == 2) { //help
                echo("Usage php parse.php < input_file \n");
                exit(0);
            }
            else exit(10); // bad argument
        }

        $header = false;
        $in_count =0;  //instruction count

        while($line = fgets(STDIN)){

            $line = cut_comment($line); //cut comment

              if(!$header){             //check header
                  if(strtoupper(trim($line)) == strtoupper(".IPPcode23")){
                      $header =true; //header ok
                      echo("<program language=\"IPPcode23\">\n");
                      continue;
                  }
                  else if(str_contains($line, "#") || trim($line) == ""){ ///comment on the first line
                      continue;
                  }
                  else{
                      exit(21); //missing header
                  }
              }

            $lexemes = preg_split('/\s+/', $line); ///separating into lexemes

             if($lexemes[0]=="" || str_contains($lexemes[0], "#")){ //empty array after cutting new line
                 continue;
             }

             $in_count ++; //new valid instruction
             $inst = $lexemes[0]; ///after cutting and trimming the first word in line is instruction

             switch(trim(strtoupper($inst))){   ///sorting the input based on first lexeme

                 ///<INSTRUCTION> <VARIABLE>
                 case'DEFVAR':
                 case 'POPS':
                     if(count($lexemes)!=2){
                         exit(23);
                     }

                     echo(" <instruction order=\"".$in_count."\" opcode=\"".strtoupper($lexemes[0])."\">\n");
                     if(check_variable(trim($lexemes[1]))){
                         echo("  <arg1 type=\"var\">".convert_string($lexemes[1])."</arg1>\n");
                     }
                     else{
                         exit(23);
                     }

                     echo(" </instruction>\n");


                     break;

                 ///<INSTRUCTION> <VARIABLE> <SYMBOL>
                 case'MOVE':
                 case 'INT2CHAR':
                 case 'STRLEN':
                 case 'TYPE':
                 case 'NOT':

                     if(count($lexemes)!=3){
                         exit(23);
                     }

                     echo(" <instruction order=\"".$in_count."\" opcode=\"".strtoupper($lexemes[0])."\">\n");
                     if(check_variable($lexemes[1])){
                         echo("  <arg1 type=\"var\">".$lexemes[1]."</arg1>\n");
                     }
                     else{
                         exit(23);
                     }

                     if(check_symbol($lexemes[2])){
                         echo("  <arg2 type=\"".substr($lexemes[2], 0, strpos($lexemes[2], "@"))."\">".convert_string(substr($lexemes[2], strpos($lexemes[2], "@")+1,strlen($lexemes[2])-strpos($lexemes[2], "@")))."</arg2>\n");

                     }
                     else if(check_variable($lexemes[2])){
                         echo("  <arg2 type=\"var\">".$lexemes[2]."</arg2>\n");
                     }
                     else{
                         exit(23);
                     }

                     echo(" </instruction>\n");
                     break;

                 ///<INSTRUCTION>
                 case'PUSHFRAME':
                 case'CREATEFRAME':
                 case'POPFRAME':
                 case 'BREAK':
                     if(count($lexemes)!=1){
                         exit(23);
                     }
                     echo(" <instruction order=\"".$in_count."\" opcode=\"".strtoupper($lexemes[0])."\"/>\n");

                     break;

                 ///<INSTRUCTION> <LABEL>
                 case 'LABEL':
                 case 'JUMP':
                 case'CALL':
                     echo(" <instruction order=\"".$in_count."\" opcode=\"".strtoupper($lexemes[0])."\">\n");
                     if(check_label($lexemes[1])){
                         echo("  <arg1 type=\"label\">".$lexemes[1]."</arg1>\n");
                     }
                     else{
                         exit(23);
                     }

                     echo(" </instruction>\n");

                     if(count($lexemes)>2){
                         exit(23);
                     }
                     break;

                 ///<INSTRUCTION>
                 case'RETURN':
                     if(count($lexemes)!=1){
                         exit(23);
                     }
                     echo(" <instruction order=\"".$in_count."\" opcode=\"RETURN\"/>\n");

                     break;

                 ///<INSTRUCTION> <VARIABLE> <SYMBOL> <SYMBOL>
                 case 'ADD':
                 case 'SUB':
                 case 'MUL':
                 case 'IDIV':

                 case 'LT':
                 case 'GT':
                 case 'EQ':

                 case 'AND':
                 case 'OR':

                 case 'STRI2INT':

                 case 'GETCHAR':
                 case 'CONCAT':
                 case 'SETCHAR':
                     if(count($lexemes)!=4){
                         exit(23);
                     }
                     echo(" <instruction order=\"".$in_count."\" opcode=\"".strtoupper($lexemes[0])."\">\n");
                     if(check_variable(trim($lexemes[1]))){
                         echo("  <arg1 type=\"var\">".$lexemes[1]."</arg1>\n");
                     }
                     else{
                         exit(23);
                     }
                     if(check_symbol($lexemes[2])){
                         echo("  <arg2 type=\"".substr($lexemes[2], 0, strpos($lexemes[2], "@"))."\">".convert_string(substr($lexemes[2], strpos($lexemes[2], "@")+1,strlen($lexemes[2])-strpos($lexemes[2], "@")))."</arg2>\n");
                     }
                     else if(check_variable($lexemes[2])){
                         echo("  <arg2 type=\"var\">".$lexemes[2]."</arg2>\n");
                     }
                     else{
                         exit(23);
                     }

                     if (check_symbol($lexemes[3])) {
                         echo("  <arg3 type=\"".substr($lexemes[3], 0, strpos($lexemes[3], "@"))."\">".convert_string(substr($lexemes[3], strpos($lexemes[3], "@") + 1, strlen($lexemes[3]) - strpos($lexemes[3], "@")))."</arg3>\n");
                     } else if (check_variable($lexemes[3])) {
                         echo("  <arg3 type=\"var\">" .$lexemes[3] . "</arg3>\n");
                     } else {
                         exit(23);
                     }

                     echo(" </instruction>\n");
                     break;

                 ///<INSTRUCTION> <VARIABLE> <TYPE>
                 case 'READ':
                     if(count($lexemes)!=3){
                         exit(23);
                     }
                     echo(" <instruction order=\"".$in_count."\" opcode=\"".strtoupper($lexemes[0])."\">\n");
                     if(check_variable($lexemes[1])){
                         echo("  <arg1 type=\"var\">".$lexemes[1]."</arg1>\n");
                     }
                     else{
                         exit(23);
                     }

                     if(($lexemes[2] == "bool")||($lexemes[2] == "int")||($lexemes[2] == "string")){
                         echo("  <arg2 type=\"type\">".$lexemes[2]."</arg2>\n");
                     }
                     else{
                         exit(23);
                     }

                     echo(" </instruction>\n");
                     break;

                 ///<INSTRUCTION> <VARIABLE>
                 case 'WRITE':
                 case 'EXIT':
                 case 'DPRINT':
                 case 'PUSHS':
                     if(count($lexemes)!=2){
                         exit(23);
                     }
                     echo(" <instruction order=\"".$in_count."\" opcode=\"".strtoupper($lexemes[0])."\">\n");
                     if(check_symbol($lexemes[1])){
                         echo("  <arg1 type=\"".substr($lexemes[1], 0, strpos($lexemes[1], "@"))."\">".convert_string(substr($lexemes[1], strpos($lexemes[1], "@")+1, strlen($lexemes[1])-strpos($lexemes[1], "@")))."</arg1>\n");
                     }
                     else if(check_variable($lexemes[1])){
                         echo("  <arg1 type=\"var\">".$lexemes[1]."</arg1>\n");
                     }
                     else{
                         exit(23);
                     }
                     echo(" </instruction>\n");

                     break;

                 ///<INSTRUCTION> <LABEL> <SYMBOL> <SYMBOL>
                 case 'JUMPIFEQ':
                 case 'JUMPIFNEQ':
                     if(count($lexemes)!=4){
                         exit(23);
                     }
                     echo(" <instruction order=\"".$in_count."\" opcode=\"".strtoupper($lexemes[0])."\">\n");
                     if(check_label($lexemes[1])){
                         echo("  <arg1 type=\"label\">".$lexemes[1]."</arg1>\n");
                     }
                     else{
                         exit(23);
                     }

                     if(check_symbol($lexemes[2])){
                         echo("  <arg2 type=\"".substr($lexemes[2], 0, strpos($lexemes[2], "@"))."\">".convert_string(substr($lexemes[2], strpos($lexemes[2], "@")+1,strlen($lexemes[2])-strpos($lexemes[2], "@")))."</arg2>\n");
                     }
                     else if(check_variable($lexemes[2])){
                         echo("  <arg2 type=\"var\">".$lexemes[2]."</arg2>\n");
                     }
                     else{
                         exit(23);
                     }

                     if(check_symbol($lexemes[3])){
                         echo("  <arg3 type=\"".substr($lexemes[3], 0, strpos($lexemes[3], "@"))."\">".convert_string(substr($lexemes[3], strpos($lexemes[3], "@")+1,strlen($lexemes[3])-strpos($lexemes[3], "@")))."</arg3>\n");
                     }
                     else if(check_variable($lexemes[3])){
                         echo("  <arg3 type=\"var\">".$lexemes[3]."</arg3>\n");
                     }
                     else{
                         exit(23);
                     }

                 echo(" </instruction>\n");
                     break;
                     ///rest after comment, no problem, just adjust the instruction counter
                 case '#':
                     $in_count --;
                    break;
                 ///unknown instruction
                 default:
                     exit(22);

             }

        }
        echo("</program>\n"); //end of program

?>
