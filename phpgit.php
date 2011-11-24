<?php

//########################################################################################## Author
class Author
{

	/**
	 * @var string
	 */
	private $email;

	/**
	 * @var string
	 */
	private $name;

	//---------------------------------------------------------------------------------------- Author
	/**
	 * @param string $raw_author
	 */
	public function Author($name = null, $email = null)
	{
		$this->email = $email;
		$this->name  = $name;
	}

	//-------------------------------------------------------------------------- createFromMimeString
	public static function createFromMimeString($raw_author)
	{
		$author = new Author();
		$begin_pos = strpos($raw_author, "<");
		$end_pos   = strpos($raw_author, ">");
		if ($begin_pos && $end_pos) {
			$author->name  = trim(substr($raw_author, 0, $begin_pos));
			$author->email = substr($raw_author, $begin_pos + 1, $end_pos - $begin_pos - 1);
		} else {
			$author->name  = $raw_author;
			$author->email = null;
		}
echo "name = $author->name<br>";
echo "email = $author->email<br>";
		return $author;
	}

	//-------------------------------------------------------------------------------------- getEmail
	/**
	 * @return string
	 */
	public function getEmail()
	{
		return $this->email;
	}

	//--------------------------------------------------------------------------------------- getName
	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	//-------------------------------------------------------------------------------------- toString
	/**
	 * @return string
	 */
	public function toString()
	{
		if (is_null($this->email)) {
			return $this->name;
		} else {
			return "$this->name <$this->email>";
		}
	}

}

//##################################################################################### File_Change
class File_Change
{

	const ADD     = "A";
	const DELETE  = "D";
	const MODIFY  = "M";
	const READY   = " ";
	const NOT     = "?";

	/**
	 * @var string
	 */
	private $change_type;

	/**
	 * @var string
	 */
	private $current_branch;

	/**
	 * @var string
	 */
	private $current_file_crc;

	/**
	 * @var string
	 */
	private $file_name;

	/**
	 * @var string
	 */
	private $previous_branch;

	/**
	 * @var string
	 */
	private $previous_file_crc;

