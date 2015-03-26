<?php ?>
	<script id="dot-openGameRow" type="text/x-dot-template">
		<tr>
			<td>
				<a href="?view=gamestart&gameid={{=it.id}}">
					{{=it.name}}
				</a>
			</td>
			<td class="hidden-xs">
			{{?it.members==null}}0 {{?}}
			{{?it.members!=null}}{{=it.members}} {{?}}Teilnehmer</td>
			<td class="hidden-xs">{{=it.username}}</td>
			<td class="hidden-xs">
				<a href="?view=gamestart&gameid={{=it.id}}">
					<span class="glyphicon glyphicon-ok-sign"></span>
				</a>
			</td>
		</tr>
	</script>
	<div class="panel-group">
		<div class="panel panel-default no-margin">
			<a data-toggle="collapse" data-target="#openGames" href="#openGames">
				<div class="panel-heading bg-info text-info">
					<h4 class="panel-title">Offene Games</h4>
				</div>
			</a>
	    	<div id="openGames" class="panel-collapse collapse in">
				<div class="panel-body">
					<table class="table" id="tableOpenGames">
						<thead>
							<tr>
								<th>
									Name
								</th>
								<th class="hidden-xs">
									Teilnehmer
								</th>
								<th class="hidden-xs">
									Ersteller
								</th>
								<th class="hidden-xs">Beitreten</th>
							</tr>
						</thead>
						<tbody id="tableBodyOpenGames">
						<?php foreach ( $this->_ ['openGames'] as $game ) { ?>
							<tr>
								<td>
									<a href="<?php echo '?view=gamestart&gameid=' . $game['id']; ?>">
										<?php echo htmlspecialchars($game['name']); ?>
									</a>
								</td>
								<td class="hidden-xs"><?php echo (isset($game['members'])?$game['members']:'0').' Teilnehmer'; ?> </td>
								<td class="hidden-xs"><?php echo htmlspecialchars($game['username']); ?></td>
								<td class="hidden-xs">
									<a href="<?php echo '?view=gamestart&gameid=' . $game['id']; ?>" >
										<span class="glyphicon glyphicon-ok-sign"></span>
									</a>
								</td>
							</tr>
						<?php }  ?>
						</tbody>
					</table>
				</div> <!-- panel-body -->
	    	</div> <!-- panel-collapse -->
	    </div> <!-- panel -->
	    <a class="panel btn btn-primary" data-toggle="modal" data-target="#newGame" href="javascript:void()">
	    Game erstellen
	    </a>

	    
	    <div class="modal fade" id="newGame" tabindex="-1" role="dialog"
			aria-labelledby="newGameModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<form method="post">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">
								<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
							</button>
							<h4 class="modal-title" id="newGameModalLabel">Neues Game erstellen aus Quiz</h4>
						</div>
						<div class="modal-body">
							<?php if( isset($this->_ ['quizzes']) && count($this->_ ['quizzes']) > 0 ){ ?>
							<input type="text" autofocus="" required="required"
								placeholder="Game Name" name="gamename" id="gameNameModal"
								class="form-control" />
							<hr>
								<h4>Bitte Quiz auswählen</h4>
								<table class="table" id="tableNewGame">
									<thead>
										<tr>
											<th>Quizname</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ( $this->_ ['quizzes'] as $quiz ) { ?>
										
											<tr class="clickable">
												<td>
													<input class="css-checkbox css-checkbox-relative" type="radio" required="required" name="quizid" value="<?= $quiz['id']; ?>">													
														<?= htmlspecialchars($quiz['name']); ?>
													</input>
												</td>
											</tr>
											<?php
											}
										?>
									</tbody>
								</table>

							<?php } else{ ?>
							<div class="form-group">
								Games werden aus Quizzes erstellt. Bitte zuerst ein <a class="text-primary" href="?view=mycontent#myquizzes">Quiz erstellen</a>
							<?php } ?>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Abbrechen</button>
							<!-- <input id="saveNewGame" type="button" class="btn btn-primary" value="Speichern"></input> -->
							<button id="saveNewGame" type="submit" class="btn btn-primary" formaction="./index.php?view=gamenew">Speichern</button>
						</div>
					</form>
				</div>
			</div>
		</div>
<?php /* 
	    <div class="panel panel-default no-margin">
			<a data-toggle="collapse" data-target="#newGames" href="#newGames">
				<div class="panel-heading bg-info text-info">
					<h4 class="panel-title">Neues Game erstellen</h4>
				</div>
			</a>
			<div id="newGames" class="panel-collapse collapse in">
				<div class="panel-body">
					<table class="table" id="tableNewGame">
						<thead>
							<tr>
								<th>Quizname</th>
							</tr>
						</thead>
						<tbody>
							<?php
							if (! is_null ( $this->_ ['quizzes'] )) {
								foreach ( $this->_ ['quizzes'] as $quiz ) { ?>
								<tr>
									<td>

										<a data-toggle="modal" data-target="#newGameDialog" data-quiz-id="<?= $quiz['id'] ?>" data-quiz-name="<?= htmlspecialchars($quiz['name']); ?>" href="javascript:void()">
											<?= htmlspecialchars($quiz['name']); ?>
										</a>
									</td>
								</tr>
								<?php
								}
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
	    </div>
		<div class="modal fade" id="newGameDialog" tabindex="-1" role="dialog"
			aria-labelledby="newGameModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<form method="post">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">
								<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
							</button>
							<h4 class="modal-title" id="newGameModalLabel">Neues Game erstellen aus Quiz</h4>
						</div>
						<div class="modal-body">
							<div hidden="true">
								<input type="text" id="quizIdModal" name="quizid" class="form-control" />
							</div>
							<input type="text" autofocus="" required="required"
								placeholder="Game Name" name="gamename" id="gameNameModal"
								class="form-control" />
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Abbrechen</button>
							<!-- <input id="saveNewGame" type="button" class="btn btn-primary" value="Speichern"></input> -->
							<button id="saveNewGame" type="submit" class="btn btn-primary" formaction="./index.php?view=gamenew">Speichern</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		 */?>
	</div> <!-- panel-group -->
<?php ?>