<?php
$con=mysqli_connect("localhost","sils_rdm_reader","Een__Beest","sils_rdm");
if (mysqli_connect_errno()) { echo "Failed to connect to MySQL: " . mysqli_connect_error(); }

$clean_input=[];
$input_err=[];
$input_submit=0;
$nomail = array_key_exists('nomail',$_GET);


if (!empty($_POST))
{
    $email = filter_var($_POST["Email"],FILTER_SANITIZE_EMAIL);
    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
           $input_err["Email"]="contact email empty or invalid";
    } else {
           $clean_input["Email"] = $_POST["Email"];
    }
    if (($_POST["Year"] < 1900) || ($_POST["Year"] > 2040) )  { 
           $input_err["Year"]="invalid year";
    } else {  
           $clean_input["Year"] = $_POST["Year"];
    } 
    foreach ( [ "PreservationPeriod","Embargo","LastAuthor","ShortTitle","Confidentiality","Institutions","Name" ]  as $x)  {
       if (!$_POST[$x])  { 
            $input_err[$x]="Value required.";
       } else {
            $clean_input[$x] = mysqli_real_escape_string($con,$_POST[$x]);
       }
    }
    foreach ( [ "FirstAuthor","FundingAgencies" ]  as $x)  {
       if (!$_POST[$x])  { 
            $clean_input[$x]="-";
       } else {
            $clean_input[$x] = mysqli_real_escape_string($con,$_POST[$x]);
       }
    } 
    $clean_input["Group"] = mysqli_real_escape_string($con,$_POST["Group"]);
    $clean_input["Remarks"] =mysqli_real_escape_string($con,$_POST["Remarks"]);
    $id=$clean_input["Year"]."_".$clean_input['Group']."_".
        str_replace(" ",".",$clean_input['ShortTitle'])."_".
        str_replace(" ","",$clean_input['FirstAuthor'])."_".
        str_replace(" ","",$clean_input['LastAuthor']);
    $cleanid=str_replace("__","_",preg_replace(array('/[^a-zA-Z0-9_.]/', '/[.]+/', '/^-|-$/'), array('', '.', ''),remove_accent($id)));
     $ext_query="SELECT COUNT(*) AS cnt FROM Request WHERE ArchiveID = '$cleanid'"; 
     $result=mysqli_query($con,$ext_query);
     $row = $result->fetch_assoc();
     if ($row['cnt'] > 0) { 
          $input_err["GenerateID"] = "Request with id $cleanid submitted earlier.";
     } 

    if (count($input_err) == 0) {
          $ins_query="INSERT INTO Request (`Name`,`Email`,`Year`,`Group`,`ShortTitle`,`FirstAuthor`,`LastAuthor`,`Institutions`,`FundingAgencies`,`Confidentiality`,`Embargo`,`PreservationPeriod`,`Remarks`,`Status`,`CreationDate`,`ArchiveID`) VALUES ( '".
       $clean_input["Name"]."', '".
       $clean_input["Email"]."', '".
       $clean_input["Year"]."', '".
       $clean_input["Group"]."', '".
       $clean_input["ShortTitle"]."', '".
       $clean_input["FirstAuthor"]."', '".
       $clean_input["LastAuthor"]."', '".
       $clean_input["Institutions"]."', '".$clean_input["FundingAgencies"]."', '".$clean_input["Confidentiality"]."', '".$clean_input["Embargo"]."', '".$clean_input["PreservationPeriod"]."', '".$clean_input["Remarks"]."','Pending',NOW(),'$cleanid');";
        if (mysqli_query($con,$ins_query) === TRUE) { 
           $input_submit=1;
        } else { 
           $input_err["submission"]="Failed";
        } 
    } 
    if (($input_submit == 1) && !$nomail) { 
        $headers= 'From: rdm-archive.sils@uva.nl'."\r\n".'bcc: rdm-archive.sils@uva.nl'."\r\n";
        $mail_reply=            "SILS RDM archive request received. It has been assigned \r\n";
        $mail_reply=$mail_reply."id $cleanid. You will be\r\n";
        $mail_reply=$mail_reply."contacted soon to transfer the data to archive.\r\n";
        $mail_reply=$mail_reply."\r\n";
        $mail_reply=$mail_reply."The following information was recorded:\r\n";
        $mail_reply=$mail_reply."ID : $cleanid\r\n"; 
        foreach  ( [ "Name","Email","Year","Group","ShortTitle","FirstAuthor","LastAuthor","Institutions","FundingAgencies","Confidentiality","Embargo","PreservationPeriod","Remarks" ]  as $x) {
               $mail_reply=$mail_reply.$x." : ".$clean_input[$x]."\r\n";
        }
        if (!mail($clean_input["Email"],"SILS rdm request $cleanid",$mail_reply,$headers)) {
               echo "Sending mail to ...".$clean_input["Email"]." failed";
               $input_submit =0;
        } 
        echo "<p>Submission received.  </p>\n";
        echo "<p>A confirmation email is underway.  </p>\n";
    } 
}

