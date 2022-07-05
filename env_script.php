<?php



$env = file_get_contents('public/web/assets/assets/mobile.env');

preg_match_all('~(["\'])([^"\']+)\1~', $env, $arr);
// echo ($arr[0][0]);
// echo $env;

if (PHP_OS === 'WINNT') {
    $str = shell_exec('ipconfig /all');
} else {
    exec('ifconfig', $arr);
    $str = implode(";\n", $arr);
}


preg_match_all('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $str, $ip_matches);
// print_r($ip_matches);

if (PHP_OS === 'WINNT') {
    $ip = $ip_matches[0][0];
} else {

    $ip = $ip_matches[0][1];
}
// print_r($ip);

$baseurl = 'http://' . $ip . ':8000/' . 'api';
$newEnv = str_replace(array($arr[0][0]), "'" . $baseurl . "'", $env);
echo $newEnv;
file_put_contents('public/web/assets/assets/mobile.env', $newEnv);

$runcommand = 'php artisan serve --host=' . $ip . " --port=8000";


$runfile = file_get_contents('run.bat');

file_put_contents('run.bat', $runcommand . PHP_EOL, FILE_APPEND | LOCK_EX);