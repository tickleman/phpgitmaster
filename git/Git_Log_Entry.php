<?php
include_once "git/Author.php";
include_once "git/File_Change.php";
include_once "git/Files_Changes.php";
include_once "git/Git_Formats.php";

//####################################################################################### Git_Log_Entry
class Git_Log_Entry
{

	/**
	 * @var Author
	 */
	private $author;

	/**
	 * @var Files_Changes
	 */
	private $changes;

	/**
	 * @var string
	 */
	private $message;

	/**
	 * @var string
	 */
	private $commit;

	/**
	 * @var DateTime
	 */
	private $date;

	//------------------------------------------------------------------------------------- Git_Log_Entry
	/**
	 * @param string $log_block
	 */
	public function Git_Log_Entry($raw_log = null)
	{
		$this->author  = new Author();
		$this->changes = new Files_Changes();
		$this->merge   = null;
		if (is_string($raw_log)) {
			$log = explode("\n", $raw_log);
			$this->commit = substr(array_shift($log), 7);
			foreach ($log as $line) {
				switch (substr($line, 0, 1)) {
					case "A":
						if (substr($line, 0, 8) === "Author: ") {
							$this->author = Author::createFromMimeString(substr($line, 8));
						}
						break;
					case ":":
						$this->changes->add(File_Change::createFromGitLogRawLine($line));
						break;
					case " ":
						if ($this->message) {
							$this->message .= "\n";
						}
						$this->message .= trim($line);
						break;
					case "D":
						if (substr($line, 0, 8) === "Date:   ") {
							$this->date = new DateTime(substr($line, 8));
						}
						break;
						case "M":
						if (substr($line, 0, 7) === "Merge: ") {
							$this->merge = substr($line, 7);
						}
						break;
				}
			}
		}
		if (is_null($this->date)) {
			$this->date = new DateTime();
		}
	}

	//--------------------------------------------------------------------------------------- toArray
	/**
	 * @return array
	 */
	public function toArray()
	{
		return array(
			"author"  => $this->author->toArray(),
			"changes" => $this->changes->toArray(),
			"message" => $this->message,
			"commit"  => $this->commit,
			"date"    => $this->date->format("Y-m-d H:i:s")
		);
	}

	//-------------------------------------------------------------------------------------- toString
	/**
	 * @return string
	 */
	public function toString()
	{
		return "commit " . $this->commit . "\n"
		. ($this->merge ? "Merge: " . $this->merge . "\n" : "")
		. "Author: " . $this->author->toString() . "\n"
		. "Date:   " . $this->date->format(Git_Formats::date) . "\n"
		. "\n"
		. "    " . str_replace("\n", "\n    ", $this->message) . "\n"
		. ($this->changes->getChangesCount() ? "\n" . $this->changes->toString() . "\n" : ""); 
	}

}
