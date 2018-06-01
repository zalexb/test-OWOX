<?php
require_once '../vendor/autoload.php';
require_once '../tasks/task1.php';

class testTask1 extends PHPUnit\Framework\TestCase {

    /**
     * @dataProvider providerTask1
     */
    public function test($res,$s){
        $this->assertEquals($res,checkBrackets($s));
    }

    public function providerTask1(){
        return array(
            [true,'[5] * 3 - ( 4 - 7 * [3-6])'],
            [false,'( 5 * 3 [ 6 ) - 6]'],
            [true,'foo{bar([1,2,3])}'],
            [false,'foo{bar[(1,2,3])}'],
        );
    }

}
?>