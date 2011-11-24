<?php

//##################################################################################### File_Change
class File_Change
{

	const ADD      = "A";
	const DELETE   = "D";
	const MODIFY   = "M";
	const NOT      = "?";
	const READY    = " ";
	const UNMERGED = "U";

	/**
	 * @var string
	 */
	private $change_type = null;

	/**
	 * @var string
	 */
	private $current_branch = null;

	/**
	 * @var string
	 */
	private $current_object_id = null;

	/**
	 * @var string
	 */
	private $file_name = null;

	/**
	 * @var string
	 */
	private $previous_branch = null;

	/**
	 * @var string
	 */
	private $previous_object_id = null;

	//----------------------------------------------------------------------------------- File_Change
	public function File_Change($file_name = null, $change_type = null)
	{
		$this->change_type = $change_type;
		$this->file_name = $file_name;
	}

	//------------------------------------------------------------------ createFromGitCheckoutRawLine
	/**
	 * @param string $raw_change
	 * @return File_Change
	 */
	public static function createFromGitCheckoutRawLine($raw_change)
	{
		$file_change = new File_Change();
		$file_change->previous_branch    = null;
		$file_change->current_branch     = null;
		$file_change->previous_object_id = null;
		$file_change->current_object_id  = null;
		$file_change->change_type        = substr($raw_change, 0, 1);
		$file_change->file_name          = substr($raw_change, 2);
		return $file_change;
	}

	//----------------------------------------------------------------------- createFromGitLogRawLine
	/**
	 * @param string $raw_change
	 * @return File_Change
	 */
	public static function createFromGitLogRawLine($raw_change)
	{
		$file_change = new File_Change();
		$file_change->previous_branch    = substr($raw_change, 1,  6);
		$file_change->current_branch     = substr($raw_change, 8,  6);
		$file_change->previous_object_id = substr($raw_change, 15, 7);
		$file_change->current_object_id  = substr($raw_change, 26, 7);
		$file_change->change_type        = substr($raw_change, 37, 1);
		$file_change->file_name          = substr($raw_change, 39);
		return $file_change;
	}

	//-------------------------------------------------------------------- createFromGitStatusRawLine
	/**
	 * @param string $raw_change
	 * @return File_Change
	 */
	public static function createFromGitStatusRawLine($raw_change)
	{
		$file_change = new File_Change();
		$file_change->previous_branch    = null;
		$file_change->current_branch     = null;
		$file_change->previous_object_id = null;
		$file_change->current_object_id  = null;
		$file_change->change_type        = substr($raw_change, 1, 1);
		$file_change->file_name          = substr($raw_change, 3);
		return $file_change;
	}

	//--------------------------------------------------------------------------------- getChangeType
	/**
	 * @return string
	 */
	public function getChangeType()
	{
		return $this->change_type;
	}

	//--------------------------------------------------------------------------- getChangeTypeAsText
	/**
	 * @return string
	 */
	public function getChangeTypeAsText()
	{
		switch ($this->change_type) {
			case File_Change::ADD:      return "added";
			case File_Change::DELETE:   return "deleted";
			case File_Change::MODIFY:   return "modified";
			case File_Change::NOT:      return "not in repository";
			case File_Change::READY:    return "ready to commit";
			case File_Change::UNMERGED: return "unmerged files";
		}
		return "unknown file change type";
	}

	//------------------------------------------------------------------------------ getCurrentBranch
	/**
	 * @return string
	 */
	public function getCurrentBranch()
	{
		return $this->current_branch;
	}

	//---------------------------------------------------------------------------- getCurrentObjectId
	/**
	 * @return string
	 */
	public function getCurrentObjectId()
	{
		return $this->current_object_id;
	}

	//----------------------------------------------------------------------------------- getFileName
	/**
	 * @return string
	 */
	public function getFileName()
	{
		return $this->file_name;
	}

	//----------------------------------------------------------------------------- getPreviousBranch
	/**
	 * @return string
	 */
	public function getPreviousBranch()
	{
		return $this->previous_branch;
	}

	//--------------------------------------------------------------------------- getPreviousObjectId
	/**
	 * @return string
	 */
	public function getPreviousObjectId()
	{
		return $this->previous_object_id;
	}

	//----------------------------------------------------------------------------------- isDirectory
	/**
	 * @return bool
	 */
	public function isDirectory()
	{
		return (substr($this->file_name, -1) == "/");
	}

	//-------------------------------------------------------------------------------------- toString
	/**
	 * @return string
	 */
	public function toString()
	{
		if ($this->current_object_id) {
			return ":"
			. $this->previous_branch . " " . $this->current_branch . " "
			. $this->previous_file_crc . "... " . $this->current_file_crc . "... "
			. $this->change_type . "  " . $this->file_name;
		} else {
			return $this->change_type . "\t" . $this->file_name;
		}
	}

}
