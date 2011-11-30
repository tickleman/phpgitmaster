<?php
include_once "git/Author.php";
include_once "git/Git.php";
include_once "git/Git_Checkout.php";
include_once "git/Git_Commands.php";
include_once "git/Git_Config.php";
include_once "git/Git_Log.php";
include_once "git/Git_Merge_Solver.php";
include_once "git/Git_Status.php";
include_once "gui/Gui_Changed_Files_Selector.php";
include_once "gui/Gui_Commits.php";

//############################################################################################ Main
class Main
{

	//----------------------------------------------------------------------------------- callCommand
	/**
	 * @param array $params
	 * @return array
	 */
	private static function callCommand($params)
	{
		switch ($params["command"]) {
			case "clone":
				return Git_Commands::cloneCmd($params["clone_url"]);
			case "commit":
				return Git_Commands::commit($params["files"], $params["message"], $params["amend"]);
			case "fetch":
				return Git_Commands::fetch();
			case "init":
				return Git_Commands::init();
			case "merge":
				return Git_Commands::merge();
			case "push":
				return Git_Commands::push();
			case "prefer":
				return Git_Merge_Solver::prefer($params["object_id"]);
			case "rebase":
				return Git_Commands::rebase();
		}
	}

	//----------------------------------------------------------------------------- cleanUpCallResult
	/**
	 * @param string $log
	 * @return string
	 */
	private static function cleanUpCallResult($log)
	{
		$unwished_message = <<<EOT
Your name and email address were configured automatically based
on your username and hostname. Please check that they are accurate.
You can suppress this message by setting them explicitly:

    git config --global user.name "Your Name"
    git config --global user.email you@example.com

If the identity used for this commit is wrong, you can fix it with:

    git commit --amend --author='Your Name <you@example.com>'

EOT;
		return str_replace(str_replace("\r", "", $unwished_message), "", str_replace("\r", "", $log));
	}

	//-------------------------------------------------------------------------------- guiAuthorInput
	/**
	 * @return string
	 */
	private static function guiAuthorInput()
	{
		$html = <<<EOT
Author :
<input id="author" value="$_SESSION[author]" SIZE="40" onkeydown="document.getElementById('author_change_button').style.display='inline'">
<button onclick="location='?author='+document.getElementById('author').value" id='author_change_button' style="display:none;">change</button>
EOT;
		return $html;
	}

	//--------------------------------------------------------------------------------- guiInitButton
	/**
	 * @return string
	 */
	private static function guiInitButton()
	{
		$clone_url = $_GET["clone_url"] ? $_GET["clone_url"] : "git@github.com:username/projectname.git"; 
		$project_path_change_html = Main::guiProjectPathInput();
		$html = <<<EOT
$project_path_change_html
<h3>Welcome PhpGitMaster</h3>
What would you do ?
<p>
<button onclick="location='?command=init'">INIT</button>
<p>
OR
<p>
<button onclick="location='?command=clone&clone_url='+document.getElementById('clone_url').value">CLONE</button>
<input id="clone_url" size="60" value="$_GET[clone_url]">
<script> document.getElementById("clone_url").focus(); </script>
EOT;
		return $html;
	}

	//---------------------------------------------------------------------------------------- guiLog
	/**
	 * @return string
	 */
	private static function guiLog()
	{
		$git_checkout = new Git_Checkout();
		$git_status   = new Git_Status();
		$git_log      = new Git_Log();
		$html .= "<h3>Gui_Log</h3>\n" . Gui_Commits::display();
		$html = "<h3>git checkout</h3>\n"
		. "<pre>" . htmlentities($git_checkout->toString()) . "</pre>\n"
		. "<h3>git status</h3>"
		. "<pre>" . htmlentities($git_status->toString()) . "</pre>\n"
		. "<h3>git log</h3>"
		. "<pre>" . htmlentities($git_log->toString()) . "</pre>\n"
		. "<h3>git config</h3>"
		. "<pre>" . htmlentities(Git_Config::toString()) . "</pre>\n";
		return $html;
	}

	//-------------------------------------------------------------------------------- guiMainActions
	/**
	 * @return string
	 */
	private static function guiMainActions()
	{
		$git_status = new Git_Status();
		$author_change_html = Main::guiAuthorInput();
		$project_path_change_html = Main::guiProjectPathInput();
		$files_changes_html = Gui_Changed_Files_Selector::display($git_status->getFilesChanges());
		$html = <<<EOT
$project_path_change_html<br>
$author_change_html
<p>
<button onclick="location='./'">REFRESH<br>STATUS</button> &gt;
<button onclick="location='?command=fetch'">\/<br>FETCH</button> &gt;
<button onclick="location='?command=merge'">=&gt;-<br>MERGE</button> OR
<button onclick="location='?command=rebase'">=&gt;-<br>REBASE</button> &gt;
<button onclick="if (document.commit.message.value) document.commit.submit(); else { alert('You must comment your commit'); document.commit.message.focus(); } ">--&gt;<br>COMMIT</button> &gt;
<button onclick="location='?command=push'">/\<br>PUSH</button>
<br>
<form name="commit" action="./" method="post">
<input type="hidden" name="command" value="commit">
<h3>Commit</h3>
$files_changes_html
<p>
<input type="checkbox" name="amend" value="on">amend previous commit<br>
<textarea name="message" cols="80" rows="3"></textarea>
<script> document.commit.message.focus(); </script>
</form>
EOT;
		return $html;
	}

	//--------------------------------------------------------------------------- guiProjectPathInput
	/**
	 * @return string
	 */
	private static function guiProjectPathInput()
	{
		$html = <<<EOT
Project path :
<input id="project_path" value="$_SESSION[project_path]" SIZE="40" onkeydown="document.getElementById('project_path_change_button').style.display='inline'">
<button onclick="location='?project_path='+document.getElementById('project_path').value" id='project_path_change_button' style="display:none;">change</button>
EOT;
		return $html;
	}

	//-------------------------------------------------------------------------------------- mainCall
	/**
	 * @param array $params
	 */
	public static function mainCall($params)
	{
		Main::start($params);
		echo "<html><head><title>PhpGitMaster by Tickleman</title></head><body>\n";

		if ($params["command"]) {
			$command_result = Main::callCommand($params);
		}

		if (!Git::isInitialized()) {
			echo Main::guiInitButton();
		} else {
			echo Main::guiMainActions();
		}

		if ($params["command"]) {
			echo "<h3>$params[command] result :</h3>"
			. "<pre style='border: 1px solid black;margin:2px; padding:2px;'>"
			. htmlentities(Main::cleanUpCallResult(join("\n", $command_result)))
			. "</pre>"
			. "<p>";
		}

		if (Git::isInitialized()) {
			echo Main::guiLog();
		}

		echo "</body></html>\n";
	}

	//----------------------------------------------------------------------------------------- start
	/**
	 * @param array $params
	 */
	private static function start($params)
	{
		ini_set("session.use_cookies", true);
		session_start();
		// author
		if ($params["author"]) {
			$_SESSION["author"] = $params["author"];
		}
		if (!$_SESSION["author"]) {
			$_SESSION["author"] = "Your Name <your@email.org>";
		}
		Git_Config::setAuthor(Author::createFromMimeString($_SESSION["author"]));
		// project path
		if ($params["project_path"]) {
			$_SESSION["project_path"] = $params["project_path"];
		}
		if (!$_SESSION["project_path"]) {
			$_SESSION["project_path"] = "./";
		}
		Git_Config::setProjectPath($_SESSION["project_path"]);
		// apply git config
		Git_Config::apply();
	}

}
