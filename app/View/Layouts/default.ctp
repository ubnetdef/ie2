<!DOCTYPE html>
<html>
<head>
	<?= $this->Html->charset(); ?>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">


	<title><?= env('COMPETITION_NAME'); ?>: Inject Engine</title>
	<?php
		echo $this->Html->meta('icon');

		echo $this->Html->css('bootstrap.min');
		echo $this->Html->css('style');

		echo $this->Html->script('jquery.min');
		echo $this->Html->script('bootstrap.min');

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
</head>
<body>
<?php if ( $emulating ): ?>
<div class="alert alert-danger" style="margin-bottom: 0px;">
	You are currently emulating a user's account! <?= $this->Html->link('EXIT', '/user/emulate_clear', ['class' => 'btn btn-sm btn-info pull-right']); ?>
</div>
<?php endif; ?>

<nav class="navbar navbar-default<?= env('COMPETITION_LOGO') != false ? ' navbar-with-logo' : ''; ?>">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="<?php echo $this->Html->url('/'); ?>">
				<?php if ( env('COMPETITION_LOGO') != false ): ?>
				
				<img src="<?= $this->Html->url(env('COMPETITION_LOGO')); ?>"/>
				
				<?php else: ?>

				<?= env('COMPETITION_NAME'); ?>
				
				<?php endif; ?>
			</a>
		</div>
		<div class="navbar-collapse collapse">
			<ul class="nav navbar-nav">
				<li class="<?= isset($at_home) ? 'active' : ''; ?>"><a href="<?= $this->Html->url('/'); ?>">Home</a></li>

				<?php if ( !empty($userinfo) ): ?>
				<li class="<?= isset($at_injects) ? 'active' : ''; ?>"><a href="<?= $this->Html->url('/injects'); ?>">Injects</a></li>
				<?php endif; ?>

				<li class="<?= isset($at_scoreboard) ? 'active' : ''; ?>"><a href="<?= $this->Html->url('/scoreboard'); ?>">Scoreboard</a></li>
			</ul>
			
			<ul class="nav navbar-nav navbar-right">
				<?php if ( !empty($userinfo) ): ?>

				<li class="<?= isset($at_teampanel) ? 'active' : ''; ?>"><a href="<?= $this->Html->url('/team'); ?>">Team Panel</a></li>

				<li class="dropdown<?= isset($at_dashboard) ? ' active' : ''; ?>">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button">
						Dashboards <span class="caret"></span>
					</a>
					<ul class="dropdown-menu">
						<li class=""><a href="<?= $this->Html->url('/dashboard/overview'); ?>">Overview</a></li>
						<li class=""><a href="<?= $this->Html->url('/dashboard/timeline'); ?>">Inject Completion Timeline</a></li>
						<li class=""><a href="<?= $this->Html->url('/dashboard/personal'); ?>">Personalized</a></li>
					</ul>
				</li>

				<li class="dropdown<?= isset($at_backendpanel) ? ' active' : ''; ?>">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button">
						Backend Panel <span class="caret"></span>
					</a>
					<ul class="dropdown-menu">
						<li class=""><a href="<?= $this->Html->url('/backend/user'); ?>">User Manager</a></li>
						<li class=""><a href="<?= $this->Html->url('/backend/injects'); ?>">Inject Manager</a></li>
						<li class=""><a href="<?= $this->Html->url('/backend/service'); ?>">Service Manager</a></li>
						<li class=""><a href="<?= $this->Html->url('/backend/logs'); ?>">Log Manager</a></li>
					</ul>
				</li>

				<li class="dropdown">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button">
						<?= $userinfo['username']; ?> <span class="caret"></span>
					</a>
					<ul class="dropdown-menu">
						<li class=""><a href="<?= $this->Html->url('/user/profile'); ?>">My Profile</a></li>
						<li class=""><a href="<?= $this->Html->url('/user/logout'); ?>">Logout</a></li>
					</ul>
				</li>

				<?php else: ?>
				<li class="<?= isset($at_login) ? 'active' : ''; ?>"><a href="<?= $this->Html->url('/user/login'); ?>">Login</a></li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
</nav>

<div class="container">
	<?= $this->Session->flash(); ?>

	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<?= $this->fetch('content'); ?>
		</div>
	</div>
</div>

<footer class="footer">
	<div class="container">
		<p class="text-muted pull-right">
			ie<sup>2</sup> <abbr title="DEV">DEV</abbr>
		</p>
	</div>
</footer>

</body>
</html>