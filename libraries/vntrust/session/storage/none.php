<?php
if (! defined('IN_vnT'))
{
  die('Hacking attempt!');
}


class SESessionStorageNone extends SESessionStorage
{
	function register()
	{
		// Handled by PHP (C code faster than PHP code)
    return;
	}
}

?>