<?php
include '../../core/inc.php';
include 'db/cognizdb.php';
function depunctuate($str){
$pattern = '/(\'|\&)/'; 
$replace = '_';
$out = preg_replace($pattern, $replace, $str);
$pattern = '/(\+)/'; 
$replace = '';
$out = preg_replace($pattern, $replace, $out);
return $out;
}
function insert_person($name,$ins,$db){
$id = strtolower($name);
$id = depunctuate($id);
$id = str_replace(' ','-',$id);
$name = str_replace('+','',$name);
$checksum = hash("crc32b", $name.'+'.$ins.'+'.time());
$id = $id.'-'.$checksum;
$up = "INSERT IGNORE INTO persons (id, name, ins) VALUES (?,?,?)";
$i_p = $db->prepare($up);
$i_p->execute(array($id,$name,$ins));
}
$q = $_GET['q'] ?? '';
$g = explode('|', $q);
if ($g[0] && $g[1]){
$n = $g[0];
$i = $g[1];
if(strstr($n,',')||strstr($n,', ')){
$l = explode(',',$n);
$n = ltrim($l[1]).' '.$l[0];
}
insert_person($n,$i,$cdb);
}
?>
