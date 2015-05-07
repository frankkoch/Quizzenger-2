<?php

namespace quizzenger\gamification\controller {
	use \stdClass as stdClass;
	use \SplEnum as SplEnum;
	use \mysqli as mysqli;
	use \SqlHelper as SqlHelper;
	use \quizzenger\logging\Log as Log;
	use \quizzenger\utilities\NavigationUtility as NavigationUtility;
	use \quizzenger\utilities\PermissionUtility as PermissionUtility;
	use \quizzenger\messages\MessageQueue as MessageQueue;
	use \quizzenger\utilities\FormatUtility as FormatUtility;
	use \quizzenger\gamification\model\GameModel as GameModel;

	class GameQuestionController{
		private $view;
		private $sqlhelper;
		private $request;

		private $gameModel;
		private $questionModel;
		private $quizModel;
		private $answerModel;
		private $reportModel;
		private $categoryModel;

		private $gameid;
		private $gamequestions;
		private $gamecounter;
		private $gameinfo;

		public function __construct($view) {
			$this->view = $view;
			$this->sqlhelper = new SqlHelper(log::get());
			$this->request = array_merge ( $_GET, $_POST );

			$this->gameModel = new GameModel($this->sqlhelper);
			$this->questionModel = new \QuestionModel($this->sqlhelper, log::get());
			$this->quizModel = new \QuizModel($this->sqlhelper, log::get()); // Backslash means: from global Namespace
			$this->answerModel = new \AnswerModel($this->sqlhelper, log::get());
			$this->reportModel = new \ReportModel($this->sqlhelper, log::get());
			$this->categoryModel = new \CategoryModel($this->sqlhelper, log::get());

			$this->checkGameSessionParams();
			$this->gameid = $this->request ['gameid'];
			//print_r($_SESSION['game']);

			$this->gamequestions = $_SESSION ['game'][$this->gameid]['gamequestions'];
			$this->gamecounter = $_SESSION ['game'][$this->gameid]['gamecounter'];
			$this->gameinfo = $this->getGameInfo();
		}
		public function render(){
			$this->checkPreconditions();

			$this->view->setTemplate( 'gamequestion' );
			$this->loadQuestionView();

			$this->loadReportView();

			return $this->view->loadTemplate();
		}

		private function LoadQuestionView(){
			$questionView = new \View();
			$questionView->setTemplate ( 'question' );

			$questionView->assign ( 'session_id', '' );

			$questionID= $this->gamequestions[$this->gamecounter];
			$questionView->assign ( 'questionID', $questionID );
			$question = $this->questionModel->getQuestion ( $questionID );
			$questionView->assign ( 'question', $question );
			$categoryName = $this->categoryModel->getNameByID ( $question ['category_id'] );
			$questionView->assign ( 'category', $categoryName );

			$answers = $this->answerModel->getAnswersByQuestionID ( $questionID );
			//randomize array
			mt_srand(time());
			$order = array_map(create_function('$val', 'return mt_rand();'), range(1, count($answers)));
			$_SESSION['questionorder'][$questionID] = $order;
			array_multisort($order, $answers);
			$questionView->assign ( 'answers', $answers );
			$linkToSolution = '?view=GameSolution&gameid='.$this->gameid;
			$questionView->assign ( 'linkToSolution', $linkToSolution );

			$alreadyReported= $this->reportModel->checkIfUserAlreadyDoneReport("question", $questionID , $_SESSION ['user_id']);
			$questionView->assign ('alreadyreported',$alreadyReported);

			//assign GameSession
			$questionCount= count ( $this->gamequestions );
			$questionView->assign ( 'questioncount', $questionCount );
			$currentCounter= $this->gamecounter;
			$questionView->assign ( 'currentcounter', $currentCounter );
			$progress = round ( 100 * ($currentCounter / $questionCount) );
			$questionView->assign ( 'progress', $progress );
			$weight= $this->quizModel->getWeightOfQuestionInQuiz($questionID, $this->gameinfo['quiz_id']);
			$questionView->assign ( 'weight', $weight);

			$this->view->assign ( 'questionView', $questionView->loadTemplate() );
		}

