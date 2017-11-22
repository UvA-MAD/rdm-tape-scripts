<!DOCTYPE html>
<html>
<head>
 <title>SILS Research Data Archive: content</title>
 <link rel="stylesheet" href="mainstyle.css" type="text/css">
</head>
<body>
<div class="container">
<h2> projects currently in the Sils Research Data Archive</h2>
<?php
include 'config.php'; 
$con=connect_db();

if (array_key_exists('sc',$_GET)) {
   $sankey= preg_replace("/[^a-zA-Z]+/", "",$_GET['sc']);
   $sorting="order by `$sankey`";
} else {
   $sorting="";
}

$wsel="";

if (array_key_exists('selName',$_GET)) { 
    $wsel=" and Name = '".preg_replace("/[^a-zA-Z0-9_\.\-]+/", "",$_GET['selName'])."'";
} 

if (array_key_exists('selBarcode',$_GET)) { 
    $wsel=$wsel." and Archive.Barcode = ".'"'.preg_replace("/[^0-9]+/", "",$_GET['selBarcode']).'" ';
} 

if (array_key_exists('selGroup',$_GET)) { 
    $wsel=$wsel." and `Group` = ".'"'.preg_replace("/[^a-zA-Z]+/", "",$_GET['selGroup']).'" ';
} 

if (array_key_exists('selClass',$_GET)) { 
    $wsel=$wsel." and `Class` = ".'"'.preg_replace("/[^a-zA-Z]+/", "",$_GET['selClass']).'" ';
} 

$query="select Name,Archive.Barcode,TapeLocation,ExternalID,ArchiveDate,`Group`,Tape.Class,RequestID from Archive join Tape on Archive.Barcode = Tape.Barcode where (Class='Primary' or Class='GroupCopy') ".$wsel." ".$sorting;
$result = mysqli_query($con,$query);

$self="archive.php";

echo "<table class='gridtable' width='100%'>";

$i = 0.;
while($row = $result->fetch_assoc())
{
    $aid=$row["RequestID"];
    unset($row["RequestID"]);
    if ($i == 0) {
      $i++;
      echo "<tr>";
      foreach ($row as $key => $value) {
        echo "<th><a href='".$self."?sc=".$key."'>" . $key . "</a></th>";
      }
      echo "<th>info</th>";
      echo "</tr>";
    }
    echo "<tr>";
    foreach ($row as $key => $value) {
      if (($key == "Group") || ($key == "Barcode") || ($key == "Name") || ($key == "Class")) { 
        echo "<td><a href='".$self."?sel".$key."=".$value."'>" . $value."</a></td>";
      } else {
        echo "<td>". $value."</td>";
      } 
    }
    $id = $row["Name"];
    echo "<td><a href=showform.php?id=".$aid."><b>info</b></a></td>";
    echo "</tr>";
}
echo "</table>";

mysqli_close($con); ?>
</div>
</body>
</html>
