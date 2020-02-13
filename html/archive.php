<!DOCTYPE html>
<html>
<head>
 <title>SILS Research Data Archive: content</title>
 <link rel="stylesheet" href="mainstyle.css" type="text/css">
</head>
<body>
<div class="container">
<h2> projects stored in the <a href=".">Sils Research Data Archive</a></h2>
<?php
include 'config.php'; 

$margs=array();

$con=connect_db();

if (array_key_exists('sc',$_GET)) {
   $sankey= preg_replace("/[^a-zA-Z]+/", "",$_GET['sc']);
   $sorting="order by `$sankey`";
   $margs['sc']=$_GET['sc'];
} else {
   $sorting="";
}

$wsel="";

if (array_key_exists('selName',$_GET)) { 
    $wsel=" and Name = '".preg_replace("/[^a-zA-Z0-9_\.\-]+/", "",$_GET['selName'])."'";
    $margs['selName']=$_GET['selName'];
} 

if (array_key_exists('selBarcode',$_GET)) { 
    $wsel=$wsel." and Archive.Barcode = ".'"'.preg_replace("/[^0-9]+/", "",$_GET['selBarcode']).'" ';
    $margs['selBarcode']=$_GET['selBarcode'];
} 

$sg="";
if (array_key_exists('selGroup',$_GET)) { 
    $sg=preg_replace("/[^a-zA-Z]+/", "",$_GET['selGroup']);
    $wsel=$wsel." and `Group` = ".'"'.$sg.'" ';
    $margs['selGroup']=$sg;
}  

if (array_key_exists('selClass',$_GET)) { 
    $wsel=$wsel." and `Class` = ".'"'.preg_replace("/[^a-zA-Z]+/", "",$_GET['selClass']).'" ';
    $margs['selClass']=$_GET['selClass'];
} 
$self="archive.php";
$cself=$self;
$ap="?";
foreach ($margs as $key => $value) {
  $cself=$cself.$ap.$key."=".$value;
  $ap="&";
}



echo "Select group:";

echo '<select name="groupselect" class="styled-select slate" onchange="location = this.value;">';
if ($sg != "") { 
   echo "<option value=''>$sg</option> ";
} else {
   echo "<option value=''>all</option> ";
}
$query="select distinct(`Group`)  from Archive join Tape on Archive.Barcode = Tape.Barcode where (Class='Primary' or Class='GroupCopy') order by `Group`";
$result = mysqli_query($con,$query);
while($row = $result->fetch_array()) {
    if ($row[0] != $sg) {
       echo "<option value='$self?selGroup=$row[0]'>$row[0]</option> ";
    }
} 
echo "</select><br/>";


$pg=1;
$rpp=20;

if (array_key_exists('page',$_GET)) { 
    $pg = 1*($_GET['page']);
} 

$from_query="from Archive join Tape on Archive.Barcode = Tape.Barcode where (Class='Primary' or Class='GroupCopy') ".$wsel." ".$sorting;
$numres = mysqli_query($con,"select count(*) as rlen ".$from_query)->fetch_array()[0];

$i=1;
$cb=1;
echo "<span class='rfloat'>results&nbsp;".(($pg-1)*$rpp+1)."&nbsp;-&nbsp;".min($numres,$pg*$rpp)." of $numres.&nbsp;&nbsp; Select page ";
while ($cb <=$numres) {
    if ($i != $pg) {
       echo "<a href='".$cself.$ap."page=".$i."'>$i</a>&nbsp;";
    } else {
       echo "<b>$i</b>&nbsp;";
    }
    $cb=$cb+$rpp;
    $i=$i+1;
}
echo "</span>";


$query="select Name,Archive.Barcode,TapeLocation,ExternalID,ArchiveDate,`Group`,Tape.Class,Number,RequestID ".$from_query." limit ".(($pg-1)*$rpp).",".$rpp;
$result = mysqli_query($con,$query);


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
