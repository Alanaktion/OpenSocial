</head>
<body>
<div id="fb-root"></div>
<script>(function(d,s,id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=372837914270";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
	<header>
		<h1>Xusix</h1>
<?php if($u_id): ?>
		<nav id="nav-user">
			<div>
				<p class="nomargin"><a href="<?=$a_home.$u_name?>"><?=$u_fname.' '.$u_lname?></a></p>
				<a href="<?=$a_home.$u_name?>">My Page</a> | <a href="<?=$a_home?>settings">Settings</a> | <a href="<?=$a_home?>logout">Log Out</a>
			</div>
			<a href="<?=$a_home.$u_name?>" class="img">
				<img src="<?=x::userpic($u_id,64)?>" alt="<?=$u_name?>">
			</a>
		</nav>
<?php endif; ?>
		<br class="clr">
		<nav id="nav-top">
<?php if($u_id) { // Logged In ?>
			<a href="<?=$a_home?>stream"<?php if(!$pg[0] || $pg[0]=='stream') echo ' class="current"'; ?>><?=s('Stream')?></a>
			<a href="<?=$a_home?>contacts"<?php if(($pg_f && $pg[0]!=$u_name && $pg[1]!='photos') || $pg[0]=='contacts') echo ' class="current"'; ?>><?=s('Friends')?></a>
			<a href="<?=$a_home.$u_name?>/photos"<?php if($pg[1]=='photos') echo ' class="current"'; ?>><?=s('Photos')?></a>
			<a href="<?=$a_home?>music"<?php if($pg[0]=='music') echo ' class="current"'; ?>><?=s('Music')?></a>
			<a href="<?=$a_home?>games" class="<?php if($pg[0]=='games') echo ' current'; ?>"><?=s('Games')?></a>
			<a href="<?=$a_home?>apps"<?php if($pg[0]=='app' || $pg[0]=='apps') echo ' class="current"'; ?>><?=s('Applications')?></a>
			<span>
				<a href="<?=$a_home?>messages" class="messages<?php if($pg[0]=='messages') echo ' current'; ?>"><?=s('Messages')?><?php $umsg=x::msg_count_unread();if($umsg)echo' <strong>'.$umsg.'</strong>'; ?></a>
			</span>
<?php } else { // Public ?>
			<a href="<?=$a_home?>stream"<?php if(!$pg[0] || $pg[0]=='stream') echo ' class="current"'; ?>><?=s('Stream')?></a>
			<a href="<?=$a_home?>contacts"<?php if($pg_f || $pg[0]=='contacts') echo ' class="current"'; ?>><?=s('Users')?></a>
			<a href="<?=$a_home?>music"<?php if($pg[0]=='music') echo ' class="current"'; ?>><?=s('Music')?></a>
			<a href="<?=$a_home?>games" class="nav-games<?php if($pg[0]=='games') echo ' current'; ?>"><?=s('Games')?></a>
			<a href="<?=$a_home?>apps"<?php if($pg[0]=='app' || $pg[0]=='apps') echo ' class="current"'; ?>><?=s('Applications')?></a>
			<span>
				<a href="/login">Log In</a>
				<a href="/join">Sign Up</a>
			</span>
<?php } ?>
			<br class="clr">
		</nav>
	</header>