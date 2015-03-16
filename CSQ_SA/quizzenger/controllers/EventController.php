<?php

namespace quizzenger\controllers {
	use \stdClass as stdClass;
	use \SplEnum as SplEnum;
	use \mysqli as mysqli;
	use \quizzenger\data\UserEvent as UserEvent;
	use \quizzenger\scoring\ScoreDispatcher as ScoreDispatcher;
	use \quizzenger\achievements\AchievementDispatcher as AchievementDispatcher;

	class EventController {
		private $mysqli;
		private $scoreDispatcher;
		private $achievementDispatcher;

		public function __construct(mysqli $mysqli) {
			$this->mysqli = $mysqli;
			$this->scoreDispatcher = new ScoreDispatcher($this->mysqli);
			$this->achievementDispatcher = new AchievementDispatcher($this->mysqli);
		}

		public function fire(UserEvent $event) {
			$this->scoreDispatcher->dispatch($event);
			$this->achievementDispatcher->dispatch($event);
		}
	} // class EventController
} // namespace quizzenger\controllers

?>
