<?php
include_once "git/File_Change.php";
include_once "git/Files_Changes.php";
include_once "git/Git_Ignore.php";

//#################################################################################### Git_Checkout
class Git_Checkout
{

	/**
	 * @var Files_Changes
	 */
	private $files_changes;

	//---------------------------------------------------------------------------------- Git_Checkout
	public function Git_Checkout()
	{
		$this->files_changes = new Files_Changes();
		$raw_result = "";
		exec(Git_Commands::GIT_CHECKOUT, $raw_result);
		$git_ignore = new Git_Ignore();
		foreach ($raw_result as $raw_line) {
			$file_change = File_Change::createFromGitCheckoutRawLine($raw_line);
			if (!$git_ignore->isFileIgnored($file_change->getFileName())) {
				$this->files_changes->add($file_change);
			}
		}
	}

	//-------------------------------------------------------------------------------------- toString
	/**
	 * @return string
	 */
	public function toString()
	{
		return $this->files_changes->toString();
	}

}
