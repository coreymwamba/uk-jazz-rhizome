<?php
include '../../core/inc.php';
include 'db/cognizdb.php';
include 'regions.php';
function big_find_like($name,$db){
$pql = $db->prepare("SELECT * FROM groups WHERE name LIKE :value ORDER BY id ASC");
$pql->bindValue(':value', "%$name%", PDO::PARAM_STR);
$pql->execute();
$v['groups'] = $pql->fetchAll(PDO::FETCH_ASSOC);
$eql = $db->prepare("SELECT * FROM persons WHERE name LIKE :value AND display='1' ORDER BY id ASC");
$eql->bindValue(':value', "%$name%", PDO::PARAM_STR);
$eql->execute();
$v['people'] = $eql->fetchAll(PDO::FETCH_ASSOC);
return $v;
}
function get_one_thing($table,$field,$value,$db) {
$query = $db->prepare("SELECT * FROM $table WHERE $field=?");
$query->execute(array($value));
$q = $query->fetch(PDO::FETCH_ASSOC);
return $q;
}
$q = $_GET['q'] ?? '';
$g = $_GET['group'] ?? '';
$i = $_GET['mus'] ?? '';
$a = $_GET['a'] ?? '';
$b = $_GET['b'] ?? '';
$j = $_GET['j'] ?? '';
$k = $_GET['k'] ?? '';
if($a){
$matches = big_find_like($a,$cdb);
if($matches['groups']){
echo '<h3>groups</h3>'."\n";
foreach ($matches['groups'] as $m){
$id = addcslashes($m['id'],"'+)(:!?&/");
echo '<a style="display: block" onclick="acceptGroup(\''.$id.'\',\''.addslashes($m['name']).'\');">'.$m['name'].'</a>'."\n";
}
}
if($matches['people']){
echo '<h3>people</h3>'."\n";
foreach ($matches['people'] as $m){
$id = addcslashes($m['id'],"'+)(:!?&/");
echo '<a style="display: block" onclick="acceptPerson(\''.$id.'\',\''.addslashes($m['name']).'\');">'.$m['name'].'</a>'."\n";
}
}
}

if($b){
$matches = big_find_like($b,$cdb);
if($matches){
echo '<h2>Groups</h2>';
foreach ($matches['groups'] as $m){
$id = addcslashes($m['id'],"'+)(:!?&/");
echo '<a style="display: block" onclick="highlightNode(\''.$id.'\');displayGroup(\''.$id.'\');">'.$m['name'].'</a>'."\n";
}
echo '<h2>Musicians</h2>';
foreach ($matches['people'] as $p){
echo '<a style="display: block" onclick="highlightNode(\''.$p['id'].'\');displayMus(\''.$p['id'].'\')">'.$p['name'].' ('.$p['ins'].')</a>'."\n";
}
}
else {
echo 'no matches - sorry. <a href="group-maker.php">Add people</a>"'."\n";
}
}
if ($j){
$group = get_one_thing('groups','id',$j,$cdb);
echo '<h2><a href="http://www.coreymwamba.co.uk/resources/rhizome/?gid='.$group['id'].'">'.$group['name'].'</a></h2>'."\n";
$rg = $group['region'];
if(!empty($rg)){
echo '<p>'.$regions[$rg].'</p>'."\n";
}
if($group['started']){
if($group['started'] > 0){
$sd = $group['started'];
}
if($group['ended'] > 0){
$sd .= '-'.$group['ended'].'';
}
else {$sd .= ' to present';}
echo '<p>'.$sd.'</p>';
}

if($group['cp']){
echo '<h3>Current Members</h3>'."\n";
$arr = explode(', ',$group['cp']);
foreach ($arr as $a) {
$mus = get_one_thing('persons','id',$a,$cdb);
echo '<a style="display: block" onclick="highlightNode(\''.$mus['id'].'\');displayMus(\''.$mus['id'].'\')">'.$mus['name'].' ('.$mus['ins'].')</a>'."\n";
}

}
if($group['pp']){

echo '<h3>Previous Members</h3>'."\n";
$arr = explode(', ',$group['pp']);
foreach ($arr as $a) {
$mus = get_one_thing('persons','id',$a,$cdb);
echo '<a style="display: block" onclick="highlightNode(\''.$mus['id'].'\');displayMus(\''.$mus['id'].'\')">'.$mus['name'].' ('.$mus['ins'].')</a>'."\n";
}
}
}

