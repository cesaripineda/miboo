<?= $this->Html->css(
        array(
            '/vendors/bootstrapvalidator/css/bootstrapValidator.min',
            '/vendors/wow/css/animate',
            'pages/login1'
            )
        ,array('inline'=>false)
        );
 ?>

<body style="background-image: unset">
<div class="container wow fadeInDown" data-wow-delay="0.5s" data-wow-duration="2s">
    <div class="row">
        <div class="col-lg-8 push-lg-2 col-md-10 push-md-1 col-sm-10 push-sm-1 login_top_bottom">
            <div class="row">
                <div class="col-lg-8 push-lg-2 col-md-10 push-md-1 col-sm-12">
                    <div class="login_logo login_border_radius1">
                        <h3  class="text-center">
                            <img src="<?= $this->Html->url('/img/miboo_logo.png')?>" alt="josh logo" class="admire_logo">
                        </h3>
                    </div>
                    <div class="bg-white login_content login_border_radius">
                        <?php echo $this->Session->flash(); ?>
                        <?= $this->Form->create('User')?>
                            <div class="form-group">
                                <label for="email" class="form-control-label">Correo Electrónico</label>
                                <div class="input-group">
                                    <span class="input-group-addon input_email"><i class="fa fa-envelope text-primary"></i></span>
                                            <?php echo $this->Form->input('username',array('style'=>'width:100% !important','class'=>'form-control form-control-md','placeholder'=>'Usuario','label'=>false))?>
                                </div>
                            </div>
                            <!--</h3>-->
                            <div class="form-group">
                                <label for="password" class="form-control-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-addon addon_password"><i
                                            class="fa fa-lock text-primary"></i></span>
                                            <?php echo $this->Form->input('password',array('style'=>'width:100% !important','class'=>'form-control form-control-md','placeholder'=>'Contraseña','label'=>false))?>

                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <?php echo $this->Form->button('Acceder',array('type'=>'submit','class'=>'btn btn-primary btn-block btn-flat'))?>
                                    </div>
                                </div>
                            </div>
                            <?= $this->Form->end()?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- global js -->

<?php
        echo $this->Html->script(
                array(
                    'jquery.min',
                    'tether.min',
                    '/vendors/bootstrapvalidator/js/bootstrapValidator.min',
                    'vendors/wow/js/wow.min',
                    'pages/login1',
                ),
                array('inline'=>false));
?>


<!-- end of global js-->
</body>

</html>


