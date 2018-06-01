<?php
class System{
    private $lessons = [];
    public $tariffs = [];
    public $types = [];

    function __construct($tariffs,$types){
        $this->tariffs = $tariffs;
        $this->types = $types;
    }

    public function new_type($type_name){
        $this->types[] = strtolower($type_name);
    }

    public function new_tariff($tariff,$price){
        $this->tariffs[strtolower($tariff)] = $price;
    }

    public function create_lesson($type,$tariff,$duration){
        if(in_array($type,$this->types)&&in_array($tariff,array_keys($this->tariffs))&&is_int($duration)&&$duration>0){
            $lesson = new Lesson($type,$tariff,$duration,$this->tariffs[$tariff]);
            $this->lessons[] = $lesson;
            return $lesson;
        }
        else{
            throw new \InvalidArgumentException('Invalid arguments given');
        }
    }
    public function get_lessons_cost(){
        $lessons = $this->lessons;
        $price = 0;
        foreach ($lessons as $lesson){
            $price += $lesson->duration*$lesson->price;
        }
        return $price;
    }
}

Class Lesson {
    public $type;
    public $duration;
    public $tariff;
    public $price;

    function __construct($type,$tariff,$duration,$price){
        $this->type = $type;
        $this->tariff = $tariff;
        $this->duration = $duration;
        $this->price = $price;
    }


}

$system = new System(['fixed'=>200,'hourly'=>100],['speaking','grammar']);


?>
<div>
    <h2>Создаем объект класса System c параметрами</h2>
    <p>Тарифы: <pre><? print_r($system->tariffs)?></pre></p>
    <p>Типы занятий: <pre><? print_r($system->types)?></pre></p>
</div>
<?
$system->new_type('reading');

$system->new_tariff('daily',400);
?>
<h2>Добавляем новый тип и тариф </h2>
<p>Тариф "daily" по цене 400: <pre><? print_r($system->tariffs)?></pre></p>
<p>Тип занятий "reading": <pre><? print_r($system->types)?></pre></p>
<?
$today_lesson = $system->create_lesson('reading','daily',1);

$weekly_lessons = $system->create_lesson('speaking','hourly',20);

?>
<h2>Создаем два объекта класса Lesson</h2>
<p>Тип(reading), Тариф(daily), Длительность(1) <pre><? print_r($today_lesson)?></pre></p>
<p>Тип(speaking), Тариф(hourly), Длительность(20) <pre><? print_r($weekly_lessons)?></pre></p>
<h2>Подсчитуем стоимость всех уроков: <pre><? print_r($system->get_lessons_cost())?></h2>
</div>





