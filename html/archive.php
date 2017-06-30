<!DOCTYPE html>
<html>
<head>
 <title>Sils Research Data Archive: content</title>
 <link rel="stylesheet" href="mainstyle.css" type="text/css">
</head>
<body>
<div class="container">
<h2> projects currently in the Sils Research Data Archive</h2>
<?php
$con=mysqli_connect("localhost","sils_rdm_reader","Een__Beest","sils_rdm");
if (mysqli_connect_errno()) {
   echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$result = mysqli_query($con,"select Name,Archive.Barcode,TapeLocation,ExternalID,ArchiveDate,`Group`,Tape.Class from Archive join Tape on Archive.Barcode = Tape.Barcode where Class='Primary' or Class='GroupCopy'");

echo "<table class='gridtable' width='100%'>";

$i = 0;
while($row = $result->fetch_assoc())
{
    if ($i == 0) {
      $i++;
      echo "<tr>";
      foreach ($row as $key => $value) {
        echo "<th>" . $key . "</th>";
      }
      echo "<th>form</th>";
      echo "</tr>";
    }
    echo "<tr>";
    foreach ($row as $value) {
      echo "<td>" . $value . "</td>";
    }
    $id = $row["Name"];
    echo "<td><a href=forms/".$id.".pdf><b>form</b></a></td>";
    echo "</tr>";
}
echo "</table>";

mysqli_close($con); ?>
</div>
</body>
</html>
