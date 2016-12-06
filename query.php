<!--
////////////////////////////////////////////////////////////////////////////////
DESCRIPTION
This page is the PHP back-end that makes the actual SQL queries.  It is not
intended to be viewed by users.  After the queries are made, the data is
exported to div elements, where some some are parsed via Javascript on the main
page.
////////////////////////////////////////////////////////////////////////////////
-->

<?php
$q = intval($_GET['q']);

// Redirect the user to main.php just in case they are trying query.php
if ($q == "undefined"){
  echo "
    <script>
      window.location = 'main.php';
    </script>
  ";
}

// Database that contains the connectome information
$sql_HOST = "";
$sql_USER = "";
$sql_PASSWORD = "";
$sql_DATABASE = "";

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
// QUERY 1
$con = mysqli_connect($sql_HOST, $sql_USER, $sql_PASSWORD, $sql_DATABASE);

if (!$con) {
  die('Could not connect: ' . mysqli_error($con));
}

$sql_DB = "nematode";
$sql_TABLE = "row_names";
mysqli_select_db($con,$sql_DB);
$sql = "SELECT * FROM " . $sql_DB . " WHERE " . $sql_TABLE . " = '" . $q . "'";
$result = mysqli_query($con,$sql);

echo "<div id='out1' class='out1'>";

while($row = mysqli_fetch_array($result)) {

  // CELL NAME /////////////////////////////////////////////////////////////////
  echo "
    <div id='cell_name_2' class='cell_name_2'>
    " . $row['neuron_list3'] . "
    </div>"
  ;

  // CELL LOCATION /////////////////////////////////////////////////////////////
  echo "
    <div id='cell_location_2' class='cell_location_2'>
    " . $row['LOCATION_X'] . ", " . $row['LOCATION_Z'] . "
    </div>"
  ;

  // CELL TYPE /////////////////////////////////////////////////////////////////
  echo "
    <div id='cell_type_2' class='cell_type_2'>
    " . $row['desc_list2'] . "
    </div>"
  ;

  // CELL FUNCTION /////////////////////////////////////////////////////////////
  echo "<div id='cell_function_2' class='cell_function_2'>???</div>";
}


$sql_1_2 = "SELECT * FROM nematode";
$result_1_2 = mysqli_query($con,$sql_1_2);

echo "<div id='cell_locations' class='cell_locations'> ";
while($row = mysqli_fetch_array($result_1_2)){
  echo  $row['row_names'] . "_" . $row['LOCATION_X'] . ", ";
}
echo "</div>";


$sql_1_3 = "SELECT * FROM nematode";
$result_1_3 = mysqli_query($con,$sql_1_3);

echo "<div id='cell_locations_z' class='cell_locations_z'> ";
while($row = mysqli_fetch_array($result_1_3)){
  echo  $row['row_names'] . "_" . $row['LOCATION_Z'] . ", ";
}
echo "</div>";


$sql_1_4 = "SELECT * FROM nematode";
$result_1_4 = mysqli_query($con,$sql_1_4);

echo "<div id='cell_types_list' class='cell_types_list'> ";
while($row = mysqli_fetch_array($result_1_4)){
  echo $row['row_names'] . "_" . $row['desc_list2'] . ", ";
}
echo "</div>";

mysqli_close($con);
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////


////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
// QUERY 2
// This function queries the database then pushes out the connectome information
// into HTML/CSS that is displayed on main.php
function sql_2(){
  global $q, $sql_HOST, $sql_USER;
  $con = mysqli_connect($sql_HOST,$sql_USER,"","nematode_2");

  if (!$con) {
    die('Could not connect: ' . mysqli_error($con));
  }

  $a = "";
  $a .= "nematode_n" . $q;

  mysqli_select_db($con,$a);
  $sql = "SELECT * FROM " . $a;
  $result = mysqli_query($con,$sql);

  // If this is disabled then no connections are drawn AND the list of
  // connections will appear.
  // It won't show the inputs / outputs divs because they are wrapped up in
  // the out2 div.
  echo "<div id='out2' class='out2' >";

  while($row = mysqli_fetch_array($result)){
    // necessary for showing the connectome's colors
    echo $row['NEURON'] . "_" . $row['SYNAPSE'] . ", ";
  }
  echo "</div>";


  // INPUT CONNECTIONS /////////////////////////////////////////////////////////
  echo "<div id='inputs' class='post_synaptic' >
    <b>Post-Synaptic:</b> ";
  $sql3 = "SELECT * FROM " . $a . " WHERE SYNAPSE LIKE '%eceive%'";
  $result2 = mysqli_query($con,$sql3);
  while($row2 = mysqli_fetch_array($result2)) {
    echo "<span onclick='x.value = " . $row2['row_names'] . "; clear_screen();
      select_neuron();' style='color: #fff;'>" . $row2['NEURON'] . "</span>, ";
  }
  echo "</div>";


  // OUTPUT CONNECTIONS ////////////////////////////////////////////////////////
  echo "<div id='outputs' class='pre_synaptic' >
    <b>Pre-Synaptic <font color='#d90'>(Synapses):</b></font> ";
  $sql4 = "SELECT * FROM " . $a . " WHERE SYNAPSE LIKE '%en%'";
  $result3 = mysqli_query($con,$sql4);
  while($row3 = mysqli_fetch_array($result3)) {
    echo "<span onclick='x.value = " . $row3['row_names'] . "; clear_screen();
      select_neuron();' style='color: #fff;'>" . $row3['NEURON'] . "</span>, ";
  }
  echo "</div>";


  // GAP JUNCTIONS /////////////////////////////////////////////////////////////
  echo "<div id='outputs' class='gap_junction' >
    <b>Gap Junctions <font color='#808'>(Synapses):</b></font> ";
  $sql5 = "SELECT * FROM " . $a . " WHERE SYNAPSE LIKE '%unctio%'";
  $result4 = mysqli_query($con,$sql5);
  while($row4 = mysqli_fetch_array($result4)) {
    echo "<span onclick='x.value = " . $row4['row_names'] . "; clear_screen();
      select_neuron();' style='color: #fff;'>" . $row4['NEURON'] . "</span>, ";
  }
  echo "</div>";

  mysqli_close($con);
}
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

sql_2();
?>