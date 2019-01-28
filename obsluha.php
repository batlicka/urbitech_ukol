<?php
$error = '';
$date = '';
$note='';
$data = [];
function clean_text($string)
{
  $string = trim($string);//vyjme white space z pravé a levé strany stringu
  $string = stripslashes($string);//vyjme lomítka ze stringu
  $string = htmlspecialchars($string);//Convert special characters to HTML entities
  return $string;
}

if(isset($_GET["id"])) {

  echo $_GET["id"];
}

if(isset($_POST["submit"])) {
  if(empty($_POST["date"])) {
    $error .='<p><label class="text-danger">please enter date</label></p>';
  
  } else {
    $date=clean_text($_POST["date"]);    
  }
  if(empty($_POST["note"]))
  {
    $error .='<p><label class="text-danger">please enter note</label></p>';
  }
  else
  {
      $note=clean_text($_POST["note"]);      
  }
}

  if($error =='' && $note != '' && $date != '')
  {
    $file_open = fopen("contact_data.csv", "a");
    $no_rows = count(file("contact_data.csv"));//file() create array - Each array element contains a line from the file
        
        $form_data = array(
          'sr_no' => $no_rows+1,
          'date'  => $date,
          'note'  => $note
        );
        fputcsv($file_open, $form_data);
        $date = '';
        $note = '';

        header('Location: index.php');
        exit;
  }

//reading from csv
function get_data()
{
  $row = 0;
    if (($handle = fopen("contact_data.csv", "r")) !== FALSE) {
      while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) { //po dosažení 
        print_r($row);
        $data[] = $row;
      }
      fclose($handle);          
    }
    return $data;
}

function get_tableRow()
{
  $dataT=get_data();
    //for ($i = 0; $i < count($dataT); $i++) {
    //  $row = $dataT[$i];
    //}
  $table_str = '';
    foreach ($dataT as $row) {
      $table_str.='<tr>';
        $table_str.='<th scope="row">1</th>';
        $table_str.='<td>' . $row[1] . '</td>';
        $table_str.='<td>' . $row[2] . '</td>';
        $table_str.='<td><a href="index.php ? id=' . $row[0] . '" class="btn btn-primary">OK</a></td>';
        $table_str.='</tr>'; 
    }
    return $table_str;  
}
?>