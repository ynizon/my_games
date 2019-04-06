<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
  <head>
    <title>My Games Everywhere</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Ce site permet de jouer a des jeux de cartes via un téléphone mobile (times up, brainstorm, taboo...)">
	<meta name="robots" content="noindex">
	<link href="<?php echo URL;?>/public/css/bootstrap.min.css" rel="stylesheet">
	<link href="<?php echo URL;?>/public/css/style.css" rel="stylesheet">
	
	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->	
	<script type="text/javascript" src="<?php echo URL;?>/public/js/jquery-2.1.4.min.js"></script>
	
	<script type="text/javascript" src="<?php echo URL;?>/public/js/bootstrap.min.js"></script>
	
	<?php
	/*
	<script type="text/javascript" src="<?php echo URL;?>/public/js/jquery-ui.min.js"></script>
	*/
	?>
	
	
	<?php
	if (ANALYTICS_UA != ""){
	?>
		<script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		  ga('create', '<?php echo ANALYTICS_UA;?>', 'auto');
		  ga('send', 'pageview');

		</script>
	<?php
	}
	?>
	
  </head>
  <body>

	<nav id="navbar-head" class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?php echo URL;?>">My games</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
			<?php
			if (isset($_SESSION["id"])){
			?>
				<li class="active"><a href="<?php echo URL;?>/users/home"><?php echo _('Home');?></a></li>
				<li><a href="<?php echo URL;?>/users/<?php echo $_SESSION["id"];?>/edit"><?php echo _('Settings');?></a></li>
				<li><a href="<?php echo URL;?>/users/logout"><?php echo _('Logout');?></a></li>
			<?php
			}
			?>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>
	
	<div class="clear40"></div>
		
    <div class="container">
		<div class="content">
			<?php 
			if ($error != ""){?>
				<div class="clear10" ></div>
				<div class="error">
					<?php echo $error ?>
				</div>
			<?php
			}
			?>
			
			<?php 
			if ($success != ""){?>
				<div class="clear10" ></div>
				<div class="success">
					<?php echo $success ?>
				</div>
			<?php
			}
			?>
			<?php echo $content ?>
		</div>
	</div>
		
	<br style="clear:both" />
	<br style="clear:both" />
	<br style="clear:both" />
	<br style="clear:both" />
	
	<nav class="navbar navbar-inverse navbar-fixed-bottom">
		 <div class="container">		
			  <ul class="nav navbar-nav" id="ul_footer">
				<li><a href="<?php echo URL;?>/contact">Contact</a></li>
			  </ul>
		</div>
	</nav>
  </body>
</html>
