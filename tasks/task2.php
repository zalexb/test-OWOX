<?php
// по Методу динамического программирования
function luckyTickets($k){
    $arr = [1,1,1,1,1,1,1,1,1,1];
    $result = 0;

    for($i = 0; $i < ($k/2-1); $i++){
// создание нового массива по формуле
        $new_arr=[];
// увеличиваем длину на 9
        $new_length = count($arr)+9;

        for($n = 0; $n < $new_length; $n++){
            $num = 0;
// берем 10 предыдущих значений
            for($j = 0; $j < 10; $j++){
                if (isset($arr[$n-$j])) {
                    $num += $arr[$n - $j];
                }
            }
// записываем сумму предыдущих значений в массив

            $new_arr[$n] = $num;
        }
// заменяем массив на новый
        $arr = $new_arr;
    }
// сумируем квадраты значений в новом массиве
    foreach ($arr as $value){
        $result += pow($value,2);
    }

    return $result;
}

?>