<?php
include_once "git/File_Change.php";

//################################################################################### Files_Changes
class Files_Changes
{

	/**
	 * @var array(File_Change)
	 */
	private $files_changes; 

	//--------------------------------------------------------------------------------- Files_Changes
	public function Files_Changes()
	{
		$this->files_changes = array();
	}

	//------------------------------------------------------------------------------------------- add
	/**
	 * @param File_Change $file_change
	 */
	public function add($file_change)
	{
		if ($file_change->isDirectory()) {
			$this->addTree($file_change->getChangeType(), $file_change->getFileName());
		} else {
			$this->files_changes[] = $file_change;
		}
	}

	//--------------------------------------------------------------------------------------- addTree
	/**
	 * @param string $change_type
	 * @param string $path
	 */
	private function addTree($change_type, $path)
	{
		$d = dir($path);
		while ($e = $d->read()) if (substr($e, 0, 1) != ".") {
			if (is_dir("$path$e")) {
				$this->addTree("$path$e/");
			} else {
				$this->add(new File_Change("$path$e", $change_type));
			}
		}
		$d->close();
	}

	//------------------------------------------------------------------------------- getChangesCount
	/**
	 * @return int
	 */
	public function getChangesCount()
	{
		return count($this->files_changes);
	}

	//-------------------------------------------------------------------------------- getFirstChange
	/*
	 * @return File_Change null if there is no change
	 */
	public function getFirstChange()
	{
		$first_change = reset($this->files_changes);
		return $first_change ? $first_change : null;
	}

	//--------------------------------------------------------------------------------- getNextChange
	/*
	 * @return File_Change null if last change was reached
	 */
	public function getNextChange()
	{
		$next_change = next($this->files_changes);
		return $next_change ? $next_change : null;
	}

	//-------------------------------------------------------------------------------------- toString
	/**
	 * @return string
	 */
	public function toString()
	{
		$string = "";
		foreach ($this->files_changes as $file_change) {
			if ($string) $string .= "\n";
			$string .= $file_change->toString();
		}
		return $string;
	}

}