	//------------------------------------------------------------------ createFromGitCheckoutRawLine
	/**
	 * @param string $raw_change
	 * @return File_Change
	 */
	public static function createFromGitCheckoutRawLine($raw_change)
	{
		$file_change = new File_Change();
		$file_change->previous_branch   = null;
		$file_change->current_branch    = null;
		$file_change->previous_file_crc = null;
		$file_change->current_file_crc  = null;
		$file_change->change_type       = substr($raw_change, 0, 1);
		$file_change->file_name         = substr($raw_change, 2);
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
		$file_change->previous_branch   = substr($raw_change, 1,  6);
		$file_change->current_branch    = substr($raw_change, 8,  6);
		$file_change->previous_file_crc = substr($raw_change, 15, 7);
		$file_change->current_file_crc  = substr($raw_change, 26, 7);
		$file_change->change_type       = substr($raw_change, 37, 1);
		$file_change->file_name         = substr($raw_change, 39);
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
		$file_change->previous_branch   = null;
		$file_change->current_branch    = null;
		$file_change->previous_file_crc = null;
		$file_change->current_file_crc  = null;
		$file_change->change_type       = substr($raw_change, 1, 1);
		$file_change->file_name         = substr($raw_change, 3);
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
			case File_Change::ADD:    return "added";
			case File_Change::DELETE: return "deleted";
			case File_Change::MODIFY: return "modified";
			case File_Change::READY:  return "ready to commit";
			case File_Change::NOT:    return "not in repository";
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

	//----------------------------------------------------------------------------- getCurrentFileCrc
	/**
	 * @return string
	 */
	public function getCurrentFileCrc()
	{
		return $this->current_file_crc;
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

	//---------------------------------------------------------------------------- getPreviousFileCrc
	/**
	 * @return string
	 */
	public function getPreviousFileCrc()
	{
		return $this->previous_file_crc;
	}

	//-------------------------------------------------------------------------------------- toString
	/**
	 * @return string
	 */
	public function toString()
	{
		if ($this->current_file_crc) {
			return ":"
			. $this->previous_branch . " " . $this->current_branch . " "
			. $this->previous_file_crc . "... " . $this->current_file_crc . "... "
			. $this->change_type . "  " . $this->file_name;
		} else {
			return $this->change_type . "\t" . $this->file_name;
		}
	}

}

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
		$this->files_changes[] = $file_change;
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

//############################################################################################# Git
class Git
{

	//--------------------------------------------------------------------------------- isInitialized
	public static function isInitialized()
	{
		return is_dir(".git");
	}

}

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

//##################################################################################### Git_Formats
class Git_Formats
{

	/**
	 * @var string
	 */
	const date = "Y-m-d H:i:s O";

}

//#################################################################################### Git_Commands
class Git_Commands
{

	const GIT_ADD               = "git add '@file_name'";
	const GIT_CHECKOUT          = "git checkout";
	const GIT_CLONE             = "git clone";
	const GIT_COMMIT            = "git commit --amend --author='@author' -m '@message'";
	const GIT_CONFIG_USER_NAME  = "git config --global user.name '@name'";
	const GIT_CONFIG_USER_EMAIL = "git config --global user.email '@email'";
	const GIT_FETCH             = "git fetch";
	const GIT_INIT              = "git init";
	const GIT_LOG               = "git log --raw --date=iso";
	const GIT_PUSH              = "git push -u origin master";
	const GIT_STATUS            = "git status -s"; 

	//------------------------------------------------------------------------------------------- add
	/**
	 * @param string $file_name
	 * @return array
	 */
	public static function add($file_name)
	{
		$raw_output = array();
		$command = str_replace("@file_name", $file_name, Git_Commands::GIT_ADD);
		exec($command, $raw_output);
		return $raw_output;
	}

	//---------------------------------------------------------------------------------------- commit
	/**
	 * @param array $add_files
	 * @param string $message
	 * @return array
	 */
	public static function commit($add_files, $message)
	{
		$raw_output = Git_Config::apply();
		if ($add_files) foreach ($add_files as $file_name => $add) if ($add) {
			$raw_output = array_merge($raw_output, Git_Commands::add($file_name));
		}
		$message = str_replace("'", "\\'", $message);
		$command = str_replace(
			array("@author", "@message"),
			array(Git_Config::getAuthor()->toString(), $message),
			Git_Commands::GIT_COMMIT
		);
echo "<pre>" . htmlentities($command) . "</pre>";
		exec($command, $raw_output);
		return $raw_output;
	}

	//----------------------------------------------------------------------------------------- fetch
	/**
	 * @return array
	 */
	public static function fetch()
	{
		$raw_output = array();
		exec(Git_Commands::GIT_FETCH, $raw_output);
		return $raw_output;
	}

	//------------------------------------------------------------------------------------------ init
	/**
	 * @return array 
	 */
	public static function init()
	{
		if (!is_dir(".git")) {
			$raw_output = array();
			exec(Git_Commands::GIT_INIT, $raw_output);
			Git_Ignore::createNew();
			file_put_contents("README", "");
			return $raw_output;
		} else {
			return array("There is already a .git directory here");
		}
	}

}

//###################################################################################### Git_Config
class Git_Config
{

	/**
	 * @var Author
	 */
	private static $author;

	//----------------------------------------------------------------------------------------- apply
	/**
	 * @result array
	 */
	public static function apply()
	{
		$raw_output = array();
		$command = str_replace(
			"@name", Git_Config::getAuthor()->getName(), Git_Commands::GIT_CONFIG_USER_NAME
		);
echo htmlentities($command) . "<br>";
		exec($command, $raw_output);
		$command = str_replace(
			"@email", Git_Config::getAuthor()->getEmail(), Git_Commands::GIT_CONFIG_USER_EMAIL
		);
echo htmlentities($command) . "<br>";
		exec($command, $raw_output);
		return $raw_output;
	}

	//------------------------------------------------------------------------------------- getAuthor
	/**
	 * @result Author
	 */
	public static function getAuthor()
	{
		return Git_Config::$author;
	}

	//------------------------------------------------------------------------------------- setAuthor
	/**
	 * @param Author $author
	 */
	public static function setAuthor($author)
	{
		Git_Config::$author = $author;
	}

	//-------------------------------------------------------------------------------------- toString
	/**
	 * @return string
	 */
	public static function toString()
	{
		return "author: " . Git_Config::$author->toString();
	}

}

//###################################################################################### Git_Ignore
class Git_Ignore
{

	/**
	 * 
	 * @var array(string)
	 */
	private $ignore_files;

	//------------------------------------------------------------------------------------ Git_Ignore
	public function Git_Ignore()
	{
		$this->ignore_files = array();
		if (is_file(".gitignore")) {
			$this->ignore_files = explode("\n", str_replace("\r", "", file_get_contents(".gitignore")));
		}
	}

	//------------------------------------------------------------------------------------- createNew
	/**
	 * @return Git_Ignore
	 */
	public static function createNew()
	{
		file_put_contents(".gitignore", ".git\n.settings\n.buildpath\n.gitignore\n.log\n.project\n");
		return new Git_Ignore();
	}

	//--------------------------------------------------------------------------------- isFileIgnored
	/**
	 * @param string $file_name
	 * @return bool
	 */
	public function isFileIgnored($file_name)
	{
		$result = in_array(trim($file_name), $this->ignore_files);
		return $result;
	}

}

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
	/*
	 * @return Git_Log_Entry null if there is no entry
	 */
	public function getFirstEntry()
	{
		$first_entry = reset($this->log_entries);
		return $first_entry ? $first_entry : null;
	}

	//---------------------------------------------------------------------------------- getNextEntry
	/*
	 * @return Git_Log_Entry null if last entry was reached
	 */
	public function getNextEntry()
	{
		$next_entry = next($this->log_entries);
		return $next_entry ? $next_entry : null;
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
			$this->commit = array_shift($log);
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
							$this->date = DateTime::createFromFormat(Git_Formats::date, substr($line, 8));
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
			$file_change = File_Change::createFromGitStatusRawLine($raw_line);
			if (!$git_ignore->isFileIgnored($file_change->getFileName())) {
				$this->files_changes->add($file_change);
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

//###################################################################### Gui_Changed_Files_Selector
class Gui_Changed_Files_Selector
{

	//--------------------------------------------------------------------------------------- display
	/**
	 * @param Files_Changes $files_changes
	 */
	public static function display($files_changes)
	{
		$html = "";
		$file_change = $files_changes->getFirstChange();
		while (!is_null($file_change)) {
			$change_type = $file_change->getChangeType();
			$type_text = $file_change->getChangeTypeAsText();
			$file_name   = $file_change->getFileName();
			if ($change_type === File_Change::READY) {
				$checkbox =  "<input type=checkbox readonly checked onclick='return false'>\n";
				$change_type = "V";
			} else {
				$checkbox = "<input type=checkbox name='add_files[$file_name]'>\n";
			}
			$html .= "<span style='float:left;'>$checkbox</span>"
			. "<span style='width:20px;float:left;' title='$type_text'>$change_type</span>"
			. "$file_name<br>\n";
			$file_change = $files_changes->getNextChange();
		}
		return $html;
	}

}

//############################################################################################ Main
class Main
{

	//----------------------------------------------------------------------------------- callCommand
	private static function callCommand($params)
	{
		switch ($params["command"]) {
			case "commit":
				return Git_Commands::commit($params["add_files"], $params["message"]);
			case "fetch":
				return Git_Commands::fetch();
			case "init":
				return Git_Commands::init();
			case "push":
				return Git_Commands::push();
		}
	}

	//------------------------------------------------------------------------------- guiAuthorChange
	private static function guiAuthorChange()
	{
		$html = <<<EOT
Author :
<input id="author" value="$_SESSION[author]" SIZE="40" onkeydown="document.getElementById('change_button').style.display='inline'">
<button onclick="location='phpgit?author='+document.getElementById('author').value" id='change_button' style="display:none;">change</button>
EOT;
		return $html;
	}

	//--------------------------------------------------------------------------------- guiInitButton
	private static function guiInitButton()
	{
		$html = <<<EOT
<button onclick="location='?command=init'">INIT</button>
EOT;
		return $html;
	}

	//---------------------------------------------------------------------------------------- guiLog
	private static function guiLog()
	{
		$git_checkout = new Git_Checkout();
		$git_status   = new Git_Status();
		$git_log      = new Git_Log();
		$html = "<h3>git checkout</h3>"
		. "<pre>" . htmlentities($git_checkout->toString()) . "</pre>"
		. "<h3>git status</h3>"
		. "<pre>" . htmlentities($git_status->toString()) . "</pre>"
		. "<h3>git log</h3>"
		. "<pre>" . htmlentities($git_log->toString()) . "</pre>"
		. "<h3>git config</h3>"
		. "<pre>" . htmlentities(Git_Config::toString()) . "</pre>";
		return $html;
	}

	//-------------------------------------------------------------------------------- guiMainActions
	private static function guiMainActions()
	{
		$git_status = new Git_Status();
		$author_change_html = Main::guiAuthorChange();
		$files_changes_html = Gui_Changed_Files_Selector::display($git_status->getFilesChanges());
		$html = <<<EOT
$author_change_html
<p>
<button onclick="location='phpgit'">REFRESH<br>STATUS</button> &gt;
<button onclick="location='phpgit?command=fetch'">\/<br>FETCH</button> &gt;
<button onclick="location='phpgit?command=merge'">&lt;=&gt;<br>MERGE</button> &gt;
<button onclick="if (document.commit.message.value) document.commit.submit(); else { alert('You must comment your commit'); document.commit.message.focus(); } ">--&gt;<br>COMMIT</button> &gt;
<button onclick="location='phpgit?command=fetch'">/\<br>PUSH</button>
<br>
<form name="commit" action="phpgit" method="post">
<input type="hidden" name="command" value="commit">
<h3>Commit</h3>
$files_changes_html
<textarea name="message" cols="80" rows="3"></textarea>
<script> document.commit.message.focus(); </script>
</form>
EOT;
		return $html;
	}

	//-------------------------------------------------------------------------------------- mainCall
	public static function mainCall($params)
	{
		Main::start($params);
		if ($params["command"]) {
			echo "<pre>" . htmlentities(join("\n", Main::callCommand($params))) . "</pre>";
		}
		if (!Git::isInitialized()) {
			echo Main::guiInitButton();
		} else {
			echo Main::guiMainActions();
			echo Main::guiLog();
		}
	}

	//----------------------------------------------------------------------------------------- start
	private static function start($params)
	{
		ini_set("session.use_cookies", true);
		session_start();
		if ($params["author"]) {
			$_SESSION["author"] = $params["author"];
		}
		if (!$_SESSION["author"]) {
			$_SESSION["author"] = "Your Name <your@email.org>";
		}
		Git_Config::setAuthor(new Author($_SESSION["author"]));
	}

}

//#################################################################################################

Main::mainCall(array_merge($_POST, $_GET));
