<?php

namespace quizzenger\achievements {
	use \mysqli as mysqli;
	use \quizzenger\data\ArgumentCollection as ArgumentCollection;

	/**
	 * Defines the plugin interface for individual achievements.
	**/
	interface IAchievement {
		/**
		 * This function determines whether the conditions have been met to
		 * grant the specific achievement that implements this interface.
		 * @param mysqli $database Connection to the database.
		 * @param ArgumentCollection Additional relevant settings.
		 * @param string $event Event that caused the function to be called.
		 * @param object $arguments List of arguments defined for the achievement.
		 * @return boolean Returns true if the specific achievement is to be granted, false otherwise.
		**/
		public function grant(mysqli $database, ArgumentCollection $collection, $id, $event, $type, $arguments);
	} // interface IAchievement
} // namespace quizzenger\achievements

?>