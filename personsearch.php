<?php
include '../../core/inc.php';
include 'db/cognizdb.php';
function find_person_like($name,$db){
$pql = $db->prepare("SELECT * FROM persons WHERE name LIKE :value AND display='1' ORDER BY id ASC");
$pql->bindValue(':value', "%$name%", PDO::PARAM_STR);
$pql->execute();
$v = $pql->fetchAll(PDO::FETCH_ASSOC);
return $v;
}
$q = $_GET['q'] ?? '';
$matches = find_person_like($q,$cdb);
if($matches){
foreach ($matches as $m){
echo '<span style="display: block; margin-bottom: 0.8em">'.$m['name'].' ('.$m['ins'].') <br /><a onclick="acceptCurrent(\''.$m['id'].'\',\''.addslashes($m['name']).'\')" href="#">Current member</a> || <a onclick="acceptPrevious(\''.$m['id'].'\',\''.addslashes($m['name']).'\')" href="#">Previous member</a></span>'."\n";
}
}
else {
echo '<label>List instruments/voice here <input type="text" id="ins" /></label> <input type="button" onclick="addToDb()" value="add person" />'."\n";
}
?>