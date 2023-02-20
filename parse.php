<?php
 
 ini_set('display_errors', 'stderr');

        // arg check
        if ($argc > 1) {

            if ($argv[1] == "--help" && $argc == 2) { //help
                echo("help");
                exit(0);
            }
            else if(substr_compare($argv[1], "--source", 0, 8) == 0){ // source file
                $source_file = substr($argv[1], 9, strlen($argv[1]));
            }
            else exit(10); // error
        }

        // loading input
        while($line = fgets(STDIN)){
            echo($line);
        }



    

?>
