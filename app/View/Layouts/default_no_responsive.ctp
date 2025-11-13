<!doctype html>
<html class="no-js">
<head>
	<meta charset="UTF-8">
	<title>MiBoo</title>
	<!--IE Compatibility modes-->
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<!--Mobile first-->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="shortcut icon" href="<?= $this->Html->url('/img/logo1.ico')?>" type="image/x-icon">
	<link rel="apple-touch-icon" sizes="180x180" href="<?= $this->Html->url('/img/apple-touch-icon.png')?>">
	<link rel="icon" type="image/png" sizes="32x32" href="<?= $this->Html->url('/img/favicon-32x32.png')?>">
	<link rel="icon" type="image/png" sizes="16x16" href="<?= $this->Html->url('/img/favicon-16x16.png')?>">
	<link rel="manifest" href="<?= $this->Html->url('site.webmanifest')?>/">
	<!-- Global styles -->
	<?= $this->Html->css(
			array(
				'components','custom'
			)
		)
	?>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
	<?= $this->fetch('css')?>
	<!--End of global styles-->
</head>
<body class="no_header">
<div id="wrap">
	<div class="wrapper">
		<?= $this->Element('menu')?>
		<div id="content" class="bg-container">
			<header class="head">
				<div class="main-bar">
					<div class="row no-gutters">
						<div class="col-lg-12 col-md-12 col-sm-12">
							<h4 class="nav_top_align">
								<?= $titulo_seccion?>
							</h4>
						</div>
					</div>
				</div>
			</header>
			<?= $this->Flash->render(); ?>
			<?= $this->fetch('content')?>
		</div>
		<!-- /#content -->
	</div>
	<!--wrapper-->
</div>

<!--Global scripts-->
<?= $this->Html->script(
		array(
			'components','custom'
		)
	)
?>
<!--End of global scripts-->
<?= $this->fetch('script')?>
</body>

</html>
