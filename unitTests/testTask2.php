<?php
require_once '../vendor/autoload.php';
require_once '../tasks/task2.php';

class testTask2 extends PHPUnit\Framework\TestCase {

    /**
     * @dataProvider providerTask2
     */
    public function test($res,$k){
        $this->assertEquals($res,luckyTickets($k));
    }

    public function providerTask2(){
        return array(
            [10,2],
            [670,4],
            [55252,6],
            [4816030,8]
        );
    }

}

?>