<?php 

use Hcode\PageAdmin;
use Hcode\Model\User;


$app->get("/admin/users/:iduser/password", function($iduser){

  User::verifyLogin();

  $user = new User();

  $user->get((int)$iduser);

  $page = new PageAdmin();

  $page->setTpl("users-password",[
      'user'=>$user->getValues(),
      "msgError"=>User::getError(),
      "msgSuccess"=>User::getSuccess()
  ]);

});

$app->post("/admin/users/:iduser/password", function($iduser){

  User::verifyLogin();


   if (!isset($_POST['despassword']) || $_POST['despassword'] === ''){

      User::setError("Preencha a senha atual.");
      header('Location: /admin/users/'.$iduser.'/password');
      exit;

   }

if (!isset($_POST['despassword-confirm']) || $_POST['despassword-confirm'] === ''){
   
      User::setError("Preencha a confirmação da nova senha.");
       header('Location: /admin/users/'.$iduser.'/password');
      exit;

   }

   if ($_POST['despassword'] !== $_POST['despassword-confirm']){
   
      User::setError("Nova senha e confirmação não são iguais.");
       header('Location: /admin/users/'.$iduser.'/password');
      exit;

   }

   $user = new User();

    $user->get((int)$iduser);

    $user->setPassword(User::getPassworHash($_POST['despassword']));

     User::setSuccess("Senha alterada com sucesso.");
     header('Location: /admin/users/'.$iduser.'/password');
     exit;

});

$app->get("/admin/users",function() {

  User::verifyLogin();

  $search = (isset($_GET['search'])) ? $_GET['search'] : "";

  $page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;

  $pagination = User::getPage($search, $page);

  $pages= [];

  for ($x= 0; $x < $pagination['pages']; $x++)
  {

    array_push($pages, [
      'href'=>'/admin/users?'.http_build_query([
         'page'=>$x+1,
         'search'=>$search
      ]),
      'text'=>$x+1
    ]);
  }
 
  foreach ($pagination['data'] as &$row )
  {
     $row['desperson'] = utf8_encode($row['desperson']);
  } 

  $page = new PageAdmin();

  $page->setTpl("users", array(
     "users"=>$pagination['data'],
     "search"=>$search,
     "pages"=>$pages
  ));

});

$app->get("/admin/users/create",function() {


  User::verifyLogin();

  $page = new PageAdmin();

  $user = new User();

  $page->setTpl("users-create", [
      "msgError"=>User::getError(),
      "msgSuccess"=>User::getSuccess(),
      "user"=>$user->getValues()  
  ]);

});

$app->get("/admin/users/:iduser/delete", function($iduser){

  User::verifyLogin();

  $user = new User();

  $user->get((int)$iduser);

  $user->delete();

  header("location: /admin/users");

  exit;

});


$app->get("/admin/users/:iduser",function($iduser) {


  User::verifyLogin();

  $user = new User();

  $user->get((int)$iduser);

  
  $page = new PageAdmin();

  $page->setTpl("users-update", array(
    "user"=>$user->getValues()  
  ));

});

$app->post("/admin/users/create", function(){

  User::verifyLogin();

  $user = new User();

  $file = $_FILES["file-upload"];
  

  if (!isset($_POST['desperson']) || $_POST['desperson'] === ''){

      User::setError("Preencha o nome.");
      header('Location: /admin/users/create');
      exit;

   }

    $_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;
  
 if ($file["error"]) {

       User::setError("Erro no arquivo de foto. Error: ".$file["error"]);
       header('Location: /admin/users/create');
       exit;
  }
  $dirUploads =  $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "res" . DIRECTORY_SEPARATOR . "admin" . DIRECTORY_SEPARATOR . "dist" . DIRECTORY_SEPARATOR . "img" . DIRECTORY_SEPARATOR;

  if (!move_uploaded_file($file["tmp_name"], $dirUploads. $file["name"])){

       User::setError("Erro no carregamento da foto, tente novamente");
       header('Location: /admin/users/create');
       exit;
    }


  $user->setData($_POST);
  

  $user->save();

  rename($dirUploads. $file["name"], $dirUploads.$user->getiduser().".jpg");

  header("Location: /admin/users/create");

  exit;

});

$app->post("/admin/users/:iduser", function($iduser){

  User::verifyLogin();

  $user = new User();

  $file = $_FILES["file-upload"];

  $user->get((int)$iduser);

  

  if ($file["error"]) {

       User::setError("Erro no arquivo de foto. Error: ".$file["error"]);
       header('Location: /admin/users/create');
       exit;
  }
  $dirUploads =  $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "res" . DIRECTORY_SEPARATOR . "admin" . DIRECTORY_SEPARATOR . "dist" . DIRECTORY_SEPARATOR . "img" . DIRECTORY_SEPARATOR;

  if (!move_uploaded_file($file["tmp_name"], $dirUploads. $file["name"])){

       User::setError("Erro no carregamento da foto, tente novamente");
       header('Location: /admin/users/create');
       exit;
    }


  $user->setData($_POST);

  $user->update();
  
  rename($dirUploads. $file["name"], $dirUploads.$user->getiduser().".jpg");

  header("Location: /admin/users");

  exit;

});


 ?>