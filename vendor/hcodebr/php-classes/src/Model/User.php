<?php 

 namespace Hcode\Model;

 use \Hcode\DB\Sql;
 use \Hcode\Model;
 use \Hcode\Mailer;

 class User extends Model{

 const SESSION = "User"	;
 const SECRET  = "HcodePhp7_Secret";
 const ERROR   = "UserError";
 const ERROR_REGISTER = "UserErrorRegister";
 const SUCCESS = "UserSuccess";

public static function getFromSession()
{

  $user = new User();

  if (isset($_SESSION[User::SESSION]) && (int)$_SESSION[User::SESSION]['iduser'] > 0){

      $user->setData($_SESSION[User::SESSION]);

  } 
  return $user; 

}

public static function checkLogin($inadmin = true)
{

  if (
    !isset($_SESSION[User::SESSION])
    || 
    !$_SESSION[User::SESSION]
    ||
    !(int)$_SESSION[User::SESSION]["iduser"] > 0
   ){
      //Não está logado
      return false;
    }else{

      if ($inadmin === true  && $_SESSION[User::SESSION]['inadmin'] === 1){

          return true;

      }else if ($inadmin === false){

          return true;

      } else{
         return false;
      }

    } 

}


 public static function login($login, $password)
 {

   $sql = new Sql();

   $results = $sql->select("select *  from tb_users u inner join tb_persons p  on u.idperson = p.idperson
     where u.deslogin = :LOGIN", array(":LOGIN"=>$login));

  

   if (count($results) ===0)
   {
   	  throw new \Exception("Usuário inexistente ou senha inválida");
   }

   $data = $results[0];
 //var_dump($password);exit;
   if (password_verify($password, $data["despassword"]) === true)
   {
   	  $user = new User();

      $data['desperson'] = utf8_encode($data['desperson']);
      
   	  $user->setData($data);

   	  $_SESSION[User::SESSION] = $user->getValues();

   	  return $user;


   }else{
   	 throw new \Exception("Usuário inexistente ou senha inválida");
   }

}

public static function verifyLogin($inadmin = true)

{

	if (!User::checkLogin($inadmin)){

    if ($inadmin){
      	header("Location: /admin/login");
    }else{
        header("Location: /login");
    }      
		exit;
	}
}

 public static function logout()
{

	 $_SESSION[User::SESSION] = NULL;
}

public static function listAll()
{

  $sql = new Sql();

  return  $sql->select("select * from tb_users a inner join tb_persons b using(idperson) order by b.desperson");

}

public function save()

{
 
  $sql = new Sql();

  $results =  $sql->select 	("call sp_users_save(:desperson, :deslogin,  :despassword, :desemail, :nrphone, :inadmin)", array(
          ":desperson"=> utf8_decode($this->getdesperson()),
          ":deslogin"=>$this->getdeslogin(),
          ":despassword"=>$this->getPassworHash($this->getdespassword()),
          ":desemail"=>$this->getdesemail(),
          ":nrphone"=>$this->getnrphone(),
          ":inadmin"=>$this->getinadmin()
  )); 

  if (count($results) === 0) {

       throw new \Exception("Erro na criação do usuário");
   
  }
   $this->	setData($results[0]);
}

public function get($iduser)
{

	$sql = new Sql();

	$results = $sql->select("select * from tb_users a inner join tb_persons b using (idperson) where a.iduser = :iduser",  array(
      ":iduser"=>$iduser

	));

   $data = $results[0]; 

   $data['desperson'] = utf8_encode($data['desperson']);

	 $this->setData($data);
}


public function checkPhoto()
       {

           if (file_exists(
            $_SERVER['DOCUMENT_ROOT']. DIRECTORY_SEPARATOR .
            "res"  . DIRECTORY_SEPARATOR .
            "admin" . DIRECTORY_SEPARATOR .
            "dist"  . DIRECTORY_SEPARATOR .
            "img"  . DIRECTORY_SEPARATOR . 
            $this->getiduser(). ".jpg"
           )){

            $url =  "/res/admin/dist/img/" . $this->getiduser() . ".jpg";

           }else{

             $url =  "/res/admin/dist/img/boxed-bg.jpg";

           }
           return $this->setdesphoto($url);

       }

public function getValues()
    {

         $this->checkPhoto();

         $values = parent::getValues();

         return $values;


    }
public function update($encryptPassword  = 0)

{
 
  $sql = new Sql();
  
  if ($encryptPassword === 1){
    $password = $this->getdespassword();
  }else{
    $password = $this->getPassworHash($this->getdespassword());
  }
  
  $results =  $sql->select 	("call sp_usersupdate_save(:iduser, :desperson, :deslogin,  :despassword, :desemail, :nrphone, :inadmin)", array(
  	      ":iduser"=> $this->getiduser(),
          ":desperson"=>utf8_decode($this->getdesperson()),
          ":deslogin"=>$this->getdeslogin(),
          ":despassword"=>$password,
          ":desemail"=>$this->getdesemail(),
          ":nrphone"=>$this->getnrphone(),
          ":inadmin"=>$this->getinadmin()
  ));  

   if (count($results) === 0) {

       throw new \Exception("Erro na atualização do usuário");
   
  }

   $this->	setData($results[0]);
}

public function delete()
{
    $sql = new Sql();

    $sql->query("call sp_users_delete(:iduser)", array(
       ":iduser"=>$this->getiduser()
    ));

}

 public static function getForgot($email, $inadmin = true)
{

     $sql = new Sql();

     $results = $sql->select("select * from tb_persons a inner join tb_users b using (idperson) where a.desemail = :email", array (":email"=>$email));
     if (count($results) === 0)
     {
       throw new \Exception("Não foi possível recuperar a senha.");
     }
     else{

       $data  = $results[0];

       $results2 = $sql->select("call sp_userspasswordsrecoveries_create(:iduser, :desip)",array(
        ":iduser"=>$data["iduser"], ":desip"=>$_SERVER["REMOTE_ADDR"]));

       if (count($results2) === 0)
       {
        throw new \Exception("Não foi possível recuperar a senha.");
       }
       else{
         $dataRecovery = $results2[0];
         $iv = random_bytes(openssl_cipher_iv_length('aes-256-cbc'));
         $code = openssl_encrypt($dataRecovery['idrecovery'], 'aes-256-cbc', User::SECRET, 0, $iv);
         $result = base64_encode($iv.$code);

         if ($inadmin === true) {
                $link = "http://www.hcodecommerce.com.br/admin/forgot/reset?code=$result";
         } else {
                 $link = "http://www.hcodecommerce.com.br/forgot/reset?code=$result";
         } 
         $mailer = new Mailer($data['desemail'], $data['desperson'], "Redefinir senha da Hcode Store", "forgot", array(
                 "name"=>$data["desperson"],
                 "link"=>$link
          )); 
          $mailer->send();
          return $link;
       }
     }
}

public static function validForgotDecrypt($result)
 {
     $result = base64_decode($result);
     $code = mb_substr($result, openssl_cipher_iv_length('aes-256-cbc'), null, '8bit');
     $iv = mb_substr($result, 0, openssl_cipher_iv_length('aes-256-cbc'), '8bit');;
     $idrecovery = openssl_decrypt($code, 'aes-256-cbc', User::SECRET, 0, $iv);
     $sql = new Sql();
     $results = $sql->select("
         SELECT *
         FROM tb_userspasswordsrecoveries a
         INNER JOIN tb_users b USING(iduser)
         INNER JOIN tb_persons c USING(idperson)
         WHERE
         a.idrecovery = :idrecovery
         AND
         a.dtrecovery IS NULL
         AND
         DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW();
     ", array(
         ":idrecovery"=>$idrecovery
     ));

     if (count($results) === 0)
     {
         throw new \Exception("Não foi possível recuperar a senha.");
     }
     else
     {
         return $results[0];
     }
 }

public static function setForgotUsed($idrecovery)
{

    $sql = new Sql();

    $sql->query("update tb_userspasswordsrecoveries set dtrecovery = NOW() where idrecovery = :idrecovery", array(":idrecovery"=>$idrecovery
    ));

}

 public function setPassword($password)

 {

   $sql = new Sql();

   $sql -> query("update tb_users set despassword = :password where iduser = :iduser",array(":password"=>$password,":iduser"=>$this->getiduser()
   ));

 }

 public static function setError($msg)
 {

   $_SESSION[User::ERROR] = $msg;
 }

 public static function getError()
 {

   $msg = (isset($_SESSION[User::ERROR]) && $_SESSION[User::ERROR]) ? $_SESSION[User::ERROR] : '';

   User::clearError();

   return $msg;
 }

 public static function clearError()
{

   $_SESSION[User::ERROR] =  NULL;

}

public static function getPassworHash($password)
{

  return password_hash($password, PASSWORD_DEFAULT, [
    'cost'=>12]);
}

 public function setErrorRegister($msg)
 {

    $_SESSION[User::ERROR_REGISTER] = $msg;

 }

  public function getErrorRegister()
  {

    $msg = (isset($_SESSION[User::ERROR_REGISTER]) && $_SESSION[User::ERROR_REGISTER]) ? $_SESSION[User::ERROR_REGISTER] : '';
    User::clearErrorRegister();

    return $msg;
    
  }

  public static function clearErrorRegister()
  {

     $_SESSION[User::ERROR_REGISTER] =  NULL;

  }

  public static function checkLoginExist($login)
  {

      $sql = new Sql();

        $results = $sql->select("Select * from tb_users where deslogin = :deslogin",[
           ':deslogin'=>$login
      ]);
      return (count($results) > 0);
  }

    public static function setSuccess($msg)
     {

       $_SESSION[User::SUCCESS] = $msg;

     }

     public static function getSuccess()
     {

       $msg = (isset($_SESSION[User::SUCCESS]) && $_SESSION[User::SUCCESS]) ? $_SESSION[User::SUCCESS] : '';

       User::clearSuccess();

       return $msg;
     }

     public static function clearSuccess()
    {

       $_SESSION[User::SUCCESS] =  NULL;

    }

 public function getOrders()
{

   $sql = new Sql();

        $results = $sql->select("
            SELECT *
            FROM tb_orders a 
            INNER JOIN tb_ordersstatus b USING(idstatus)
            INNER JOIN tb_carts c USING(idcart)
            INNER JOIN tb_users d ON d.iduser = a.iduser
            INNER JOIN tb_addresses e USING(idaddress)
            INNER JOIN tb_persons f ON f.idperson = d.idperson
            WHERE a.iduser = :iduser
            ",[
                ":iduser"=>$this->getiduser()
        ]);

        return $results;
}
public static function getPage($search = '', $page = 1, $itensPerPage = 10)
        {


               $start = ($page - 1) * $itensPerPage;

               $sql = new Sql();
               if ($search === '') {

                  $results = $sql->select("
                   select SQL_CALC_FOUND_ROWS *
                   from tb_users a 
                   inner join tb_persons b using(idperson)
                   order by b.desperson
                   limit $start, $itensPerPage;");

               }else{
                 $results = $sql->select("
                   select SQL_CALC_FOUND_ROWS *
                   from tb_users a 
                   inner join tb_persons b using(idperson)
                   where b.desperson like :search or  b.desemail = :search1 or a.deslogin like :search2
                   order by b.desperson
                   limit $start, $itensPerPage;",[
                     'search'=>'%'.$search.'%' ,
                     'search1'=>$search,   
                     'search2'=>'%'.$search.'%' 
                   ]);
               }  

                $resultTotal = $sql->select("select FOUND_ROWS() as nrtotal;");
              
                return [
                  'data'=>$results,
                  'total'=>(int)$resultTotal[0]["nrtotal"],
                  'pages'=>ceil($resultTotal[0]["nrtotal"] / $itensPerPage)
                ];
        }


}
 ?>