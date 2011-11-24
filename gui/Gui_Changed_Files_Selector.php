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
		$file_change = $files_changes->getFirstChange();
		if ($file_change) $html = "<table>";
		while (!is_null($file_change)) {
			$html .= "<tr>";
			$change_type = $file_change->getChangeType();
			$type_text = $file_change->getChangeTypeAsText();
			$file_name   = $file_change->getFileName();
			if ($change_type === File_Change::READY) {
				$checkbox =  "<input type=checkbox readonly checked onclick='return false'>";
				$change_type = "V";
			} else {
				$checkbox = "<input type=checkbox name='files[$file_name]' value='$change_type'>";
			}
			$html .= "<td>$checkbox</td>"
			. "<td style='cursor:arrow;' title='$type_text'>$change_type</td>"
			. "<td>$file_name</td>";
			if ($change_type === File_Change::UNMERGED) {
				$html .= "<td><button onclick=\"location='?command=prefer&file_name=$file_name&object_id=$previous_object_id'\">MY last commited version</button></td>"
				. "<td><button onclick=\"location='?command=prefer&file_name=$file_name&object_id=$current_object_id'\">the last REMOTE version</button></td>";
			}
			$html .= "</tr>\n";
			$file_change = $files_changes->getNextChange();
		}
		if ($html) $html .= "</table>";
		return $html;
	}

}
