<?php
$request_headers        = apache_request_headers();
$http_origin            = $request_headers['Origin'];
$allowed_http_origins   = array(

                            "https://www.coreymwamba.co.uk",
							"https://coreymwamba.co.uk",
                          );
if (in_array($http_origin, $allowed_http_origins)){  
    header("Access-Control-Allow-Origin: " . $http_origin);
}
header("Content-type:text/plain");
include '../../core/inc.php';
include 'db/cognizdb.php';

function make_id($str){
$pattern = '/\W+/'; 
$replace = '_';
$out = strtolower(preg_replace($pattern, $replace, $str));
return $out;
}
function insert_group($id,$name,$cp,$pp,$region,$start,$end,$db){
$up = "INSERT IGNORE INTO groups (id, name, cp, pp, region, started, ended) VALUES (?,?,?,?,?,?,?)";
$i_gig = $db->prepare($up);
$i_gig->execute(array($id,$name,$cp,$pp,$region,$start,$end));
}
$blocked = array('michael-janisch-f93e57aa, ','mike-janisch-ee142ffe, ');
$name = $_POST['gname'] ?? '';
$region = $_POST['region'] ?? '';
$start = $_POST['start'] ?? '';
$end = $_POST['end'] ?? '';
$mem = $_POST['mem'] ?? '';
$alum = $_POST['alum'] ?? '';
$curr = join(', ',$mem);
$prev = join(', ',$alum);
$id = make_id($name);
$curr = str_replace($blocked,'',$curr);
$prev = str_replace($blocked,'',$prev);

$errarray = [];
if ((!isset($mem) && !isset($alum)) || (count($mem) < 2 && !isset($alum)) || (count($alum) < 2 && !isset($mem)) || (count($mem) < 2 && count($alum) < 2)) {
array_push($errarray,'Please add some more band members. Groups are made up of two or more people.');
}
if (empty($region)){
array_push($errarray,'Please set a region');
}
if (empty($name)){
array_push($errarray,'Please set a group name');
}
$err = count($errarray);
if ($err > 0){
echo '<p><strong>Errors</strong></p>'."\n";
echo '<ol>'."\n";
foreach ($errarray as $e) {
echo '<li>'.$e.'</li>'."\n";
}
echo '</ol>'."\n";
}
else {
insert_group($id,$name,$curr,$prev,$region,$start,$end,$cdb);
echo '<p><strong>Adding new group...</strong></p>';
}
?>