		private function loadReportView(){
			$reportView = new \View();
			$reportView->setTemplate ( 'gamereport' );
			/*
			$reportView->assign('gameinfo', $this->gameinfo);
			$gameReport = $this->gameModel->getGameReport($this->gameid);
			$reportView->assign('gamereport', $gameReport);

			$now = date("Y-m-d H:i:s");
			$durationSec = timeToSeconds($this->gameinfo['duration']);
			$timeToEnd = strtotime($this->gameinfo['calcEndtime']) - strtotime($now);
			$progressCountdown = (int) (100 / $durationSec * $timeToEnd);
			$reportView->assign( 'timeToEnd', $timeToEnd);
			$reportView->assign( 'progressCountdown', $progressCountdown);
			*/

			$this->view->assign ( 'reportView', $reportView->loadTemplate() );
		}

		/*
		 * Gets the Gameinfo.
		 */
		private function getGameInfo(){
			$gameinfo = $this->gameModel->getGameInfoByGameId($this->gameid);
			return $gameinfo;
		}

		/*
		 * Redirects if at leaste one condition fails
		 * @Precondition User is logged in
		 * @Precondition User is game member
		 * @Precondition Game has started
		 * @Precondition Game is not finished
		 */
		private function checkPreconditions(){
			PermissionUtility::checkLogin();

			$isMember = $this->gameModel->isGameMember($_SESSION['user_id'], $this->gameid);

			$now = date("Y-m-d H:i:s");
			$timeToEnd = strtotime($this->gameinfo['calcEndtime']) - strtotime($now);
			$finished = $timeToEnd <= 0 || isset($this->gameinfo['endtime']);

			if($isMember && ( $finished || $this->gamecounter >= count($this->gamequestions)) ){
				NavigationUtility::redirect('./index.php?view=GameEnd&gameid='.$this->gameid);
			}

			if($isMember==false && $this->hasStarted($this->gameinfo['starttime'])){
				MessageQueue::pushPersistent($_SESSION['user_id'], 'err_game_has_started');
				NavigationUtility::redirectToErrorPage();
			}

			if($isMember==false || $finished
					|| $this->hasStarted($this->gameinfo['starttime'])==false ){
				MessageQueue::pushPersistent($_SESSION['user_id'], 'err_not_authorized');
				NavigationUtility::redirectToErrorPage();
			}
		}

		private function checkGameSessionParams(){
			if(! isset($this->request ['gameid'])){
				MessageQueue::pushPersistent($_SESSION['user_id'], 'err_not_authorized');
				NavigationUtility::redirectToErrorPage();
			}
			/*
			if(! isset($_SESSION ['game'][$this->request ['gameid']]['gamequestions'],
					$_SESSION ['game'][$this->request ['gameid']]['gamecounter']))
			{
				if($this->gameModel->isGameMember($_SESSION['user_id'], $this->request ['gameid'])){
					//restore GameSession
					$sessionData = $this->gameModel->getSessionData($_SESSION['user_id'], $this->request ['gameid']);
					$_SESSION ['game'][$this->request ['gameid']]['gamequestions'] = $sessionData['gamequestions'];
					$_SESSION ['game'][$this->request ['gameid']]['gamecounter'] = $sessionData['gamecounter'];
				}
				else{
					MessageQueue::pushPersistent($_SESSION['user_id'], 'err_not_authorized');
					NavigationUtility::redirectToErrorPage();
				}
			} */
			//restore GameSession
			$sessionData = $this->gameModel->getSessionData($_SESSION['user_id'], $this->request ['gameid']);
			$_SESSION ['game'][$this->request ['gameid']]['gamequestions'] = $sessionData['gamequestions'];
			$_SESSION ['game'][$this->request ['gameid']]['gamecounter'] = $sessionData['gamecounter'];
		}

		private function isGameOwner($owner_id){
			return $owner_id == $_SESSION['user_id'];
		}
		private function hasStarted($has_started){
			return isset($has_started);
		}

	} // class GameQuestionController
} // namespace quizzenger\gamification\controller

?>
