<?php
include_once "git/File_Change.php";
include_once "git/Files_Changes.php";
include_once "git/Git_Commands.php";

//###################################################################################### Git_Status
class Git_Status
{

	/**
	 * @var Files_Changes
	 */
	private $files_changes;

	//------------------------------------------------------------------------------------ Git_Status
	public function Git_Status()
	{
		$this->files_changes = new Files_Changes();
		$raw_result = "";
		exec(Git_Commands::GIT_STATUS, $raw_result);
		$git_ignore = new Git_Ignore();
		foreach ($raw_result as $raw_line) {
			if ((substr($raw_line, 0, 1) !== "#") && (substr($raw_line, 2, 1) === " ")) {
				$file_change = File_Change::createFromGitStatusRawLine($raw_line);
				if (!$git_ignore->isFileIgnored($file_change->getFileName())) {
					$this->files_changes->add($file_change);
				}
			}
		}
	}

	//------------------------------------------------------------------------------- getFilesChanges
	/**
	 * @return Files_Changes
	 */
	public function getFilesChanges()
	{
		return $this->files_changes;
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
