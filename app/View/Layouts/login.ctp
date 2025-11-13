
<!DOCTYPE html>
<html>
    <head>

    <meta charset="UTF-8">
    <title>Iniciar Sesión | Miboo DS</title>
    <!--IE Compatibility modes-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" href="<?= $this->Html->url('/img/logo1.ico')?>" type="image/x-icon">
		<link rel="apple-touch-icon" sizes="180x180" href="<?= $this->Html->url('/img/apple-touch-icon.png')?>">
		<link rel="icon" type="image/png" sizes="32x32" href="<?= $this->Html->url('/img/favicon-32x32.png')?>">
		<link rel="icon" type="image/png" sizes="16x16" href="<?= $this->Html->url('/img/favicon-16x16.png')?>">
    <!--global styles-->
    <?php echo $this->Html->css(array('components','custom')); ?>
    <!-- end of global styles-->
    <?php echo $this->fetch('css') ?>
</head>
</header>
<?php echo $this->Session->flash(); ?>
<?php echo $this->fetch('content'); ?>
</html>
