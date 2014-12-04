<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');

//define connection
$hostname = "localhost";
$username = "root";
$password = "";
$database = "sbd_namplus_v1";

//defint paging
define('ADMIN_PAGE_ROWS', 20);
define('MAX_RECORDS', 6);
define('CUR_ROWS',1);

//define('BASE_NAME', 'http://'.$_SERVER['HTTP_HOST'].'/');
define('BASE_NAME', 'http://'.$_SERVER['HTTP_HOST'].'/');
define('BASE_NAME_SECURE', 'https://'.$_SERVER['HTTP_HOST'].'/');

define('FACEBOOK_APP_ID', '736997686322439');
define('FACEBOOK_APP_SECRET', '23f1cc5a62b3726956d0f23a3b53df8f');

define('JOOMGA_API_KEY','api_c82693a6534e19ce016ec89dd8fcca32');

$mod_rewrite = true;
ini_set('display_errors', 1);
define('DEBUG', 0);

$arrPath = explode('/',@$_GET['p']);

//define('PATH_CLASS_MODEL', 'modules/'.$arrPath[0].'/model/');
define('PATH_CLASS_MODEL', 'libs/modules/');
define('PATH_CLASS_VIEW', 'libs/views/');
define('PATH_CLASS_MANAGER_VIEW', 'manager/libs/views/');


define('PATH_CAPTCHA_PHOTO', 'libs/captcha/');

define('PATH_ARTICLE_PHOTO', '../uploads/articles/');

?>