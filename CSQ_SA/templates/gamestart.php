<script language="JavaScript"><!--
javascript:window.history.forward(1);
//--></script>
<div class="jumbotron">
	<?php /*if(is_null($this->_ ['gameinfo']['gameid'])){
		header('Location: index.php');
		die();
	} */ ?>
	<h1>Willkommen zum Game XY</h1>

		<br>
		<p>
			Warten, bis das Game gestartet wird...
		</p> <br>
		<a data-toggle="collapse" data-target="#participants" href="#participants">
			<h4 class="panel-title"><?php echo count($this->_ ['members']); ?> Teilnehmer</h4>
		</a>
		<div id="participants" class="panel-collapse collapse">
			<ul>
				<?php  foreach ($this->_ ['members'] as $member ) {
					echo '<li>'. $member['member'] .'</li>';
				} ?>
			</ul>
		</div>
		<br>
  		<span id="gameId" hidden="true"><?php echo $this->_ ['gameinfo']['game_id']; ?></span>
  		<div <?= ($this->_ ['isMember'] ?'':'hidden="true"') ?>>
  			<input id="leaveGame" class="btn btn-primary btn-lg" role="button" value="Austreten"></input>
  		</div>
  		<div <?= ($this->_ ['isMember']?'hidden="true"':'') ?>>
			<input id="joinGame" class="btn btn-primary btn-lg" role="button" value="Teilnehmen"></input>
		</div>

</div>
<?php echo $this->_ ['admin']; ?>