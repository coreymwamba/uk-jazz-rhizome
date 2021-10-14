<?php
if (empty($_GET)){
header("Content-type:text/html");
include 'regions.php';
$tableregions[] = '<table><thead><tr><td>CODE</td><td>REGION</td></tr></thead>';
$tableregions[] .= '<tbody>';
foreach($regions as $key => $value){
$tableregions[] .= '<tr><td>'.$key.'</td><td>'.$value.'</td></tr>';

}
$tableregions[] .= '</tbody></table>';
$table = join('',$tableregions);
$menu = file_get_contents('../menu.html');
$doc = <<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>RHIZOME query endpoint</title>
<link href="https://www.coreymwamba.co.uk/resources/style.css" rel="stylesheet" />
<style>
main {font-size: 90%}
aside {font-size:70%}
iframe {height: 80vh; width: 90%; display: block; margin-top: 1.5em; border: 1px solid #36b; padding: 0.2em; font-size: 80%}
</style>

</head>
<body>
<main>
<h1>RHIZOME query endpoint</h1>
<p>This is a simple query endpoint for RHIZOME data. A GET request with parameters will respond with JSON-LD results.</p>
<h2>examples</h2>
<p>The example response will appear below.</p>
<dl>
<dt>General name search<dt>
<dd><a href="https://www.coreymwamba.co.uk/resources/rhizome/query?name=leo" target="queryres">https://www.coreymwamba.co.uk/resources/rhizome/query?name=leo</a></dd>
<dt>Search by Person ID</dt>
<dd><a href="https://www.coreymwamba.co.uk/resources/rhizome/query?pid=rachel-musson-1acf7057" target="queryres">https://www.coreymwamba.co.uk/resources/rhizome/query?pid=rachel-musson-1acf7057</a></dd>
<dt>Search by region and start year</dt>
<dd><a href="https://www.coreymwamba.co.uk/resources/rhizome/query?region=E---&sy=2008" target="queryres">https://www.coreymwamba.co.uk/resources/rhizome/query?region=E---&sy=2008</a></dd>
</dl>
<iframe name="queryres">The response will appear here.</iframe>
</main>
<aside>
<dl>
<dt>Endpoint URL</dt>
<dd>https://www.coreymwamba.co.uk/resources/rhizome/query</dd>
<dt>Response format</dt>
<dd>JSON-LD</dd>
<dt>Ontologies used</dt>
<dd><a href="http://xmlns.com/foaf/spec/">FOAF</a></dd> 
<dd><a href="http://musicontology.com/specification/">Music Ontology</a></dd>
<dd><a href="https://www.w3.org/TR/prov-o/">PROV-O</a></dd> 
<dt>GET Parameters</dt>
<dd>
<dl>
<dt>name (string)</dt>
<dd>a general search for a person or group. Cannot be used with other parameters.</dd>
<dt>pid (id)</dt><dd>person ID. Cannot be used with other parameters.</dd>
<dt>gid (id)</dt><dd>group ID. Cannot be used with other parameters.</dd>
<dt>region (code)</dt>
<dd>filter groups by geographical region.<dd>
<dt>sy (year)</dt>
<dd>year group was formed</dd>
<dt>ey (year)</dt>
<dd>year group was disbanded</dd>
</dl>
</dd>
</dl>
<h2>Regional codes</h2>
$table

</aside>
$menu
</body>
</html>
HTML;
echo $doc;
}
else {
header("Content-type:application/ld+json");
//header("Content-Disposition: attachment; filename=query.json");
include '../../core/inc.php';
include 'db/cognizdb.php';
include 'db/cognizqueries.php';
include 'db/queries.php';
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
$name = $_GET['name'] ?? '';
$region = $_GET['region'] ?? '';
$sy = $_GET['sy'] ?? '';
$ey = $_GET['ey'] ?? '';
$pid = $_GET['pid'] ?? '';
$gid = $_GET['gid'] ?? '';
include 'sql-funcs.php';
$base = 'http://www.coreymwamba.co.uk/resources/rhizome';
$l = array();
////find the id
//list the person and groups. KISS.
//@context first; 
$l['searchResults'] = array();
$cx = array();
//$l['searchResults']["@id"] = 'http://www.coreymwamba.co.uk'.$_SERVER['REQUEST_URI'];
$cx[1]["@context"]["f"] = "http://xmlns.com/foaf/0.1/";
$cx[1]["@context"]["p"] = "http://www.w3.org/ns/prov#";
$cx[1]["@context"]["m"] = "http://purl.org/ontology/mo/";
if ($pid){
$person = get_one_thing('persons','id',$pid,$cdb);
$final_array["@id"] = $base.'/p/'.$pid;
$final_array["@type"] = "f:Person";
$final_array["f:name"] = $person['name'];
$final_array["m:instrument"] = $person['ins'];
$cql = $cdb->prepare("SELECT name,id FROM groups WHERE cp LIKE :value ORDER BY id ASC");
$cql->bindValue(':value', "%$pid%", PDO::PARAM_STR);
$cql->execute();
$cur = $cql->fetchAll(PDO::FETCH_ASSOC);
$wql = $cdb->prepare("SELECT name,id FROM groups WHERE pp LIKE :value ORDER BY id ASC");
$wql->bindValue(':value', "%$pid%", PDO::PARAM_STR);
$wql->execute();
$pre = $wql->fetchAll(PDO::FETCH_ASSOC);
if($cur){
$c_array = array();
foreach ($cur as $c) {
array_push($c_array, array("@type" => "f:Group", "f:name" => $c['name'], "@id" => $base.'/g/'.$c['id']));
}
$final_array["m:member"] = $c_array;
}
if($pre){
$p_array = array();
foreach ($pre as $p) {
array_push($p_array, array("@type" => "f:Group", "f:name" => $p['name'], "@id" => $base.'/g/'.$p['id']));
}
$final_array["p:wasMemberOf"] = $p_array;
}
array_push($l['searchResults'],$final_array);
}
else if ($gid){
$group = get_one_thing('groups','id',$gid,$cdb);;
$final_array["@id"] = $base.'/g/'.$gid;
$final_array["@type"] = "f:Group";
$final_array["f:name"] = $group['name'];
$rg = $group['region'];
if(!empty($rg)){
$final_array["f:based_near"] = $regions[$rg];
}
if($group['started']){
if($group['started'] > 0){
$final_array['m:activity_start'] = $group['started'];
}
if($group['ended'] > 0){
$final_array['m:activity_end'] = $group['ended'];
}
}
if($group['cp']){
$arr = explode(', ',$group['cp']);
$c_array = array();
foreach ($arr as $a) {
$c = get_one_thing('persons','id',$a,$cdb);
array_push($c_array, array("@type" => "f:Person", "f:name" => $c['name'], "@id" => $base.'/p/'.$c['id']));
}
$final_array["f:member"] = $c_array;
}
if($group['pp']){
$arr = explode(', ',$group['pp']);
$p_array = array();
foreach ($arr as $a) {
$p = get_one_thing('persons','id',$a,$cdb);
array_push($p_array,array("@type" => "f:Person", "f:name" => $p['name'], "@id" => $base.'/p/'.$p['id']));
}
$final_array["p:hadMember"] = $p_array;
}
array_push($l['searchResults'],$final_array);
}

else if ($name){
$matches = big_find_like($name,$cdb);
if($matches){
foreach ($matches['groups'] as $g){

$final_array["@id"] = $base.'/g/'.$g['id'];
$final_array["@type"] = "f:Group";
$final_array["f:name"] = $g['name'];
array_push($l['searchResults'],$final_array);
$final_array = array();
}
foreach ($matches['people'] as $p){

$final_array["@id"] = $base.'/p/'.$p['id'];
$final_array["@type"] = "f:Person";
$final_array["f:name"] = $p['name'];
$final_array["m:instrument"] = $p['ins'];
array_push($l['searchResults'],$final_array);
$final_array = array();
}

}
}
else {
if ($region && !$sy && !$ey){
$groups = filter_by_region($region,$cdb);
}
elseif ($region && $sy && !$ey){
$groups = filter_by_region_and_start($region,$sy,$cdb);
}
elseif ($region && $sy && $ey){
$groups = filter_by_region_and_period($region,$sy,$ey,$cdb);
}
elseif ($region && $ey && !$sy){
$groups = filter_by_region_and_end($region,$ey,$cdb);
}
elseif ($sy && !$region && !$ey){
$groups = filter_by_start($sy,$cdb);
}
elseif ($ey && $sy && !$region){
$groups = filter_by_period($sy,$ey,$cdb);
}
elseif ($ey && !$sy && !$region){
$groups = filter_by_end($ey,$cdb);
}

foreach ($groups as $g){
$final_array["@id"] = $base.'/g/'.$g['id'];
$final_array["@type"] = "f:Group";
$final_array["f:name"] = $g['name'];
$rg = $g['region'];
if(!empty($rg)){
$final_array["f:based_near"] = $regions[$rg];
}
if($g['started']){
if($g['started'] > 0){
$final_array['m:activity_start'] = $g['started'];
}
}
if($g['ended']){ 
if($g['ended'] > 0){
$final_array['m:activity_end'] = $g['ended'];
}
}
if($g['cp']){
$arr = explode(', ',$g['cp']);
$c_array = array();
foreach ($arr as $a) {
$c = get_one_thing('persons','id',$a,$cdb);
array_push($c_array, array("@type" => "f:Person", "f:name" => $c['name'], "@id" => $base.'/p/'.$c['id']));
}
$final_array["f:member"] = $c_array;
}
if($g['pp']){
$arr = explode(', ',$g['pp']);
$p_array = array();
foreach ($arr as $a) {
$p = get_one_thing('persons','id',$a,$cdb);
array_push($p_array, array("@type" => "f:Person", "f:name" => $p['name'], "@id" => $base.'/p/'.$p['id']));
}
$final_array["p:hadMember"] = $p_array;
}
array_push($l['searchResults'],$final_array);
$final_array = array();
}
}

$aa["searchResults"]["a"]["b"]["@id"] = $_SERVER['QUERY_STRING'];
$aa["searchResults"]["a"]["b"]["@context"] = array_values($cx[1]);
$aa["searchResults"]["a"]["b"]["@graph"] = array_values($l['searchResults']);
$pinfo = json_encode(array_values($aa["searchResults"]["a"]), JSON_PRETTY_PRINT);
echo $pinfo;
}

?>