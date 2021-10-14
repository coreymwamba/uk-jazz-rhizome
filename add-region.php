<?php
include '../core/inc.php';
include 'db/cognizdb.php';
include 'db/cognizqueries.php';



if($_POST) {
$region = $_POST['region'] ?? '';
$sel = $_POST['sel'] ?? '';
if (isset($sel)){
foreach ($sel as $s){
$up = $cdb->prepare("UPDATE IGNORE groups SET region=? WHERE id=?");
$up->execute(array($region,$s));
}
}
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<style>
label {display: block;}
</style>
</head>
<body>
<form method="POST">
<select name="region">
<?php
include 'regions.php';
foreach($regions as $key => $value){
echo '<option value="'.$key.'">'.$value.'</option>'."\n";
}
?>
</select>
<?php
function get_nr_groups($db){
$query = $db->query("SELECT * FROM groups WHERE ISNULL(region) ORDER BY id ASC");
$q = $query->fetchAll(PDO::FETCH_ASSOC);
return $q;
}
$data = get_nr_groups($cdb);
foreach ($data as $d){
echo '<label><input type="checkbox" name="sel[]" value='.$d['id'].' /> '.$d['name'].'</label>'."\n";
}
?>
<input type="submit" value="submit!" />
</form>
</body>
<html>