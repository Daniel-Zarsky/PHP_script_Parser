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

        $head = false;
        $in_count =0;
        // loading input
        while($line = fgets(STDIN)){

            if(!$head){  //header check

                if(trim($line) != ".IPPcode23"){

                    exit(21); //missing header
                }
                echo("<program language=\"IPPcode23\">\n");
                $head = true;
                continue;
            }
            $in_count++;
            $lexemes = explode(" ", trim($line)); //dividing lexemes

            switch(strtoupper($lexemes[0])){ //instruction
                case 'MOVE':
                    echo("\t<instruction order=".$in_count." opcode=".strtoupper($lexemes[0]).">\n");

                    echo("\t</instruction>\n"); //end of instruction
                    break;
                case'DEFVAR':
                    echo("\t<instruction order=".$in_count." opcode=".strtoupper($lexemes[0]).">\n");

                    if(preg_match("/(LF|GF|TF)@[-a-zA-Z_$&%*!?][-a-zA-Z_$&%*!?0-9]*/", $lexemes[1]))//identifier check
                    {
                        echo("\t\t<arg1 type=\"var\">".$lexemes[1]."</arg1>\n"); //valid
                    } else {
                        exit(23); //invalid identifier
                    }

                    echo("\t</instruction>\n"); //end of instruction
                    break;

                default: //unknown lexeme
                    if(isValidComment($lexemes[0])){ //is it comment?
                        $in_count--;
                    }
                    else{
                        exit(22); //unknown instruction
                    }


            }



        }



    

?>
