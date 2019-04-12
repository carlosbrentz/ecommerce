<?php if(!class_exists('Rain\Tpl')){exit;}?><!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Lista de Usuários
  </h1>
</section>

<!-- Main content -->
<section class="content">

  <div class="row">
  	<div class="col-md-12">
  		<div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Editar Usuário</h3>
        </div>
        <?php if( $msgError != '' ){ ?>
        <div class="alert alert-danger alert-dismissible" style="margin:10px">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <p><?php echo htmlspecialchars( $msgError, ENT_COMPAT, 'UTF-8', FALSE ); ?></p>
        </div>
        <?php } ?>
        <!-- /.box-header -->
        <!-- form start -->
        <form role="form" name="atualizar_usuario" action="/admin/users/<?php echo htmlspecialchars( $user["iduser"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" method="post" enctype = "multipart/form-data"
          <div class="box-body">
            <div class="form-group">
              <label for="desperson">Nome</label>
              <input type="text" class="form-control" id="desperson" name="desperson" placeholder="Digite o nome" value="<?php echo htmlspecialchars( $user["desperson"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">
            </div>
            <div class="form-group">
              <label for="deslogin">Login</label>
              <input type="text" class="form-control" id="deslogin" name="deslogin" placeholder="Digite o login"  value="<?php echo htmlspecialchars( $user["deslogin"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">
            </div>
            <div class="form-group">
              <label for="nrphone">Telefone</label>
              <input type="tel" class="form-control" id="nrphone" name="nrphone" placeholder="Digite o telefone"  value="<?php echo htmlspecialchars( $user["nrphone"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">
            </div>
            <div class="form-group">
              <label for="desemail">E-mail</label>
              <input type="email" class="form-control" id="desemail" name="desemail" placeholder="Digite o e-mail" value="<?php echo htmlspecialchars( $user["desemail"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">
            </div>
            <div class="checkbox">
              <label>
                <input type="checkbox" name="inadmin" value="1" <?php if( $user["inadmin"] == 1 ){ ?>checked<?php } ?>> Acesso de Administrador
              </label>
            </div>
            <div class="form-group">
              <label for="desphoto">Foto Atual</label>
               <img src="<?php echo htmlspecialchars( $user["desphoto"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" style="height:64px;">
            </div>   
            <div class="form-group">
              <label for="desphoto1">Nova Foto</label>
              <input type="file" accept="image/jpeg" name="file-upload">
            </div>
          </div>
          <!-- /.box-body -->
          <div class="box-footer">
            <button type="button" onclick="checkFields();" class="btn btn-primary">Atualizar</button>
          </div>
          <script>
                function checkFields(){
                  if (document.getElementById("desperson").value.trim()=='')  {
                      alert('Preencha o nome do usuário');
                      return false;
                  }
                  if (document.getElementById("deslogin").value.trim()=='')  {
                      alert('Preencha o login do usuário');
                      return false;
                  }
                  if (document.getElementById("desemail").value.trim()=='')  {
                      alert('Preencha o e-mail do usuário');
                      return false;
                  }
                  document.atualizar_usuario.submit();
                }
          </script>  

        </form>
      </div>
  	</div>
  </div>

</section>
<!-- /.content -->
</div>
<!-- /.content-wrapper -->