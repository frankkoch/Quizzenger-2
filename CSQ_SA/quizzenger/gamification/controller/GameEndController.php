<?php

namespace quizzenger\gamification\controller {
	use \stdClass as stdClass;
	use \SplEnum as SplEnum;
	use \SqlHelper as SqlHelper;
	use \quizzenger\logging\Log as Log;
	use \quizzenger\gamification\model\GameModel as GameModel;


	class GameEndController{
		private $view;
		private $sqlhelper;
		private $request;

		private $quizModel;
		private $gameModel;

		private $gameid;
		private $gamequestions;
		private $gamecounter;
		private $gameinfo;

		public function __construct($view) {
			$this->view = $view;
			$this->sqlhelper = new SqlHelper(log::get());
			$this->quizModel = new \QuizModel($this->sqlhelper, log::get()); // Backslash means: from global Namespace
			$this->gameModel = new GameModel($this->sqlhelper);
			$this->request = array_merge ( $_GET, $_POST );

			$this->checkGameSessionParams();
			$this->gameid = $this->request ['gameid'];
			$this->gamequestions = $_SESSION ['game'][$this->gameid]['gamequestions'];
			$this->gamecounter = $_SESSION ['game'][$this->gameid]['gamecounter'];
			$this->gameinfo = $this->getGameInfo();

		}
		public function loadView(){
			$this->checkPreconditions();

			$this->loadGameEndView();

			$this->loadReportView();

			return $this->view;
		}

		private function loadGameEndView(){
			$this->view->setTemplate ( 'gameend' );

			$score = $this->quizModel->getSingleChoiceScoreByGameId ( $this->gameinfo['game_id'], $this->gameinfo['quiz_id'] );
			$maxScore = $this->quizModel->getMaxSingleChoiceScore ( $this->gameinfo['quiz_id'] );

			$this->view->assign ( 'score', $score );
			$this->view->assign ( 'maxScore', $maxScore );

			$this->view->assign ( 'gameinfo', $this->gameinfo );

		}

		private function loadReportView(){
			$reportView = new \View();
			$reportView->setTemplate ( 'gamereport' );
			$reportView->assign('gameinfo', $this->gameinfo);
			$gameReport = $this->gameModel->getGameReport($this->gameid);
			$reportView->assign('gamereport', $gameReport);

			$now = date("Y-m-d H:i:s");
			$durationSec = timeToSeconds($this->gameinfo['duration']);
			$timeToEnd = strtotime($this->gameinfo['gameend']) - strtotime($now);
			$progressCountdown = (int) (100 / $durationSec * $timeToEnd);
			$reportView->assign( 'timeToEnd', $timeToEnd);
			$reportView->assign( 'progressCountdown', $progressCountdown);

			$this->view->assign ( 'reportView', $reportView->loadTemplate());
		}

		/*
		 * Redirects if at leaste one condition fails
		 * @Precondition User is logged in
		 * @Precondition Setted SESSION and request params
		 * @Precondition User is game member
		 * @Precondition Game has started
		 */
		private function checkPreconditions(){
			checkLogin();

			$isMember = $this->gameModel->isGameMember($_SESSION['user_id'], $this->gameid);

			//checkConditions
			if($isMember==false || $this->hasStarted($this->gameinfo['starttime'])==false){
				redirectToErrorPage('err_not_authorized');
			}
		}
		private function checkGameSessionParams(){
			if(! isset($this->request ['gameid'],
					$_SESSION ['game'][$this->request ['gameid']]['gamequestions'],
					$_SESSION ['game'][$this->request ['gameid']]['gamecounter'])
			){
				redirectToErrorPage('err_not_authorized');
			}
		}



		/*
		 * Gets the Gameinfo. Redirects to errorpage when no result returned.
		 */
		private function getGameInfo(){
			$gameinfo = $this->gameModel->getGameInfoByGameId($this->gameid);
			if(count($gameinfo) <= 0) redirectToErrorPage('err_db_query_failed');
			else return $gameinfo[0];
		}
		private function isGameOwner($owner_id){
			return $owner_id == $_SESSION['user_id'];
		}
		private function hasStarted($has_started){
			return isset($has_started);
		}

	} // class GameController
} // namespace quizzenger\gamification\controller

?>