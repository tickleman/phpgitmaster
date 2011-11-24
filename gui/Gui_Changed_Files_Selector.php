<?php
include_once "git/Files_Changes.php";

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
				$checkbox = "<input type=checkbox name='files[$file_name]' value='$change_type'>\n";
			}
			$html .= "<span style='float:left;'>$checkbox</span>"
			. "<span style='width:20px;float:left;' title='$type_text'>$change_type</span>"
			. "$file_name<br>\n";
			$file_change = $files_changes->getNextChange();
		}
		return $html;
	}

}