if($k){
$mus = get_one_thing('persons','id',$k,$cdb);
echo '<h2><a href="http://www.coreymwamba.co.uk/rhizome/?id='.$mus['id'].'">'.$mus['name'].'</a> ('.$mus['ins'].')</h2>'."\n";
$mid = $mus['id'];
$cql = $cdb->prepare("SELECT name,id FROM groups WHERE cp LIKE :value ORDER BY id ASC");
$cql->bindValue(':value', "%$mid%", PDO::PARAM_STR);
$cql->execute();
$cur = $cql->fetchAll(PDO::FETCH_ASSOC);
$wql = $cdb->prepare("SELECT name,id FROM groups WHERE pp LIKE :value ORDER BY id ASC");
$wql->bindValue(':value', "%$mid%", PDO::PARAM_STR);
$wql->execute();
$pre = $wql->fetchAll(PDO::FETCH_ASSOC);
if($cur){
echo '<h3>Current groups</h3>'."\n";
foreach ($cur as $m) {
$id = str_replace('+','\+',$m['id']);
echo '<a style="display: block" onclick="highlightNode(\''.$id.'\');displayGroup(\''.$id.'\')">'.$m['name'].'</a>'."\n";
}
}
if($pre){
echo '<h3>Previous groups</h3>'."\n";

foreach ($pre as $m) {
$id = str_replace('+','\+',$m['id']);
echo '<a style="display: block" onclick="highlightNode(\''.$id.'\');displayGroup(\''.$id.'\')">'.$m['name'].'</a>'."\n";
}

}
}

//text-based
if ($q){
$matches = big_find_like($q,$cdb);
if($matches){
echo '<h2>Groups</h2>';
foreach ($matches['groups'] as $m){
echo '<a style="display: block" href="http://www.coreymwamba.co.uk/resources/rhizome/search.php?gid='.$m['id'].'">'.$m['name'].'</a>'."\n";
}
echo '<h2>Musicians</h2>';
foreach ($matches['people'] as $p){
echo '<a style="display: block" href="http://www.coreymwamba.co.uk/resources/rhizome/search.php?pid='.$p['id'].'">'.$p['name'].' ('.$p['ins'].')</a>'."\n";
}
}
else {
echo 'no matches - sorry.'."\n";
}
}
if ($g){
$group = get_one_thing('groups','id',$g,$cdb);
echo '<h2><a href="http://www.coreymwamba.co.uk/resources/rhizome/?gid='.$group['id'].'">'.$group['name'].'</a></h2>'."\n";
$rg = $group['region'];
if(!empty($rg)){
echo '<p>'.$regions[$rg].'</p>'."\n";
}
if($group['started']){
if($group['started'] > 0){
$sd = $group['started'];
}
if($group['ended'] > 0){
$sd .= '-'.$group['ended'].'';
}
else {$sd .= ' to present';}
echo '<p>'.$sd.'</p>';
}
if($group['cp']){
echo '<h3>Current Members</h3>'."\n";
$arr = explode(', ',$group['cp']);
foreach ($arr as $a) {
$mus = get_one_thing('persons','id',$a,$cdb);
echo '<a style="display: block" onclick="displayMus(\''.$mus['id'].'\')" href="#">'.$mus['name'].' ('.$mus['ins'].')</a>'."\n";
}

}
if($group['pp']){

echo '<h3>Previous Members</h3>'."\n";
$arr = explode(', ',$group['pp']);
foreach ($arr as $a) {
$mus = get_one_thing('persons','id',$a,$cdb);
echo '<a style="display: block" onclick="displayMus(\''.$mus['id'].'\')" href="#">'.$mus['name'].' ('.$mus['ins'].')</a>'."\n";
}
}
}
if($i){
$mus = get_one_thing('persons','id',$i,$cdb);
echo '<h2><a href="http://www.coreymwamba.co.uk/rhizome/?id='.$mus['id'].'">'.$mus['name'].'</a></h2>'."\n";
echo '<h3>'.$mus['ins'].'</h3>'."\n";
$mid = $mus['id'];
$cql = $cdb->prepare("SELECT name,id FROM groups WHERE cp LIKE :value ORDER BY id ASC");
$cql->bindValue(':value', "%$mid%", PDO::PARAM_STR);
$cql->execute();
$cur = $cql->fetchAll(PDO::FETCH_ASSOC);
$wql = $cdb->prepare("SELECT name,id FROM groups WHERE pp LIKE :value ORDER BY id ASC");
$wql->bindValue(':value', "%$mid%", PDO::PARAM_STR);
$wql->execute();
$pre = $wql->fetchAll(PDO::FETCH_ASSOC);
if($cur){
echo '<h3>Current groups</h3>'."\n";
foreach ($cur as $m) {
echo '<a style="display: block" href="#" onclick="displayGroup(\''.$m['id'].'\')">'.$m['name'].'</a>'."\n";
}
}
if($pre){
echo '<h3>Previous groups</h3>'."\n";

foreach ($pre as $m) {
echo '<a style="display: block" href="#" onclick="displayGroup(\''.$m['id'].'\')">'.$m['name'].'</a>'."\n";
}

}
}
?>