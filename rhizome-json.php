<?php
if(!ob_start("ob_gzhandler")) ob_start();
header("Content-type:application/json");
include '../../core/inc.php';
include 'db/cognizdb.php';
include 'db/cognizqueries.php';
include 'db/queries.php';
$r = $_GET['region'] ?? '';
$sy = $_GET['sy'] ?? '';
$ey = $_GET['ey'] ?? '';
$pe = $_GET['id'] ?? '';
$gr = $_GET['gid'] ?? '';
$faves = $_GET['faves'] ?? '';
$frag = $_GET['frag'] ?? '';
if ($frag){
$doc = 'https://www.coreymwamba.co.uk/resources/schedule';
$dom = new DOMDocument;
$dom->loadHTMLFile($doc);
$gig = $dom->getElementById($frag);
print "$gig->nodeValue";
}
else {
include 'sql-funcs.php';
$links_array = array();
$links_array_alum = array();
$nodes_array = array();

if (empty($_GET)){
$groups = get_groups($cdb);
$people = get_people($cdb);
foreach ($people as $p){
array_push($nodes_array, array("id" => $p['id'], "name" => $p['name'],"instrument" => $p['ins'], "type" => "person"));
}
}

else if ($gr){
$people = array();
$g = get_one_thing('groups','id',$gr,$cdb);
$year = $g['started'] ?? '';
array_push($nodes_array, array("id" => $g['id'], "name" => $g['name'], "type" => "group", "startdate" => $year ));
$member_array = explode(', ',$g['cp']);
$alum_array = explode(', ',$g['pp']);
if(is_array($member_array)){
foreach($member_array as $member){
if ($member){
array_push($links_array, array("source" => $member, "target" => $g['id'],"type" => "member"));
$p = get_one_thing('persons','id',$member,$cdb);
array_push($nodes_array, array("id" => $p['id'], "name" => $p['name'],"instrument" => $p['ins'], "type" => "person"));
array_push($people,$p['id']);
}
}
}
if(is_array($alum_array)){
foreach($alum_array as $alum){
if($alum){
array_push($links_array_alum, array("source" => $alum, "target" => $g['id'],"type" => "alum"));
$p = get_one_thing('persons','id',$alum,$cdb);
array_push($nodes_array, array("id" => $p['id'], "name" => $p['name'],"instrument" => $p['ins'], "type" => "person"));
array_push($people,$p['id']);
}
}
}
}

else {
if ($faves){
$people = array();
$query = get_one_thing('favourites','id',$faves,$cdb);
array_push($nodes_array, array("id" => $faves, "name" => $query['name'],"type" => "organisation"));
if (!empty($query['groups'])){
$groups = get_faves($query['groups'],$cdb);
foreach ($groups as $gro){
array_push($links_array, array("source" => $gro['id'], "target" => $faves, "type" => "member"));
}
}
$persons = explode(',',$query['persons']);
if (!empty($query['persons'])){
foreach ($persons as $per){
$p = get_person($per,$cdb);
array_push($links_array, array("source" => $p[0]['id'], "target" => $faves, "type" => "member"));
array_push($nodes_array, array("id" => $p[0]['id'], "name" => $p[0]['name'],"instrument" => $p[0]['ins'], "type" => "person"));
array_push($people,$p[0]['id']);
}
}
}

if ($pe){
$groups = filter_by_id($pe,$cdb);
}

if ($r && !$sy && !$ey){
$groups = filter_by_region($r,$cdb);
}
elseif ($r && $sy && !$ey){
$groups = filter_by_region_and_start($r,$sy,$cdb);
}
elseif ($r && $sy && $ey){
$groups = filter_by_region_and_period($r,$sy,$ey,$cdb);
}
elseif ($r && $ey && !$sy){
$groups = filter_by_region_and_end($r,$ey,$cdb);
}
elseif ($sy && !$r && !$ey){
$groups = filter_by_start($sy,$cdb);
}
elseif ($ey && $sy && !$r){
$groups = filter_by_period($sy,$ey,$cdb);
}
elseif ($ey && !$sy && !$r){
$groups = filter_by_end($ey,$cdb);
}

$people = array();

foreach ($groups as $g){
$new_arr = explode(', ',$g['cp']);
$pnew = explode(', ',$g['pp']);
foreach ($new_arr as $na){
if($na){
$person = get_person($na,$cdb);
$res = in_array($person[0]['id'],$nodes_array);
if (array_search($person[0]['id'],array_column($nodes_array,'id')) === false){
array_push($nodes_array, array("id" => $person[0]['id'], "name" => $person[0]['name'],"instrument" => $person[0]['ins'], "type" => "person"));
array_push($people,$person[0]['id']);
}
}
}
foreach ($pnew as $np){
if($np){
$person = get_person($np,$cdb);
if (array_search($person[0]['id'],array_column($nodes_array,'id'))===false){
array_push($nodes_array, array("id" => $person[0]['id'], "name" => $person[0]['name'],"instrument" => $person[0]['ins'], "type" => "person"));
array_push($people,$person[0]['id']);
}
}
}
}

}

foreach ($groups as $g){
array_push($nodes_array, array("id" => $g['id'], "name" => $g['name'], "type" => "group"));
$member_array = explode(', ',$g['cp']);
$alum_array = explode(', ',$g['pp']);
if(is_array($member_array)){
foreach($member_array as $member){
if ($member){
array_push($links_array, array("source" => $member, "target" => $g['id'],"type" => "member"));
}
}
}
if(is_array($alum_array)){
foreach($alum_array as $alum){
if($alum){
array_push($links_array_alum, array("source" => $alum, "target" => $g['id'],"type" => "alum"));
}
}
}
}

$l["count"] = array("groups" => count($groups), "people" => count($people));
$l["nodes"] = $nodes_array;
$l["links"]["memberOf"] = $links_array;
$l["links"]["alumOf"] = $links_array_alum;
$pinfo = json_encode($l, JSON_PRETTY_PRINT);
echo $pinfo;
}
?>