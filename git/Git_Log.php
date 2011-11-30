<?php
include_once "git/Git_Commands.php";
include_once "git/Git_Log_Entry.php";

//######################################################################################### Git_Log
class Git_Log
{

	/**
	 * @var array(Git_Log_Entry) $log_entries
	 */
	private $log_entries;

	//--------------------------------------------------------------------------------------- Git_Log
	public function Git_Log()
	{
		$this->log_entries = array();
		$raw_log = "";
		exec(Git_Commands::GIT_LOG, $raw_log);
		foreach (explode("\n\ncommit ", join("\n", $raw_log)) as $raw_entry) {
			if ($raw_entry) {
				$this->log_entries[] = new Git_Log_Entry($raw_entry);
			}
		}
	}

	//------------------------------------------------------------------------------- getEntriesCount
	/**
	 * @return int
	 */
	public function getEntriesCount()
	{
		return count($this->log_entries);
	}

	//--------------------------------------------------------------------------------- getFirstEntry
	/**
	 * @return Git_Log_Entry null if there is no entry
	 */
	public function getFirstEntry()
	{
		$first_entry = reset($this->log_entries);
		return $first_entry ? $first_entry : null;
	}

	//---------------------------------------------------------------------------------- getNextEntry
	/**
	 * @return Git_Log_Entry null if last entry was reached
	 */
	public function getNextEntry()
	{
		$next_entry = next($this->log_entries);
		return $next_entry ? $next_entry : null;
	}

	//--------------------------------------------------------------------------------------- toArray
	/**
	 * @return array
	 */
	public function toArray()
	{
		$array = array();
		foreach ($this->log_entries as $log_entry) {
			$array[] = $log_entry->toArray();
		}
		return $array;
	}

	//-------------------------------------------------------------------------------------- toString
	/**
	 * @return string
	 */
	public function toString()
	{
		$string = "";
		foreach ($this->log_entries as $git_log_entry) {
			if ($string) $string .= "\n";
			$string .= $git_log_entry->toString();
		}
		return $string;
	}

}
