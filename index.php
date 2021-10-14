<?php
$re = $_GET['region'] ?? '';
$sy = $_GET['sy'] ?? '';
$ey = $_GET['ey'] ?? '';
$pe = $_GET['id'] ?? '';
$gr = $_GET['gid'] ?? '';
$faves = $_GET['faves'] ?? '';
if (!empty($re)){
$datastring[] = 'region='.$re;
$querystring[] = 'region='.$re;
}
if (!empty($sy)){
$datastring[] = 'sy='.$sy;
$querystring[] = 'sy='.$sy;
}
if (!empty($ey)){
$datastring[] = 'ey='.$ey;
$querystring[] = 'ey='.$ey;
}

if (!empty($pe)){
$datastring[] = 'id='.$pe;
$querystring[] = 'pid='.$pe;
}
if (!empty($gr)){
$datastring[] = 'gid='.$gr;
$querystring[] = 'gid='.$gr;
}
if (!empty($faves)){
$datastring[] = 'faves='.$faves;
$querystring[] = 'faves='.$faves;
}
include '../core/inc.php';
include 'db/cognizdb.php';
include 'db/cognizqueries.php';
include 'regions.php';
if($re != ''){
$area = $regions[$re];
}
else {
$area = 'All regions';
}
if($sy){
if($sy > 0){
$sd = $sy;
}
if($ey > 0){
$sd .= '-'.$ey.'';
}
else {$sd .= ' to present';}
}
$json = 'https://www.coreymwamba.co.uk/resources/rhizome/rhizome-json.php?'.join('&',$datastring);
$count = json_decode(file_get_contents($json),true);
$peo = $count['count']['people'];
$gro = $count['count']['groups'];
$qu = join('&',$querystring);
if($qu){
$qjson = file_get_contents('https://www.coreymwamba.co.uk/resources/rhizome/query?'.$qu);
$dq = json_decode($qjson,true);
$graph = $dq[0]["@graph"][0];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<base href="http://www.coreymwamba.co.uk/resources/" />
<title>THE RHIZOME</title>
<meta name="description" content="a necessarily unfinished network graph showing jazz/improv groups and musicians in Britain and Ireland" />
<link href="https://www.coreymwamba.co.uk/resources/style.css" rel="stylesheet" />
<style>
.links line {
  stroke: #999;
  stroke-opacity: 0.6;
}

.nodes circle {
  stroke: #fff;
  stroke-width: 1.5px;
}
.node:hover {
cursor: pointer;
}

svg#rhizome {
overflow: auto;
width: 800px; height: 600px
}
aside {
font-size: 80%;
background-color: rgba(250,250,250,0.8);
z-index: 400;
padding-left: 0.3em;
}

#metadata ul{
  list-style: none;

}
option {width: 12em}
input[type=number] {width: 6em}
label {display: block; margin: 0.3em}
section {border-top: 1px solid #bbb; border-bottom: 1px solid #bbb; padding: 0.3em 0; margin: 1em 0;}
@media (max-width: 800px){
svg#rhizome {width: 450px; height: 300px}
main {min-height: 10vh}
}
</style>
</head>
<body>
<main>
<svg id="rhizome"<?php if (isset($datastring)){echo ' data-query="'.join('&',$datastring).'"';}?>></svg>
<script src="rhizome/d3.min.js"></script>
<script src="rhizome/rhizome-2.js"></script>
</main>
<aside id="metadata">
<h1>THE RHIZOME</h1>

<?php
if(!$_GET){
?>
<p> <?php echo $peo;?> people; <?php echo $gro;?> groups. <strong>Desktop/laptop recommended</strong>.</p>

<?php
}
else {
include 'vis.php';
}
?>
<section>
<ul>
<li><a href="rhizome">RESET AND RELOAD</a></li>
<li><a href="rhizome/group-maker.php">Feed the RHIZOME with groups and people</a></li>
<li><a href="rhizome/picknmix.php">Pick and mix</a></li>
<li><a href="rhizome/search.php">Finder (text-based)</a></li>
<li><a href="rhizome/query">Query endpoint</a></li>
</ul>
</section>
<?php
if(!$_GET){
?>
<label for="finder">Focus the rhizome by entering a name</label>
<input type="text" id="finder" />
<input type="button" onclick="unsetFinder();" value="reset focus" />
<div id="sr"></div>
<?php
}
else {
echo '<div id="sr"></div>'."\n";
}
?>

<section>
<form method="GET">
<label>British/Irish region
<select name="region">
<option value=""></option>
<?php
foreach($regions as $key => $value){
echo '<option value="'.$key.'">'.$value.'</option>'."\n";
}
?>
</select>
</label>
<label>Year started <input type="number" min="1900" max="2018" name="sy" /></label>
<label>Year dissolved <input type="number" min="1900" max="2018" name="ey" /></label>
<input type="submit" value="FILTER" />
</form>
</section>
<p>by Corey Mwamba and Tom Ward</p>
<p>Inspired by a <a href="https://medium.com/@ktcita/the-new-uk-jazz-family-tree-b25db4b47cbe" target="_blank">beautiful, static version by Kimberley Crofts</a>. </p>
</aside>
<?php
include '../menu.html';
?>


<script>
<?php
if(!isset($datastring)){
?>
var holder = document.getElementById("finder");
holder.addEventListener("keyup",findNames,false);
function unsetFinder(){
var holder = document.getElementById("finder");
var nameInput = document.getElementById("sr");
nameInput.innerHTML = "";
holder.value = "";
allNodesOpacity(1);
}
function findNames() {
var holder = document.getElementById("finder");
var nameInput = document.getElementById("sr");
var xhr = new XMLHttpRequest();
xhr.onreadystatechange = function(){
	if (this.readyState==4 && this.status==200){
		nameInput.innerHTML = this.responseText;
		}
	}
xhr.open("GET","https://www.coreymwamba.co.uk/resources/rhizome/groupsearch.php?b="+holder.value);
xhr.send();
}
function displayGroup(id){
var nameInput = document.getElementById("sr");
var xhr = new XMLHttpRequest();
xhr.onreadystatechange = function(){
	if (this.readyState==4 && this.status==200){
		nameInput.innerHTML = this.responseText;
		}
	}
xhr.open("GET","https://www.coreymwamba.co.uk/resources/rhizome/groupsearch.php?j="+encodeURIComponent(id));
xhr.send();
}
<?php
}
?>
function displayMus(id){
var nameInput = document.getElementById("sr");
var xhr = new XMLHttpRequest();
xhr.onreadystatechange = function(){
	if (this.readyState==4 && this.status==200){
		nameInput.innerHTML = this.responseText;
		}
	}
xhr.open("GET","https://www.coreymwamba.co.uk/resources/rhizome/groupsearch.php?k="+encodeURIComponent(id));
xhr.send();
}
</script>
<?php
if ($qjson){
echo '<script type="application/ld+json">'."\n";
echo $qjson."\n";
echo '</script>'."\n";
}
?>
</body>
</html>