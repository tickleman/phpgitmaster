<?php
include_once "git/Author.php";

//###################################################################################### Git_Config
class Git_Config
{

	/**
	 * @var Author
	 */
	private static $author;

	/**
	 * @var string
	 */
	private static $project_path;

	//----------------------------------------------------------------------------------------- apply
	/**
	 * @result array
	 */
	public static function apply()
	{
		chdir(Git_Config::$project_path);
		return array();
	}

	//------------------------------------------------------------------------------------- getAuthor
	/**
	 * @result Author
	 */
	public static function getAuthor()
	{
		return Git_Config::$author;
	}

	//-------------------------------------------------------------------------------- getProjectPath
	/**
	 * @result string
	 */
	public static function getProjectPath()
	{
		return Git_Config::$project_path;
	}

	//------------------------------------------------------------------------------------- setAuthor
	/**
	 * @param Author $author
	 */
	public static function setAuthor($author)
	{
		Git_Config::$author = $author;
	}

	//-------------------------------------------------------------------------------- setProjectPath
	/**
	 * @param string $project_path
	 */
	public static function setProjectPath($project_path)
	{
		Git_Config::$project_path = $project_path;
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
