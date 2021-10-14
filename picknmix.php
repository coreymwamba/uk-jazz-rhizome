<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<link href="https://www.coreymwamba.co.uk/resources/style.css" rel="stylesheet" />
<style>
form {font-size: 90%}
label {display: block}
input[type="text"] {display: block;}
input {font-size: 130%; margin-top: 0.2em;}
input[type=submit],input[type=button] {background-color: #fff; padding: 0.6em; color: #000; text-decoration: none; font-size: 1em; margin: 2em; border-radius: 8px; -moz-border-radius: 8px; -webkit-border-radius: 8px;; border: 1px solid #555}
input[type=button]:hover,input[type=submit]:hover {background-color: #555; color: #fff; cursor:pointer}
fieldset {border: 0px solid}
</style>
<title>RHIZOME pick 'n' mix</title>
</head>
<body>
<main>
<h1>RHIZOME pick 'n' mix</h1>
<form method="POST" action="https://www.coreymwamba.co.uk/resources/rhizome/pm-gen.php" target="_blank">
<label>Enter a musician OR group name <input type="text" id="finder" /></label>
<section>
<p>select from the list below...</p>
<div id="sr"></div>
</section>
<label>Give your RHIZOME a name (optional) <input type="text" id="pm_name" name="pm_name" /></label>
<input type="submit" value="Make my rhizome!" /><input type="button" value="reset?" onclick="resetForm()" />
<input type="hidden" id="id_list" />
</form>
</main>
<aside>
<h2>selections</h2>
<ul id="out">
</ul>
</aside>
<script>
function resetForm(){
              var retVal = confirm("Do you want to reset the form?");
               if( retVal == true ){
               window.location.reload();  
               return true;
               }
               else{
                  return false;
               }
            }

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
xhr.open("GET","https://www.coreymwamba.co.uk/resources/rhizome/groupsearch.php?a="+holder.value);
xhr.send();
}


function acceptGroup(id,str){
var formStore = document.getElementById("id_list");
var storeId = document.createElement("input");
storeId.setAttribute("type","hidden");
storeId.setAttribute("name","faves[]");
storeId.setAttribute("value",id);
formStore.appendChild(storeId);

var vis = document.getElementById("out");
var storeLi = document.createElement("li");
storeLi.innerHTML = str;
vis.appendChild(storeLi);

	var nameInput = document.getElementById("sr");
	while (nameInput.hasChildNodes()){
		nameInput.removeChild(nameInput.lastChild);
		}
		holder.value="";

	var backToBox = document.getElementById("finder");
	backToBox.focus();
	}
function acceptPerson(id,str){
var formStore = document.getElementById("id_list");
var storeId = document.createElement("input");
storeId.setAttribute("type","hidden");
storeId.setAttribute("name","pfaves[]");
storeId.setAttribute("value",id);
formStore.appendChild(storeId);

var vis = document.getElementById("out");
var storeLi = document.createElement("li");
storeLi.innerHTML = str;
vis.appendChild(storeLi);

	var nameInput = document.getElementById("sr");
	while (nameInput.hasChildNodes()){
		nameInput.removeChild(nameInput.lastChild);
		}
		holder.value="";

	var backToBox = document.getElementById("finder");
	backToBox.focus();
	}

</script>
<?php include '../menu.html'; ?>
</body>
</html>