function remove_accent($str)
{
  $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
  $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
  return str_replace($a, $b, $str);
}


function show_err() { 
   global $input_err,$input_submit,$clean_input;
    if (count($input_err) > 0) { 
         if (array_key_exists("GenerateID",$input_err)) {
             echo "<p>Request failed: ".$input_err['GenerateID']."</p>\n";
         } 
         else { 
             echo "<p>Request failed: check red field(s)</p>\n";
         } 
    } 
} 


function keep_posted($parameter,$default) {
   if (array_key_exists($parameter,$_POST)) 
        { return $_POST[$parameter]; }
   else { return $default; }
}

function inputnameval($parameter,$default,$size,$ml) {
   global $input_err;
   $rv = "<input size='$size' maxlength='$ml' ";
   if (array_key_exists($parameter,$input_err)) {
     $rv=$rv.' class="haserror" ';
   } 
   $rv=$rv."name='$parameter' value='";
   $rv=$rv.keep_posted($parameter,$default);
   $rv=$rv."'/>";
   if (array_key_exists($parameter,$input_err)) {
      $rv=$rv."<span class='err_msg'>$input_err[$parameter]</span>";
   }
   return $rv;
}


function form_table_input($label,$parameter,$default,$size,$ml) {
  return "<tr><td class='label'>$label</td><td>".inputnameval($parameter,$default,$size,$ml)."</td></tr>\n";
} 

?>
<?php if  ($input_submit != 1)  ?>
<!DOCTYPE html>
<html>
<head>
   <title>SILS RD Archival request form</title>
   <link rel="stylesheet" href="http://sils-tape.science.uva.nl/mainstyle.css" type="text/css">
</head>
<body class="form"> 
<?php show_err(); 
      $fh= "<form name='requestform' method='post'>\n".
      "<h2>Request for archiving to Sils Research Data Archive</h2>\n". 
      "<table>\n". 
      "<tr><td class='label'>Name of the study:</td>\n ". 
      "<td><textarea rows='3' cols='70'  name='Name'";
      if (array_key_exists("Name",$input_err)) { $fh=$fh.' class="haserror" '; }  
      $fh=$fh.">". 
      keep_posted("Name",""). 
      "</textarea> </td></tr>". 
      form_table_input("Contact (e-mail):","Email","",50,60).
      "<tr><td colspan =2><h3>Information for archive name:</h3></td></tr>\n".
      "<tr><td colspan =2><h4>Archive name will have the form: year_group_short.title_first.author_last.author</h4></td></tr>\n".
      form_table_input("Year:","Year","2017",4,4). 
      "<tr><td class='label'>Group:</td>\n".  
      "<td><select name='Group'>\n";
      if (array_key_exists("Group",$_POST)) { $s= $_POST["Group"];} else { $s= "UNKNOWN"; } 
      $result = mysqli_query($con,"SELECT Name FROM Groups");
      while($row = $result->fetch_assoc()) { 
          $n=$row["Name"];
          if ($s == $n) { 
            $fh=$fh."    <option selected value=".$n.">".$n."</option>\n"; 
          } else {
            $fh=$fh."    <option value=".$n.">".$n."</option>\n";  
          }
       }
        $fh=$fh."</select>\n".
        "</td></tr>\n".
        form_table_input("Short title:","ShortTitle","",24,32). 
        form_table_input("First author:","FirstAuthor","",24,32). 
        form_table_input("Last author or PI:","LastAuthor","",24,32). 
        "<tr><td colspan=2><h3>Other information</h3></td></tr>".
        form_table_input("Institutions:","Institutions","SILS - University of Amsterdam",50,50).
        form_table_input("Funding agencies:","FundingAgencies","",50,50). 
        form_table_input("Confidentiality:","Confidentiality","open",10,16). 
        form_table_input("Embargo:","Embargo","no",10,16). 
        form_table_input("Preservation period:","PreservationPeriod","10 years",10,16).
        "<tr><td class='label'>Remarks:</td> <td><textarea rows='20' cols='70'name='Remarks'>".
        keep_posted("Remarks","").
        "</textarea></td></tr>\n".
        "<tr><td></td><td><button name='Submit' value='request'>submit</button></td><tr>\n".
        "</table>\n</form>\n"; 
        if ($input_submit != 1)  {
              echo $fh;
        }
        ?>
</body>
</html>
<?php mysqli_close($con); ?>
