<?php
$p = "";
if (!empty($_GET['p'])) 
	$p = $_GET['p'];
?>
<div class='tm_sub'>
	<a href='/eshop.php5?p=cat' class='tm_sub_a'><?=($p != "orders" ? '<b>':'')?>Каталог товаров<?=($p != "orders" ? '</b>':'')?></a>
<? if (! empty($_COOKIE['INF_USER_LOGIN'])) { ?>
	|
	<a href='/eshop.php5?p=orders' class='tm_sub_a'><?=($p == "orders" ? '<b>':'')?>Корзина<?=($p == "orders" ? '</b>':'')?></a>
<? } ?>
</div>
