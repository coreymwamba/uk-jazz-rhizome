<?php
$request_headers        = apache_request_headers();
$http_origin            = $request_headers['Origin'];
$allowed_http_origins   = array(
                            "http://cmbook.local",
   							"http://corlap.local",
                            "https://www.coreymwamba.co.uk",
							"https://coreymwamba.co.uk",
                            "http://corey.netiva-hosting.net",
                          );
if (in_array($http_origin, $allowed_http_origins)){  
    header("Access-Control-Allow-Origin: " . $http_origin);
}
include '../../core/inc.php';
include 'db/cognizdb.php';
$groups = join(',',$_POST['faves']) ?? '';
$persons = join(',',$_POST['pfaves']) ?? '';
$name = $_POST['pm_name'] ?? '';
$id = hash('md5',$groups.$persons);
function insert_fave($id,$name,$groups,$persons,$db){
$up = "INSERT IGNORE INTO favourites (id, name, groups, persons) VALUES (?,?,?,?)";
$i_gig = $db->prepare($up);
$i_gig->execute(array($id,$name,$groups,$persons));
}
insert_fave($id,$name,$groups,$persons,$cdb);
unset($_POST);
header("Location: http://www.coreymwamba.co.uk/resources/rhizome/?faves=$id");
?>