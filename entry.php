<?php
header("Content-type:application/ld+json");
include '../../core/inc.php';
include 'db/cognizdb.php';
include 'db/cognizqueries.php';
include 'db/queries.php';
include 'regions.php';
//$base = 'http://resources.coreymwamba.co.uk/rhizome/';
$base = 'http://www.coreymwamba.co.uk/resources/rhizome';
////find the id
//list the person and groups. KISS.
//@context first; 
$cx_sub_array["f"] = "http://xmlns.com/foaf/0.1/";
$cx_sub_array["p"] = "http://www.w3.org/ns/prov#";
$cx_sub_array["m"] = "http://purl.org/ontology/mo/";
$groups_array["@context"] = $cx_sub_array;
?>

<?php
$groups = $_GET['gid'] ?? '';
$people = $_GET['pid'] ?? '';
if ($groups){
$group = get_one_thing('groups','id',$groups,$cdb);
$groups_array["@id"] = $base.'/g/'.$groups;
$groups_array["@type"] = "f:Group";
$groups_array["f:name"] = $group['name'];
$rg = $group['region'];
if(!empty($rg)){
$groups_array["f:based_near"] = $regions[$rg];
}
if($group['started']){
if($group['started'] > 0){
$groups_array['m:activity_start'] = $group['started'];
}
if($group['ended'] > 0){
$groups_array['m:activity_end'] = $group['ended'];
}
}
if($group['cp']){
$arr = explode(', ',$group['cp']);
$c_array = array();
foreach ($arr as $a) {
$c = get_one_thing('persons','id',$a,$cdb);
array_push($c_array, array("@type" => "f:Person", "f:name" => $c['name'], "@id" => $base.'/p/'.$c['id']));
}
$groups_array["f:member"] = $c_array;
}
if($group['pp']){
$arr = explode(', ',$group['pp']);
$p_array = array();
foreach ($arr as $a) {
$p = get_one_thing('persons','id',$a,$cdb);
array_push($p_array, array("@type" => "f:Person", "f:name" => $p['name'], "@id" => $base.'/p/'.$p['id']));
}
$groups_array["p:hadMember"] = $p_array;
}


}
if($people){
$person = get_one_thing('persons','id',$people,$cdb);
$groups_array["@id"] = $base.'/p/'.$people;
$groups_array["@type"] = "f:Person";
$groups_array["f:name"] = $person['name'];
$groups_array["m:instrument"] = $person['ins'];
$cql = $cdb->prepare("SELECT name,id FROM groups WHERE cp LIKE :value ORDER BY id ASC");
$cql->bindValue(':value', "%$people%", PDO::PARAM_STR);
$cql->execute();
$cur = $cql->fetchAll(PDO::FETCH_ASSOC);
$wql = $cdb->prepare("SELECT name,id FROM groups WHERE pp LIKE :value ORDER BY id ASC");
$wql->bindValue(':value', "%$people%", PDO::PARAM_STR);
$wql->execute();
$pre = $wql->fetchAll(PDO::FETCH_ASSOC);
if($cur){
$c_array = array();
foreach ($cur as $c) {
array_push($c_array, array("@type" => "f:Group", "f:name" => $c['name'], "@id" => $base.'/g/'.$c['id']));
}
$groups_array["f:memberOf"] = $c_array;
}
if($pre){
$p_array = array();
foreach ($pre as $p) {
array_push($p_array, array("@type" => "s:MusicGroup", "f:name" => $p['name'], "@id" => $base.'/g/'.$p['id']));
}
$groups_array["p:wasMemberOf"] = $p_array;
}
}
$pinfo = json_encode($groups_array, JSON_PRETTY_PRINT);
echo $pinfo;
?>