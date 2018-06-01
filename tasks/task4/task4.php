<?php
  const DB_NAME = "test-owox";
  const DB_HOST = "localhost";
  const DB_PASSWD = "";
  const DB_USER = "root";

"SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = \"+00:00\";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `test-owox`
--

-- --------------------------------------------------------

--
-- Структура таблицы `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(64) NOT NULL,
  `phone` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `ip` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Структура таблицы `product_order`
--

CREATE TABLE `product_order` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`);

--
-- Индексы таблицы `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `product_order`
--
ALTER TABLE `product_order`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT для таблицы `product_order`
--
ALTER TABLE `product_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`);

--
-- Ограничения внешнего ключа таблицы `product_order`
--
ALTER TABLE `product_order`
  ADD CONSTRAINT `product_order_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `product_order_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;
";
class Db
{
    protected $dbc;

    protected $result;

    function __construct(){

        $this->dbc = new mysqli(DB_HOST, DB_USER, DB_PASSWD, DB_NAME);
        if ($this->dbc->connect_error) {
            die();
        }
        $this->dbc->set_charset('utf8');

    }

    public function makeQuery($query){

        $this->result = $this->dbc->query($query);

        if (!$this->result) {
//			var_dump($query);
            die();
        }
        return (is_bool($this->result)) ? $this->result : $this->mysqli_fetch_all_my($this->result);
    }


    public function mysqli_fetch_all_my($rows){
        $arr=[];

        while ($row = mysqli_fetch_assoc($rows)) {
            $arr[] = $row;
        }

        return $arr;
    }


    public function insert_id(){

        return $this->dbc->insert_id;
    }

    function __destruct(){
        $this->dbc->close();
    }

}

$db = new Db();
echo '<pre>';
//Transaction
$product_ids = [3=>3,4=>1];
$client_id  = 1;
$ip = '123.23.123.123';
$errors = [];

$db->makeQuery('START TRANSACTION; ');

$order = $db->makeQuery('INSERT INTO `orders`(`client_id`, `created`, `ip`) VALUES ('.$client_id.',NOW(),"'.$ip.'")');

if(!$order)
    $errors['order'] = true;

$order_id = $db->insert_id();

foreach ($product_ids as $key=>$value) {

    $res_order = $db->makeQuery('INSERT INTO `product_order`(`product_id`, `order_id`, `amount`) VALUES ('.$key.','.$order_id.','.$value.')');

    if($res_order==false)
        $errors['product_order'] = true;
}


if(empty($errors)){
    $db->makeQuery('COMMIT;');
    print_r('<h3>INSERT ORDER WAS SUCCEEDED</h3>');
}
else{
    print_r($errors);
    $db->makeQuery('ROLLBACK;');
}

// Выборка по клиенту
$client_id  = 1;
$query = 'SELECT orders.id as order_id, orders.created as created, COUNT(product_order.id) as count_product, AVG(products.price) as avg_product_price
          FROM orders 
          INNER JOIN product_order ON order_id = orders.id
          LEFT OUTER JOIN products ON product_order.product_id = products.id
          WHERE orders.client_id='.$client_id.'
          GROUP BY orders.id';
echo '<h2>Выборка по клиенту с id = 1</h2>';
print_r($db->makeQuery($query));

// Выборка по товарам
$product_ids = [1,2];
$query = 'SELECT orders.id as order_id, orders.created as created, COUNT(product_order.id) as count_product, AVG(products.price) as avg_product_price
          FROM orders 
          INNER JOIN product_order ON order_id = orders.id
          LEFT OUTER JOIN products ON product_order.product_id = products.id ';

$i = 0;
foreach ($product_ids as $id) {
    if($i==0)
        $query .= 'WHERE product_order.product_id=' . $id;
    else
        $query .= ' OR product_order.product_id=' . $id;
    $i++;
}
$query .= ' GROUP BY orders.id';

echo '<h2>Выборка по поварам с id = 1,2</h2>';
print_r($db->makeQuery($query));

//Выборка 10 заказов

$query = 'SELECT orders.id as order_id, orders.created as created, COUNT(product_order.id) as count_product, AVG(products.price) as avg_product_price
          FROM orders 
          INNER JOIN product_order ON order_id = orders.id
          LEFT OUTER JOIN products ON product_order.product_id = products.id
          GROUP BY orders.id DESC
          HAVING COUNT(product_order.id) > 1
          ORDER BY orders.created DESC
          LIMIT 10';
echo '<h2>Выборка последних 10 заказов</h2>';
print_r($db->makeQuery($query));

?>
