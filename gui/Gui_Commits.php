<?php

//##################################################################################### Gui_Commits
class Gui_Commits
{

	//--------------------------------------------------------------------------------------- display
	public static function display()
	{
		$log = new Git_Log();
		$entries = $log->toArray(); 
		//echo "<pre>" . print_r($entries, true) . "</pre>";
	}

}
