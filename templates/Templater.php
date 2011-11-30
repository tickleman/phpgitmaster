<?php

//####################################################################################### Templater
class Templater
{

	/**
	 * @var string
	 */
	private $buffer;

	/**
	 * @var array
	 */
	private $data = array();

	/**
	 * @var string
	 */
	private $template_file_name = null;

	//--------------------------------------------------------------------------------------- execute
	public function execute()
	{
		if (is_file($this->template_file_name)) {
			$this->buffer = get_file_contents($this->template_file_name);
		} else {
			$this->buffer = "[!TEMPLATE_FILE_NOT_FOUND:$this->template_file_name]";
		}
	}

	//------------------------------------------------------------------------------------- getOutput
	public function getOutput()
	{
		$this->execute();
		return $this->buffer;
	}

	//--------------------------------------------------------------------------------------- setData
	function setData($data)
	{
		$this->data = $data;
	}
	
	//------------------------------------------------------------------------------- setTemplateFile
	function setTemplateFile($file_name)
	{
		$this->template_file_name = $file_name;
	}
	
	
}
