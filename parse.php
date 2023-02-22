<?php

       include 'parse_lib.php';

       ini_set('display_errors', 'stderr');

       echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");

        // arg check
        if ($argc > 1) {

            if ($argv[1] == "--help" && $argc == 2) { //help
                echo("help");
                exit(0);
            }
            else if(substr_compare($argv[1], "--source", 0, 8) == 0){ // source file
                $source_file = substr($argv[1], 9, strlen($argv[1]));
            }
            else exit(10); // bad argument
        }

        $header = false;
        $in_count =0;
        global $output;

        while($line = fgets(STDIN)){



            $line = cut_comment($line); //cut comment

              if(!$header){             //check header
                  if(trim($line) == ".IPPcode23"){
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

            // $lexemes = explode(" ", trim($line, '\n')); //split the input into lexemes, trim empty lines

            $lexemes = preg_split('/\s+/', $line);

             if($lexemes[0]=="" || str_contains($lexemes[0], "#")){ //empty array after cutting new line
                 continue;
             }

             $in_count ++; //new valid instruction
             $inst = $lexemes[0]; ///after cutting and trimming the first wor in line is instruction

             switch(trim(strtoupper($inst))){

                 case'DEFVAR':
                 case 'POPS':
                     echo(" <instruction order=\"".$in_count."\" opcode=\"".strtoupper($lexemes[0])."\">\n");
                     if(check_variable(trim($lexemes[1]))){
                         echo("  <arg1 type=\"var\">".convert_string($lexemes[1])."</arg1>\n");
                     }
                     else{
                         exit(23);
                     }

                     echo(" </instruction>\n");

                     if(count($lexemes)>2){
                         exit(23);
                     }
                     break;

                 case'MOVE':

                 case 'INT2CHAR':
                 case 'STRLEN':
                 case 'TYPE':
                 case 'NOT':

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

                     if(count($lexemes)>3){
                         exit(23);
                     }

                     echo(" </instruction>\n");
                     break;

                 case'PUSHFRAME':
                 case'CREATEFRAME':
                 case'POPFRAME':
                 case 'BREAK':
                     echo(" <instruction order=\"".$in_count."\" opcode=\"".strtoupper($lexemes[0])."\"/>\n");
                     if(count($lexemes)>1){
                         exit(23);
                     }
                     break;

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

                 case'RETURN':
                     echo(" <instruction order=\"".$in_count."\" opcode=\"RETURN\"/>\n");
                     if(count($lexemes)>1){
                         exit(23);
                     }
                     break;


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


                     if(count($lexemes)>4){
                         exit(23);
                     }

                     echo(" </instruction>\n");
                     break;

                 case 'READ':
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
                     if(count($lexemes)>3){
                         exit(23);
                     }

                     echo(" </instruction>\n");
                     break;

                 case 'WRITE':
                 case 'EXIT':
                 case 'DPRINT':
                 case 'PUSHS':
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

                         if(count($lexemes)>2){
                             exit(23);
                         }

                     break;

                 case 'JUMPIFEQ':
                 case 'JUMPIFNEQ':
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

                     if(count($lexemes)>4){
                         exit(23);
                     }

                 echo(" </instruction>\n");
                     break;
                 case '#':
                     $in_count --;
                    break;

                 default:
                     exit(22);
















             }




        }

        echo("</program>\n");

    

?>
