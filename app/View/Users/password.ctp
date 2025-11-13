<?= $this->Html->css(
        array(
            'pages/layouts',
            '/vendors/bootstrapvalidator/css/bootstrapValidator.min',
            'pages/wizards',
            
            '/vendors/chosen/css/chosen',
            '/vendors/bootstrap-switch/css/bootstrap-switch.min',
            '/vendors/jasny-bootstrap/css/jasny-bootstrap.min',
            '/vendors/fileinput/css/fileinput.min'
        ),
        array('inline'=>false))
?>

<div id="content" class="bg-container">
    <header class="head">
        <div class="main-bar row">
            <div class="col-lg-12">
                <h4 class="nav_top_align"><i class="fa fa-th"></i>Cambiar Password</h4>
                <?php echo $this->Session->flash(); ?>
            </div>
        </div>
    </header>
    <div class="outer">
        <div class="inner bg-container ">
            <div class="row">
                <?php echo $this->Form->create('User');?>
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-block m-t-20">
                            <h2>Antes de continuar, por favor cambia tu contraseña para que mejoremos la seguridad de nuestro sistema. ¡Muchas gracias!</h2>
                            <?php
                                echo $this->Form->input('id');
                                echo $this->Form->input('password', array('id'=>'p1','div' => 'col-xs-12 col-md-6','onkeydown'=>'return (event.keyCode!=13)','class'=>'form-control','type'=>'password','value'=>'','label'=>'Contraseña'));
                                echo $this->Form->input('password2', array('id'=>'p2','div' => 'col-xs-12 col-md-6','onkeydown'=>'return (event.keyCode!=13)','class'=>'form-control','type'=>'password','label'=>'Confirmar Contraseña','onchange'=>'javascript:ValidaPassword()'));
                            ?>
                        </div>
                        <script>
                            function ValidaPassword(){
                                if (document.getElementById('p1').value==document.getElementById('p1').value){
                                    document.getElementById('submit_div').style.display="";
                                }else{
                                    document.getElementById('submit_div').style.display="none";
                                }
                            }
                        </script>
                        <div class="row">
                            <div class="col-md-12" id="submit_div" style="display:none">
                                <?php echo $this->Form->button('Cambiar Password',array('type'=>'submit','class'=>'btn btn-responsive layout_btn_prevent btn-success','style'=>'width:100%'))?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->Html->script(
        array(
            'pages/layouts',
            '/vendors/bootstrapvalidator/js/bootstrapValidator.min',
            '/vendors/twitter-bootstrap-wizard/js/jquery.bootstrap.wizard.min',
            'pages/wizard',
            
            '/vendors/chosen/js/chosen.jquery',
            '/vendors/bootstrap-switch/js/bootstrap-switch.min',
            'form',
            'pages/form_elements'
        ),
        array('inline'=>false))
?>