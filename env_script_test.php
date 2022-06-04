<?php



$env = file_get_contents('public/web/assets/assets/mobile.env');

preg_match_all('~(["\'])([^"\']+)\1~', $env, $arr);
// echo ($arr[0][0]);
// echo $env;

exec('ifconfig', $str);
// $str= shell_exec('ipconfig /all');
$x = implode(";\n", $str);
// print_r($x);
preg_match_all('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $x, $ip_matches);
// print_r($ip_matches[0][1]);
$ip = $ip_matches[0][1];
$baseurl = $ip_matches[0][1] . ':8000/' . 'api';
$newEnv = str_replace(array($arr[0][0]), "'" . $baseurl . "'", $env);
echo $newEnv;
file_put_contents('public/web/assets/assets/mobile.env', $newEnv);

$runcommand = 'php artisan serve --host=' . $ip_matches[0][1] . " --port:8000";


$runfile = file_get_contents('run.bat');

file_put_contents('run.bat', $runcommand . PHP_EOL, FILE_APPEND | LOCK_EX);