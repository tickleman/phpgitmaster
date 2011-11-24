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
