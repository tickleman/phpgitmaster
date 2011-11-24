<?php

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
