<?php
	use \quizzenger\utilities\PermissionUtility as PermissionUtility;

	PermissionUtility::checkLogin();

	include("myquestions.php");
	include("myquizzes.php");
	loadGameView($this->mysqli, $viewInner);

	$viewInner->setTemplate ( 'mycontent' );
	$viewInner->assign('template', $this->template);

	function loadGameView($mysqli, $viewInner){
		$gameView = new \View();
		$gameView->setTemplate ( 'gamelist' );

		//$this->sqlhelper = new SqlHelper(log::get());
		$gameModel = new \quizzenger\gamification\model\GameModel($mysqli);
		$games = $gameModel->getGamesByUser($_SESSION['user_id']);
		$gameView->assign( 'games', $games);

		$viewInner->assign ( 'gamelist', $gameView->loadTemplate() );
	}
?>