<?php
$error = '';
$date = '';
$note='';

function clean_text($string)
{
  $string = trim($string);//vyjme white space z pravé a levé strany stringu
  $string = stripslashes($string);//vyjme lomítka ze stringu
  $string = htmlspecialchars($string);//Convert special characters to HTML entities
  return $string;
}
//poradil Marek
/*
if(isset($_GET["id"])) {

  echo $_GET["id"];
}*/


if (isset($_GET["ok"])){  
  //předám si číslo řádku na kterém se vyskytovalo poklikané tlačítko OK
  $SendedButtonID = $_GET["ButtonID"];  
  //vybereme řádek na který bylo kliknuto a uložíme ho do pole
  $data = get_data();
  $row = $data[$SendedButtonID-1];
  array_push($row,"OK");
  $data[$SendedButtonID-1]=$row;
  //uložíme celé pole opět do souboru
  $file_open = fopen("ToDoList_data.csv", "w");
  foreach($data as $row)
  {
    fputcsv($file_open, $row);
  }
  fclose($file_open);
  header('Location: index.php');
        exit;  
}

if(isset($_POST["submit"])) {
  print_r($_POST["submit"]);
  if(empty($_POST["date"])){
    $error .='<p><label class="text-danger">please enter date</label></p>';  
  } else {
    $date=clean_text($_POST["date"]);    
  }
  if(empty($_POST["note"])){
    $error .='<p><label class="text-danger">please enter note</label></p>';
  }else{
      $note=clean_text($_POST["note"]);      
  }
}

//ukládání zadaných hodnot do csv
$fp = file('ToDoList_data.csv');//uložím řádky do pole, aby je mohl jednoduše spočítat
  if($error =='' && $note != '' && $date != ''){
    $file_open = fopen("ToDoList_data.csv", "a");
    //create array - Each array element contains a line from the file   

    $form_data = array(
          'sr_no' =>  count($fp)+1,
          'date'  => $date,
          'note'  => $note,          
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
  $data = [];
  $row = 0;
    if (($handle = fopen("ToDoList_data.csv", "r")) !== FALSE) {
      while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) { //po dosažení 
        //print_r($row);
        $data[] = $row;
      }
      fclose($handle); 
      return $data;         
    }else
    return null;    
}

function set_tableRow()
{
  if(get_data()!=NULL){
    $dataT=get_data();
    //for ($i = 0; $i < count($dataT); $i++) {
    //  $row = $dataT[$i];
    //}
    $table_str = '';
    foreach ($dataT as $row) {
      $table_str.='<tr>';
        $table_str.='<th scope="row">' . $row[0]. '</th>';
        $table_str.='<td>' . $row[1] . '</td>';
        $table_str.='<td>' . $row[2] . '</td>';
        //NÁPOVĚDA OD MARKA - NEPOCHOPIL JSEM $table_str.='<td><a href="index.php ? id=' . $row[0] . '" class="btn btn-primary">OK</a></td>';
        //místo nápovědy od Marka si vložím formulář do části tabulky pro tlačítko OK
        
        //zkontroluji jestli je na konci řádku v csv souboru příznak OK
        if($row[(count($row)-1)] == 'OK'){            
            $table_str.='<td>'. $row[3]. '</td>';
            $table_str.='<td></td>';
            $table_str.='</tr>'; 
        }
        else{
          $table_str.='<td>aktualni</td>';
          $table_str.='<td><form method="get">';
          $buttonID = $row[0];
          $table_str.='<input type="hidden" name="ButtonID" value="'.$buttonID.'">';
          $table_str.='<input type="submit" name="ok"  class="btn btn-primary" value="ok">';
          $table_str.= '</form></td>';               
          $table_str.='</tr>'; 
        }               
    }
    return $table_str;      
  }
  else
    echo "žádná data k zobrazení";   
}
?>


<!doctype html>
<html class="no-js" lang="">
<head>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <title></title>
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="manifest" href="site.webmanifest"> 
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
</head>
<body>
      <!--??? otázka na Marka, jak zakomponovat externí php soubor<form action="obsluha.php" method="post">-->
      <form method="post">
      <h3 align="center">To do list</h3>
      <br/>
      <?php echo $error; ?>
      <div class = "form-group">
        <label>Enter date</label>        
        <input type="date" class="form-control" name="date" placeholder="enter date in format day-month-year"  >
      </div>    
      <div class = "form-group">
        <label>Enter note</label>
        <input type="note" class="form-control" name="note" placeholder="enter note" >
      </div>
      <div  align="center"><!--class="form-control"-->
        <input type="submit" class="btn btn-info" name="submit" value="save note"> <!--is the button that when clicked submits the form to the server for processing-->
      </div>      
    </form>

<!--https://blog.mounirmesselmeni.de/2012/11/20/reading-csv-file-with-javascript-and-html5-file-api/-->


    <table class="table">
      <thead class="thead-dark">
        <tr>
          <th scope="col">#</th>
          <th scope="col">date</th>
          <th scope="col">note</th>    
          <th scope="col">vyřešeno</th>        
        </tr>
      </thead>
      <tbody>
        <?php echo set_tableRow(); ?>       
      </tbody>
    </table>

  <script src="js/vendor/modernizr-3.6.0.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
  <script>window.jQuery || document.write('<script src="js/vendor/jquery-3.3.1.min.js"><\/script>')</script>
  <script src="js/plugins.js"></script>
  <script src="js/main.js"></script>  
</body>

</html>
