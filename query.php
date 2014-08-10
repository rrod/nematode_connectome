<?php
### DESCRIPTION ######################################################################
# This page is the PHP back-end that makes the actual SQL queries.  It is not
# intended to be viewed by users.  After the queries are made, the data is exported
# to div elements, where some are parsed via Javascript on the main page.
######################################################################################

$q = @intval($_GET['q']);

$sql_HOST = "???";
$sql_USER = "???";
$sql_PASSWORD = "???";

######################################################################
### QUERY 1 ##########################################################
function sql_1(){
  global $q, $sql_HOST, $sql_USER, $sql_PASSWORD;

  $sql_DATABASE = "???";

  $sql_DB = "???";
  $sql_TABLE = "???";

  $cell_name = '???';
  $cell_location_x = '???';
  $cell_location_z = '???';
  $cell_type = '???';
  $cell_function = '???';

  $con = mysqli_connect($sql_HOST, $sql_USER, $sql_PASSWORD, $sql_DATABASE);

  if (!$con) {
    die('Could not connect: ' . mysqli_error($con));
  }

  mysqli_select_db($con, $sql_DB);
  $sql = "SELECT * FROM " . $sql_DB . " WHERE " . $sql_TABLE . " = '" . $q . "'";
  $result = mysqli_query($con, $sql);

  # Outputs the left-side data (Cell Name, Cell Location, Cell Type, Cell Function) to main.php.
  echo "<div id='out1' style='position:fixed; left:20%; top:80.5%; width: 100%; background-color:#666666;'>";


  while($row = mysqli_fetch_array($result)){
    # CELL NAME ############################
    echo "<div id='cell_name_2' style='position:fixed; left:10%; top:84%; width:9.5%; border:0px solid #FF0000; background-color:#666666; color:#00FFFF;'>" . $row[$cell_name] . "</div>";
  
    # CELL LOCATION ############################
    echo "<div id='cell_location_2' style='position:fixed; left:10%; top:87.5%; width:9.5%; border:0px solid #FF0000; background-color:#666666; color:#00FFFF;'>" . $row[$cell_location_x] . ", " . $row[$cell_location_z] . "</div>";
  
    # CELL TYPE ############################
    echo "<div id='cell_type_2' style='position:fixed; left:10%; top:91%; width:9.5%; border:0px solid #FF0000; background-color:#666666; color:#00FFFF;'>" . $row[$cell_type] . "</div>";
  
    # CELL FUNCTION ############################
    echo "<div id='cell_function_2' style='position:fixed; left:10%; top:94.5%; width:9.5%; border:0px solid #FF0000; background-color:#666666; color:#00FFFF;'>" . $cell_function . "</div>";
  }


  $sql_1_2 = "SELECT * FROM " . $sql_DB;
  $result_1_2 = mysqli_query($con, $sql_1_2);

  echo "<div id='cell_locations' style='position:fixed; left:0%; bottom:0%; width:100%; background-color:#666666; color:#00FFFF;'> ";
  while($row = mysqli_fetch_array($result_1_2)){
    echo  $row[$sql_TABLE] . "_" . $row[$cell_location_x] . ", ";
  }
  echo "</div>";


  $sql_1_3 = "SELECT * FROM " . $sql_DB;
  $result_1_3 = mysqli_query($con, $sql_1_3);

  echo "<div id='cell_locations_z' style='position:fixed; left:0%; bottom:0%; width:100%; background-color:#666666; color:#00FFFF;'> ";
  while($row = mysqli_fetch_array($result_1_3)){
    echo  $row[$sql_TABLE] . "_" . $row[$cell_location_z] . ", ";
  }
  echo "</div>";


  $sql_1_4 = "SELECT * FROM " . $sql_DB;
  $result_1_4 = mysqli_query($con, $sql_1_4);

  echo "<div id='cell_types_list' style='position:fixed; left:0%; bottom:0%; width:100%; background-color:#666666; color:#00FFFF;'> ";
  while($row = mysqli_fetch_array($result_1_4)){
    echo $row[$sql_TABLE] . "_" . $row[$cell_type] . ", ";
  }
  echo "</div>";

  mysqli_close($con);
}
######################################################################
######################################################################


