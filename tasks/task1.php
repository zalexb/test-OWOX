<?php

function checkBrackets($s){
    $container = [];
    $brackets = "(){}[]";

    for ($i = 0; $i < strlen($s); $i++) {

        $current = $s[$i];
        $index = strrpos($brackets, $current);

        if (is_int($index) && $index >= 0) {

            if ($index & 1) {

                if (!count($container))
                    return false;

                $last_bracket = array_pop($container);

                if ($last_bracket != $brackets[$index - 1])
                    return false;

            } else {

                $container[] = $current;
            }
        }
    }

    return !count($container);
}

?>