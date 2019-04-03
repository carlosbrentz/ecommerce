<?php 

use Hcode\Page;
use Hcode\Model\Product;

$app->get('/', function() {
    
    $products = Product::listALL();

	$page = new Page();

	$page->setTpl("index",[
      'products'=>Product::checkList($products)
	]);


});


 ?>