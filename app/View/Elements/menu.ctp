<div id="left">
	<div class="menu_scroll">
		<ul id="menu">
			<li>
				<?= $this->Html->link('<i class="fa fa-users"></i><span class="link-title menu_hide"> Jugadores </span>',array('controller'=>'jugadors','action'=>'index'),array('escape'=>false))?>
			</li>
			<li>
				<?= $this->Html->link('<i class="fa fa-user"></i><span class="link-title menu_hide"> Agencias </span>',array('controller'=>'comisionistas','action'=>'index'),array('escape'=>false))?>
			</li>
			<li>
				<?= $this->Html->link('<i class="fa fa-money"></i><span class="link-title menu_hide"> Bancos </span>',array('controller'=>'cuentas','action'=>'index'),array('escape'=>false))?>
			</li>
			<li>
				<?= $this->Html->link('<i class="fa fa-calendar"></i><span class="link-title menu_hide"> Cuentas Semanales </span>',array('controller'=>'jugadors','action'=>'ganancias_semanales'),array('escape'=>false))?>
			</li>
			<li>
				<?= $this->Html->link('<i class="fa fa-exchange"></i><span class="link-title menu_hide"> Solicitudes Interjugadores </span>',array('controller'=>'interjugadors','action'=>'index'),array('escape'=>false))?>
			</li>
			<li>
				<?= $this->Html->link('<i class="fa fa-file-excel-o"></i><span class="link-title menu_hide"> Reporte de Jugadores </span>',array('controller'=>'ganancias','action'=>'reporte_jugadores'),array('escape'=>false))?>
			</li>

			<li>
				<?= $this->Html->link('<i class="fa fa-file-excel-o"></i><span class="link-title menu_hide"> Reporte de Agencias </span>',array('controller'=>'ganancias','action'=>'reporte_comisionistas'),array('escape'=>false))?>
			</li>

			<li>
				<?= $this->Html->link('<i class="fa fa-sign-out"></i><span class="link-title menu_hide"> Cerrar Sesión </span>',array('controller'=>'users','action'=>'logout'),array('escape'=>false))?>
			</li>

		</ul>
		<!-- /#menu -->
	</div>
</div>
