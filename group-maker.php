<?php

//echo 'Locked for maintenance, sorry. Come back on Friday 17th March.';
?>
<!DOCTYPE html>
<html>
<head>
<title>RHIZOME feeder</title>
<link href="http://www.coreymwamba.co.uk/resources/style.css" rel="stylesheet" />
<style>
#lists {display: flex; flex-direction: row; font-size: 70%;}
ul {list-style-type: none; margin: 0.2em; padding: 0 0.3em}
#pre_list {color: grey; border-left: 1px solid; }
form {font-size: 90%}
label {display: block}
input[type="text"] {display: block;}
input, select {font-size: 130%; margin-top: 0.2em;}
select {width: 12em}
input[type=button],input[type=reset] {background-color: #fff; padding: 0.6em; color: #000; text-decoration: none; font-size: 1em; margin: 2em; border-radius: 8px; -moz-border-radius: 8px;  -webkit-border-radius: 8px;; border: 1px solid #555}
input[type=button]:hover,input[type=reset]:hover {background-color: #555; color: #fff; cursor:pointer}
</style>
</head>
<body>
<main>
<h1>RHIZOME feeder</h1>
<div id="warning"></div>
<form>
<label>Type ONE musician name here <input type="text" id="finder" /></label>
<div id="sr"></div>
<div id="id_list"></div>
<label>Group name <input type="text" name="gname" /></label>
<label>Year started <input type="number" min="1900" max="2019" name="start" /></label>
<label>Year dissolved <input type="number" min="1900" max="2019" name="end" /></label>
<label>British/Irish region
<select name="region">
<option></option>
<?php
include 'regions.php';
foreach($regions as $key => $value){
echo '<option value="'.$key.'">'.$value.'</option>'."\n";
}
?>
</select>
</label>
<input type="button" value="reset?" onclick="resetForm()" /><input type="button" id="b1" value="add this group" />
</form>
</main>
<aside id="lists">
<ul id="cur_list">
<h2>current members</h2>
</ul>
<ul id="pre_list">
<h2>previous members</h2>
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
var memArray = [];
var alumArray = [];
var holder = document.getElementById("finder");
holder.addEventListener("keyup",findNames,false);
holder.addEventListener("keydown", function(){return(event.keyCode!=188)}, false);
function findNames() {
var holder = document.getElementById("finder");
var nameInput = document.getElementById("sr");
var xhr = new XMLHttpRequest();
xhr.onreadystatechange = function(){
	if (this.readyState==4 && this.status==200){
		nameInput.innerHTML = this.responseText;
		}
	}
xhr.open("GET","https://www.coreymwamba.co.uk/resources/rhizome/personsearch.php?q="+holder.value);
xhr.send();

}

function addToDb(){
var holder = document.getElementById("finder");
var inst = document.getElementById("ins");
var nameInput = document.getElementById("sr");
var vals = holder.value+"|"+inst.value;
var xhr = new XMLHttpRequest();
xhr.onreadystatechange = function(){
	if (this.readyState==4 && this.status==200){
		findNames();
		}
	}
xhr.open("GET","https://www.coreymwamba.co.uk/resources/rhizome/personadd.php?q="+vals);
xhr.send();
alert(holder.value+" added")
}

function acceptCurrent(id,str){
// having to write a failsafe so that people do not try to form groups entirely consisting of themselves
// two ways someone would try to do this:
// 1. add themselves to the current members' list twice
// 2. add themselves to both current and previous
// 3. slightly mis-spell their name --> manual check required(!)

var checkCurrMemInc = memArray.includes(id);
if (checkCurrMemInc === false){
var checkCurrPrevInc = alumArray.includes(id);
if (checkCurrPrevInc === false){
memArray.push(id);
var memInc = memArray.includes(id);
var formStore = document.getElementById("id_list");
var storeId = document.createElement("input");
storeId.setAttribute("type","hidden");
storeId.setAttribute("name","mem[]");
storeId.setAttribute("value",id);
formStore.appendChild(storeId);
var vis = document.getElementById("cur_list");
var storeLi = document.createElement("li");
storeLi.innerHTML = str;
vis.appendChild(storeLi);
}
else {
alert("If you're not in the band any more, you're previous. If you are still in the band, you're current. You can't be both here.");
console.log("duplicate attempted: "+id);
}
}
else {
alert("No duplicates. Enter another person's name");
console.log("duplicate attempted: "+id);
}
	var nameInput = document.getElementById("sr");
	while (nameInput.hasChildNodes()){
		nameInput.removeChild(nameInput.lastChild);
		}
		holder.value="";

	var backToBox = document.getElementById("finder");
	backToBox.focus();
	}

function acceptPrevious(id,str){
var checkPrevMemInc = alumArray.includes(id);
if (checkPrevMemInc === false){
var checkPrevCurrInc = memArray.includes(id);
if (checkPrevCurrInc === false){
memArray.push(id);
var formStore = document.getElementById("id_list");
var storeId = document.createElement("input");
storeId.setAttribute("type","hidden");
storeId.setAttribute("name","alum[]");
storeId.setAttribute("value",id);
formStore.appendChild(storeId);

var vis = document.getElementById("pre_list");
var storeLi = document.createElement("li");
storeLi.innerHTML = str;
vis.appendChild(storeLi);
}
else {
alert("If you're not in the band any more, you're previous. If you're still in the band, you're current. You can't be both here.")
console.log("duplicate attempted: "+id);
}
}
else {
alert("No duplicates. Enter another person's name");
console.log("duplicate attempted: "+id);
}
	var nameInput = document.getElementById("sr");
	while (nameInput.hasChildNodes()){
		nameInput.removeChild(nameInput.lastChild);
		}
		holder.value="";

	var backToBox = document.getElementById("finder");
	backToBox.focus();
	}

var subButton = document.getElementById("b1");
subButton.addEventListener("click",sendFile,true);

function sendFile() {
var formElement = document.querySelector("form");
var request = new XMLHttpRequest();
request.open("POST", "https://www.coreymwamba.co.uk/resources/rhizome/builder.php");
request.onload = function() {
	var warningdiv = document.getElementById("warning");
	if (request.readyState === 4) {
    if (request.status === 200) {
        warningdiv.innerHTML = request.responseText;
     	setTimeout(function() {warningdiv.style = "display: block;"}, 500);
     	setTimeout(function() {warningdiv.style = "display: none;"; warningdiv.innerHTML = "";window.location.reload();}, 5000);
    }
     else {
      alert(request.statusText);
      //warningdiv.innerHTML = "Error " + request.statusText + " occurred";
    }
}
  };

request.send(new FormData(formElement));

        }


</script>
<?php include '../menu.html'; ?>
</body>
</html>