<?php
require_once(dirname(__FILE__).'/../config/config.php');
require_once(dirname(__FILE__).'/functions.php');
//require_once(dirname(__FILE__).'/../lib/encrypt.php');

echo password_hash('password', PASSWORD_DEFAULT);

//echo encrypt('徳田');

?>