<?php
include 'config.php';
$con=connect_db();

$id = $_GET['id'];

$query="SELECT `Name`,`Email`,`Year`,`Group`,`ShortTitle`,`FirstAuthor`,".
       "`LastAuthor`,`Institutions`,`FundingAgencies`,`Confidentiality`,".
       "`Embargo`,`PreservationPeriod`,`Remarks`,`Status`,`CreationDate`,`ArchiveID`".
       " from Request where id=$id";

$result=mysqli_query($con,$query);
$request = $result->fetch_assoc();

function form_table_line($label,$value) {
  return "<tr><td class='label'>$label</td><td class='value'>$value</td></tr>\n";
} 

?>
<?php if  ($input_submit != 1)  ?>
<!DOCTYPE html>
<html>
<head>
   <title>SILS RD Archival form</title>
   <link rel="stylesheet" href="http://sils-tape.science.uva.nl/mainstyle.css" type="text/css">
</head>
<body class="form"> 
<?php
$fh= "<h2>Sils Research Data Archive</h2>\n". 
     "<table>\n". 
     form_table_line("Archive ID:",$request["ArchiveID"]).
     "<tr><td>&nbsp; </td></tr>".
     form_table_line("Name of the study:",$request["Name"]). 
     form_table_line("Contact (e-mail):",$request["Email"]).
     "<tr><td colspan =2><h3>Information for archive name:</h3></td></tr>\n".
     form_table_line("Year:",$request["Year"]). 
     form_table_line("Group:",$request["Group"]). 
     form_table_line("Short_Title:",$request["ShortTitle"]).
     form_table_line("First author:",$request["FirstAuthor"]). 
     form_table_line("Last author or PI:",$request["LastAuthor"]). 
     "<tr><td colspan=2><h3>Other information</h3></td></tr>".
     form_table_line("Institutions:",$request["Institutions"]).
     form_table_line("Funding agencies:",$request["FundingAgencies"]). 
     form_table_line("Confidentiality:",$request["Confidentiality"]).
     form_table_line("Embargo:",$request["Embargo"]). 
     form_table_line("Preservation period:",$request["PreservationPeriod"]).
     form_table_line("Remarks:",$request["Remarks"]).
     "</table>\n"; 
echo $fh; ?>
</body>
</html>
<?php mysqli_close($con); ?>
