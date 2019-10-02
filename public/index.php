<?php
defined('APPLICATION_PATH') || define('APPLICATION_PATH',realpath(dirname(__FILE__).'/../app'));

//directory separator
const DS = DIRECTORY_SEPARATOR;

require APPLICATION_PATH.DS.'config'.DS.'config.php';

$page     = get('page','home');
$model    = $config['MODEL_PATH'] . $page . '.php';
$view     = $config['VIEW_PATH'] . $page . '.phtml';
$page_404 = $config['VIEW_PATH'] .'404.phtml';
$header = $config['VIEW_PATH'] .'header.phtml';
$footer = $config['VIEW_PATH'] .'footer.phtml';

$main_content =  $page_404;
if(file_exists($model)){
    require $model;
}
if(file_exists($view)){
    $main_content = $view;
}
include $config['VIEW_PATH'] . 'layout.phtml';



?>

