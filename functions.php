<?php 

 use \Hcode\Model\User;	
 use \Hcode\Model\Cart;	

 function formatPrice($vlprice)
 {

 	return number_format($vlprice, 2, ",", ".");
 }

 function formatDate($date)
 {

   return date('d/m/Y', strtotime($date));

 }


 function checkLogin($indamin = true)
{

	return User::checkLogin($indamin);
}


function getUserName()
{

	 $user = User::getFromSession();


	 return $user->getdesperson();
}

function getUserPhoto()
{

	 $user = User::getFromSession();
  
	 return $user->getdesphoto();
}

function getUserMemberSince()
{
     
	 $user = User::getFromSession();
  
     $dataSince = date('F', strtotime($user->getdtregister())).' de '.date('Y', strtotime($user->getdtregister()));

	 return $dataSince;
}

function getCartNrQtd()
{

 $cart= Cart::getFromSession();

 $totals = $cart->getProductsTotals();

 return $totals['nrqtd'];

}

function getCartVlSubTotal()
{

 $cart= Cart::getFromSession();

 $totals = $cart->getProductsTotals();

 return formatPrice($totals['vlprice']);

}
 ?>