######################################################################
### QUERY 2 ##########################################################
function sql_2(){
  global $q, $sql_HOST, $sql_USER, $sql_PASSWORD;
  $sql_DATABASE_2 = "???";
  $cell_name_2 = '???';
  $cell_synapse_direction = '???';
  $cell_no = "???";

  $con = mysqli_connect($sql_HOST, $sql_USER, $sql_PASSWORD, $sql_DATABASE_2);

  if (!$con) {
    die('Could not connect: ' . mysqli_error($con));
  }
  
  $sql_cell = "";
  $sql_cell .= $cell_no . $q;
  
  mysqli_select_db($con, $sql_cell);
  $sql = "SELECT * FROM " . $sql_cell;
  $result = mysqli_query($con, $sql);

  # Gets the neuron projection type (input, output, gap junction).
  echo "<div id='out2' style='position:fixed; left:0%; bottom:0%; width: 100%; background-color:#666666;'>"; 
  while($row = mysqli_fetch_array($result)) {
    echo $row[$cell_name_2] . "_" . $row[$cell_synapse_direction] . ", ";
  }
  echo "</div>";
  
  
  # INPUT CONNECTIONS ############################
  echo "<div id='inputs' style='position:fixed; left:20%; top:80.5%; height:6%; width:80%; border:0px solid #FF0000; background-color:#666666; overflow-y: auto;'><b>Post-Synaptic:</b> ";
  $sql3 = "SELECT * FROM " . $sql_cell . " WHERE " . $cell_synapse_direction . " LIKE '%eceive%'";
  $result2 = mysqli_query($con, $sql3);
  while($row2 = mysqli_fetch_array($result2)){
    echo "<a href='' onclick='function(){ select_neuron() }'>" . $row2[$cell_name_2] . "</a>, ";
  }
  echo "</div>";
  
  
  # OUTPUT CONNECTIONS ############################
  echo "<div id='outputs' style='position:fixed; left:20%; top:87%; height:6%; overflow-y: auto; width:80%; border:0px solid #FF0000; background-color:#666666;'><b>Pre-Synaptic:</b> ";
  $sql4 = "SELECT * FROM " . $sql_cell . " WHERE " . $cell_synapse_direction . " LIKE '%en%'";
  $result3 = mysqli_query($con, $sql4);
  while($row3 = mysqli_fetch_array($result3)){
    echo "<a href=''>" . $row3[$cell_name_2] . "</a>, ";
  }
  echo "</div>";
  
  
  # GAP JUNCTIONS ############################
  echo "<div id='outputs' style='position:fixed; left:20%; top:93.5%; height:6%; overflow-y: auto; width:80%; border:0px solid #FF0000; background-color:#666666;'><b>Gap Junctions:</b> ";
  $sql5 = "SELECT * FROM " . $sql_cell . " WHERE " . $cell_synapse_direction . " LIKE '%unctio%'";
  $result4 = mysqli_query($con, $sql5);
  while($row4 = mysqli_fetch_array($result4)) {
    echo "<a href=''>" . $row4[$cell_name_2] . "</a>, ";
  }
  echo "</div>";

  mysqli_close($con);
}
######################################################################
######################################################################

# Ensures that no implausible queries are made.
if ($q >= 1 && $q <= 302){
  sql_1();
  sql_2();
}
?>

NOTICE:  Javascript is required for this site to function properly!

<script>
// This script re-directs the viewer to the main page, main.php.  If the viewer has Javascript
// disabled then they should still see a link available.
window.onload = function(){
  document.body.innerHTML = ""
  document.write("<a href='main.php'>Click here.</a>")
  window.location.replace("main.php")
}
</script>