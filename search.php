<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>RHIZOME Finder</title>
<link href="http://www.coreymwamba.co.uk/resources/style.css" rel="stylesheet" />
<style>
option {width: 12em}
input {font-size: 120%}
input[type=number] {width: 6em;}
label {display: block; margin: 0.3em}
section {border-top: 1px solid #bbb; border-bottom: 1px solid #bbb; padding: 0.3em 0; margin: 1em 0;}
</style>
</head>
<body>
<main>
<h1>RHIZOME Finder</h1>
<?php 
if($_GET){
include './text.php'; 
}
else {echo '<p>Results will appear here.</p>';}
?>
</main>
<aside>
<h2>rhizome index search</h2>
<form>
<label>Enter a musician or group name
<input type="text" id="finder" /></label>
<div id="sr"></div>
</form>
<section>
<p><strong>OR</strong></p>
</section>
<form method="GET">
<h2>Filter groups</h2>
<label>British/Irish region
<select name="region">
<option value=""></option>
<?php
include 'regions.php';
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
</aside>

<script>
var holder = document.getElementById("finder");
holder.addEventListener("keyup",findNames,false);
function findNames() {
var holder = document.getElementById("finder");
var nameInput = document.getElementById("sr");
var xhr = new XMLHttpRequest();
xhr.onreadystatechange = function(){
	if (this.readyState==4 && this.status==200){
		nameInput.innerHTML = this.responseText;
		}
	}
xhr.open("GET","https://www.coreymwamba.co.uk/resources/rhizome/groupsearch.php?q="+holder.value);
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
xhr.open("GET","https://www.coreymwamba.co.uk/resources/rhizome/groupsearch.php?group="+encodeURIComponent(id));
xhr.send();
}

function displayMus(id){
var nameInput = document.getElementById("sr");
var xhr = new XMLHttpRequest();
xhr.onreadystatechange = function(){
	if (this.readyState==4 && this.status==200){
		nameInput.innerHTML = this.responseText;
		}
	}
xhr.open("GET","http://www.coreymwamba.co.uk/resources/rhizome/groupsearch.php?mus="+encodeURIComponent(id));
xhr.send();
}
</script>
<?php include '../menu.html'; ?>
</body>
</html>