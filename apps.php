<?php
include 'inc/applist.php';
head();
?>
<title>Applications - Xusix</title>
<?php top(); ?>
<div id="main">
<?php if(!$pg[1] || $pg[1]=='browse') { ?>
	<h1 class="frmsearch">Applications</h1>
	<p>Xusix Applications provide add-in functionality to the Xusix social network. Applications can be games, friend finders, added communication methods, and virtually anything else.</p>
	<p>When using an application, you authorize it to have access to the following:</p>
	<ul>
		<li>Xusix Username</li>
		<li>First Name</li>
		<li>Gender</li>
		<li>Ability to post on your Page</li>
	</ul>
	<p class="light">Note: Applications only have access to this information and functionality while you are using them, or if they collect this information.  Applications are only allowed to store information temporarily or for statistical purposes.</p>
<?php
	foreach($apps as $app=>$data) {
		echo '<a href="'.$a_home.'app/'.$app.'">';
		echo '<h3>'.$data[0].'</h3>';
		echo '<p>'.$data[1].'</p>';
		echo '</a>';
	}
} ?>
</div>
<?php foot(); ?>