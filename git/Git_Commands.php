<?php
include_once "git/Git_Config.php";
include_once "git/Git_Ignore.php";

//#################################################################################### Git_Commands
class Git_Commands
{

	const GIT_ADD               = "git add \"@file_name\"";
	const GIT_CHECKOUT          = "git checkout";
	const GIT_CLONE             = "git clone \"@url\" ./";
	const GIT_COMMIT            = "git commit --author=\"@author\" -m \"@message\"";
	const GIT_COMMIT_AMEND      = "git commit --author=\"@author\" -m \"@message\" --amend";
	const GIT_CONFIG_USER_NAME  = "git config --global user.name \"@name\"";
	const GIT_CONFIG_USER_EMAIL = "git config --global user.email \"@email\"";
	const GIT_FETCH             = "git fetch";
	const GIT_INIT              = "git init";
	const GIT_LOG               = "git log --raw --date=iso";
	const GIT_PUSH              = "git push -u origin master";
	const GIT_MERGE             = "git merge origin/master --no-commit";
	const GIT_REMOVE            = "git rm \"@file_name\"";
	const GIT_STATUS            = "git status -s"; 

	//------------------------------------------------------------------------------------------- add
	/**
	 * @param string $file_name
	 * @return array
	 */
	public static function add($file_name)
	{
		$command = str_replace("@file_name", $file_name, Git_Commands::GIT_ADD);
		$raw_output = array("> " . $command);
		exec($command, $raw_output);
		return $raw_output;
	}

	//-------------------------------------------------------------------------------------- cloneCmd
	/**
	 * @param string $url
	 * @return array
	 */
	public static function cloneCmd($url)
	{
		$command = str_replace("@url", $url, Git_Commands::GIT_CLONE);
		$raw_output = array("> " . $command);
		exec($command, $raw_output);
		Git_Ignore::createNew();
		return $raw_output;
	}

	//---------------------------------------------------------------------------------------- commit
	/**
	 * @param array $files
	 * @param string $message
	 * @return array
	 */
	public static function commit($files, $message, $amend)
	{
		$raw_output = Git_Config::apply();
		if ($files) foreach ($files as $file_name => $change_type) if ($change_type) {
			switch ($change_type) {
				case File_Change::ADD:
				case File_Change::MODIFY:
				case File_Change::UNMERGED:
				case File_Change::NOT:
					$raw_output = array_merge($raw_output, Git_Commands::add($file_name));
					break;
				case File_Change::DELETE:
					$raw_output = array_merge($raw_output, Git_Commands::remove($file_name));
					break;
				default:
					$raw_output = array_merge(
						$raw_output, array("Unknown change type [$change_type] for file $file_name")
					);
					break;
			}
		}
		$author  = str_replace('"', "\\\"", Git_Config::getAuthor()->toString());
		$message = str_replace('"', "\\\"", $message);
		$command = str_replace(
			array("@author", "@message"),
			array($author, $message),
			$amend ? Git_Commands::GIT_COMMIT_AMEND : Git_Commands::GIT_COMMIT 
		);
		$raw_output[] = "> " . $command;
		exec($command, $raw_output);
		return $raw_output;
	}

	//----------------------------------------------------------------------------------------- fetch
	/**
	 * @return array
	 */
	public static function fetch()
	{
		$raw_output = array("> " . Git_Commands::GIT_FETCH);
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
			$raw_output = array("> " . Git_Commands::GIT_INIT);
			exec(Git_Commands::GIT_INIT, $raw_output);
			Git_Ignore::createNew();
			file_put_contents("README", "");
			return $raw_output;
		} else {
			return array("There is already a .git directory here");
		}
	}

	//----------------------------------------------------------------------------------------- merge
	/**
	 * @return array
	 */
	public static function merge()
	{
		$raw_output = array("> " . Git_Commands::GIT_MERGE);
		exec(Git_Commands::GIT_MERGE, $raw_output);
		return $raw_output;
	}

	//------------------------------------------------------------------------------------------ push
	/**
	 * @return array
	 */
	public static function push()
	{
		$raw_output = array("> " . Git_Commands::GIT_PUSH);
		exec(Git_Commands::GIT_PUSH, $raw_output);
		return $raw_output;
	}

	//---------------------------------------------------------------------------------------- remove
	/**
	 * @param string $file_name
	 * @return array
	 */
	public static function remove($file_name)
	{
		$command = str_replace("@file_name", $file_name, Git_Commands::GIT_REMOVE);
		$raw_output = array("> " . $command);
		exec($command, $raw_output);
		return $raw_output;
	}

}
