<!-- DESCRIPTION /////////////////////////////////////////////////////////////////
  This page is the front-end that the user interacts with.  Ajax queries are called
via Javascript to the PHP back-end (query.php) in order to make SQL queries.  
These queries are output onto this page, where they are then parsed via Javascript
into useful information.
////////////////////////////////////////////////////////////////////////////// -->

<html>
<head>
  <link href='http://fonts.googleapis.com/css?family=Droid+Sans' rel='stylesheet' type='text/css'>
<title>
  Nematode (C. elegans) Connectome
</title>

<style>
  a:link{
    color: #00FFFF;
    text-decoration: none;
  }
  a:visited{
    color: #00FFFF;
    text-decoration: none;
  }
  a:active{
    color: #00FFFF;
    text-decoration: none;
  }
  a:hover{
    color: #FF0000;
    text-decoration: underline;
  }
</style>


<script>
// This AJAX code snippet submits the requests to query.php, which in turn uses PHP to submit
// SQL queries to the SQL database that holds the connectome data that was generated in R.
function sql_query(value){
  if (value == "") {
    document.getElementById("sql_Display").innerHTML = "";
    return;
  }
  if (window.XMLHttpRequest){
    xmlhttp = new XMLHttpRequest();
  }
  xmlhttp.onreadystatechange=function(){
    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
      document.getElementById("sql_Display").innerHTML = xmlhttp.responseText;
      sql_parse()
    }
  }
  xmlhttp.open("GET", "query.php?q="+value, true);
  xmlhttp.send();
}
</script>
</head>


<body style="background-color:#000000; font-family: 'Droid Sans', sans-serif;">

<!-- The "controls_background" div serves as the background for the text display and controls. -->
<div id="controls_background" style="position:absolute; left:0%; bottom:0%; height:20%; width:100%; color:#FFFFFF; background-color:rgba(60,60,60,1);"></div>


<!-- The "view" div contains the canvas elements. -->
<div id="view" style="position:absolute; left:0%; top:0%; border: 0px solid #666666; background-color:rgba(0,0,0,0);">
<!-- Canvas for the cell bodies (soma) and legend. -->
  <canvas id="canv" width="1000" height="300" onclick="get_canv_click()" style="position:absolute; top:0%; left:0%; background-color:rgba(0,0,0,0); z-index:3;"></canvas>
  <!-- Canvas for the cell projections (dendrites & axons; represented as single lines). -->
  <canvas id="canv_2" width="1000" height="300" onclick="" style="position:absolute; top:0%; left:0%; background-color:rgba(0,0,0,1); z-index:2;"></canvas>
</div>


<!-- The "choose" div is where the controls are stationed, which themselves are generated in the javascript below -->
<form id="choose_2" action='#' onsubmit="handle()">
<div id="choose" style="position:fixed; left:0.5%; top:80.5%; width:9%; border:0px solid #FF0000; background-color:#666666;">Neuron-ID#</div>
</form>

<div id="cell_name_1" style="position:fixed; left:0.5%; top:84%; width:9%; border:0px solid #FF0000; background-color:#666666;">Cell Name</div>

<div id="cell_location_1" style="position:fixed; left:0.5%; top:87.5%; width:9%; border:0px solid #FF0000; background-color:#666666;">Location</div>

<div id="cell_type_1" style="position:fixed; left:0.5%; top:91%; width:9%; border:0px solid #FF0000; background-color:#666666;">Cell Type</div>

<!-- [NYI] - Individual cell function. -->
<div id="cell_function_1" style="position:fixed; left:0.5%; top:94.5%; width:9%; border:0px solid #FF0000; background-color:#666666;">Function</div>

  
<!-- This is where the SQL tables from query.php are displayed. -->
<div id="sql_Display" style="position:fixed; left:0.5%; top:86%;"></div>


<!-- LEGEND -->
<div id="legend_2" style="position:absolute; right:1%; bottom:25%; height:10%; width:10%; font-size:70%; color:#888888; border: 0px solid #666666; background-color:rgba(255,255,255,0); z-index:3;">
  <font color="#FFFFFF">X-Z plane</font><hr>
  <font color="#00FF00">O</font> unknown neuron<br>
  <font color="#FF00AA">O</font> Sensory neuron<br>
  <font color="#FFAA00">O</font> Interneuron<br>
  <font color="#AA00FF">O</font> Motor neuron<br>
  <font color="#0000FF">__</font> Post-synaptic<br>
  <font color="#FFAA00">__</font> Pre-synaptic<br>
  <font color="#FF00FF">__</font> Gap junction
</div>


<!-- HELP menu -->
<div id="help" style="position:absolute; right:2%; top:0%; width:5%; font-size:70%; color:#000000; border: 0px solid #666666; text-align:center; background-color:rgba(0,255,155,1); z-index:3;" onclick="help_menu(1)">
  HELP
</div>

<div id="help_menu" style="position:absolute; left:20%; top:10%; width:0%; height:60%; background-color:#AAAAAA; z-index:5;"></div>

</body>
</html> 


<script>
// [NYI] - Help menu.
var help_ID = document.getElementById("help_menu")
function help_menu(value){
  if (value == 1){
    help_ID.style.width = "60%"
    help_ID.innerHTML = "\
    <a href='#' onclick='help_menu(0)' style='position:absolute; top:0%; right:0%; background:#555555;'>CLOSE</a>\
    HELP MENU NYI\
    <hr>\
    "
  } else{
    help_ID.style.width = "0%"
    help_ID.innerHTML = ""
  }
}

// Clears and re-draws the lowest level canvas ("canv_2") when a neuron is selected.  This is
// where the neural projections are drawn.
function handle(e){
        clear_screen()
        select_neuron()
        return false;
}

// This generates a drop down menu which would contain available neurons to select from.
// It is currently unused but may be used in the future.
var y = document.createElement("select")
    y.id = "vvv"
    y.style.position = "fixed"
    y.style.bottom = "20%"
    y.style.left = "1%"
    y.style.zIndex = 0
    document.getElementById("choose").appendChild(y)

    var y_1 = document.getElementById("vvv")
    var y_2 = document.createElement("option")
    y_2.text = "ADAL"
    y_1.add(y_2)

// This generates a number box that the user can input a number into which corresponds to 
// the ID of a neuron, which would then generate the information about that particular neuron.
var x = document.createElement("input")
    x.type = "number"
    x.id = "form_2"
    x.value = ""
    x.style.position = "fixed"
    x.style.top = "80.5%"
    x.style.left = "10%"
    x.style.width = "4%"
    x.style.border = "0px solid #222222"
    x.style.backgroundColor = "#666666"
    x.style.color = "#00FFFF"
    document.getElementById("choose").appendChild(x)
    
    x.value = y_2.index + 1 // because JS arrays start at "0" but the SQL IDs start at "1"
    

// This creates a button that is used to generate the neuron data from the value of the
// number box above.
var v = document.createElement("div")
    v.id = "but"
    v.style.position = "fixed"
    v.style.top = "80.5%"
    v.style.left = "14.5%"
    v.style.width = "5%"
    v.innerHTML = "SELECT"
    v.style.textAlign = "center"
    v.style.backgroundColor = "#666666"
    v.style.border = "0px solid #00FFFF"
    v.style.color = "#00FFFF"
    v.style.zIndex = "500"
    v.onclick = function() { clear_screen(); select_neuron() }
    document.getElementById("choose").appendChild(v)
    
// This function happens when the button above is clicked on.  
function select_neuron(){
  sql_query(x.value) // calls to the AJAX function in the header script to grab the SQL data
  neuron[x.value].cell_selected = 1 // identifies that the relevant neuron is now selected
  
  // creates a neuron object that will be drawn on the canvas
  neuron_struct(x.value,
                neuron[x.value].cell_selected,
                neuron[x.value].cell_x,
                neuron[x.value].cell_y)
}
</script>


<script>
// Clears the underlying canvas ("canv_2") which is used for drawing the projections.
function clear_screen(){
  ctx_2.clearRect(0,0,canv.width,canv.height)
}

var old_neuron = 1
var canvas_1 = document.getElementById("canv");
var canvas_2 = document.getElementById("canv_2");
canv.width = document.body.clientWidth;
canv.height = document.body.clientHeight * 0.80; // because the div only covers 80% of the height
canv_2.width = document.body.clientWidth;
canv_2.height = document.body.clientHeight * 0.80; // because the div only covers 80% of the height

var ctx_1 = canvas_1.getContext("2d");
var ctx_2 = canvas_2.getContext("2d");

// Draws neural projections where value_1 = pre-synaptic, and value_2 = post-synaptic.
function projection(value_1, value_2, color){
  ctx_2.beginPath();
  ctx_2.moveTo(neuron[value_1].cell_x, neuron[value_1].cell_y);
  ctx_2.lineTo(neuron[value_2].cell_x, neuron[value_2].cell_y);
  ctx_2.strokeStyle = color;
  ctx_2.stroke();
}

// The neuron cell data structure where id = cell id number in SQL database, 
// selected = determines if the cell is currently being viewed (selected = 1) or not (selected = 0),
// x_pos = horizontal position of cell body (soma) on the canvas ("canv"),
// y_pos = vertical position of the cell body (soma) on the canvas ("canv") (is actually z-axis)
// type_color = displays a different color per soma based on the neuron type (unknown, 
// sensory neuron, interneuron, motor neuron).
function neuron_struct(id, selected, x_pos, y_pos, type_color){
  this.cell_ID = id
  this.cell_selected = selected
  this.cell_x = x_pos
  this.cell_y = y_pos
  this.type_color = type_color

  if (selected == '0'){
    if (type_color == '0'){ // UNKNOWN (TBD) NEURON
      this.cell_draw = new draw_neuron(x_pos, y_pos, "#00FF00")
    }
    if (type_color == '1'){ // SENSORY NEURON
      this.cell_draw = new draw_neuron(x_pos, y_pos, "#FF00AA")
    }
    if (type_color == '2'){ // INTERNEURON
      this.cell_draw = new draw_neuron(x_pos, y_pos, "#FFAA00")
    }
    if (type_color == '3'){ // MOTOR NEURON
      this.cell_draw = new draw_neuron(x_pos, y_pos, "#AA00FF")
    }
  }
  
  if (selected == '1'){    
    if (old_neuron != x.value){
      // de-selects old neuron object
      draw_neuron(neuron[old_neuron].cell_x, neuron[old_neuron].cell_y, "#00FFFF")
    }
    
    // saves old neuron ID for future use
    old_neuron = x.value
    
    // deletes old neuron object
    draw_neuron(neuron[x.value].cell_x, neuron[x.value].cell_y, "#000000")
    
    // draws new neuron object
    draw_neuron(neuron[x.value].cell_x, neuron[x.value].cell_y, "#FFFF00")
    
    // highlights projections
    projection(x.value, 2, "#666666")
  }
}

// Draws the neurons onto the div element ("canv").
function draw_neuron(x, y, color){
  var cell_x = x
  var cell_y = y
  
  ctx_1.beginPath();
  ctx_1.strokeStyle = color;
  ctx_1.arc(cell_x, cell_y, 5, 0, 2*Math.PI);
  ctx_1.stroke();
}

var neuron = [] // Array that holds every neuron_struct(), where
// neuron_struct(ID#, active state, X, Y, type/color).  For example 
// neuron[1] = new neuron_struct(1, 0, 320, 200, "#FFAA00") // ADAL
// neuron[2] = new neuron_struct(2, 0, 320, 100, "#FFAA00") // ADAR

// [NYI] - Intended to help with selecting neurons by clicking on their cell bodies (soma) on
// the div element ("canv").
function get_canv_click(value1, value2){
}

// Draws the neuron cell bodies (soma) on to the div element ("canv").
var type_color = 0
function draw_neurons(value){
  var soma_pos_x = canv.width * (search_x * 0.01)
  soma_pos_x = soma_pos_x + (canv.width * 0.10) // Shifts the cell bodies over to the right for
  // easier visualization.

  var soma_pos_y = canv.height * (search_z * 0.01)
  
  neuron[value] = new neuron_struct(value, 0, soma_pos_x, soma_pos_y, type_color)
}

sql_query(1)
</script>


<script>
// These variables are used in the earlier function draw_neurons().  These variables hold the actual values of the neuron cell bodies (soma) which are then adjusted in the draw_neurons() function for better visualization.
var search_x = 0
var search_z = 0

function sql_parse(){
  // These are the strings for which the locations of the cell bodies are located.  They are
  // from the SQL queries and are here to be parsed via Javascript before they are used.
  var X_locations = document.getElementById("cell_locations").innerHTML
  var Z_locations = document.getElementById("cell_locations_z").innerHTML
  
  var string_of_cell_types = document.getElementById("cell_types_list").innerHTML

  // PARSES SOMA COORDINATES ////////////////////////////////////////////////////////////////
  for (var i = 1; i < neuron_names.length; i++){
    // X-COORDINATES ///////////////////////////////////////////////
      // triple digit X value (currently unused)
      var X_coord_triple_string = new RegExp(" " + i + "_" + "[0-9]" + "[0-9]" + "[^,]", 'g')
      var X_coord_triple_1 = X_locations.match(X_coord_triple_string)
      
      // double digit X value
      var X_coord_double_string = new RegExp(" " + i + "_" + "[0-9]" + "[^,]", 'g')
      var X_coord_double_1 = String(X_locations.match(X_coord_double_string))
      var X_coord_double_2 = new RegExp(" " + i + "_", 'g')
      var X_coord_double_3 = X_coord_double_1.match(X_coord_double_2)
      var X_coord_double = X_coord_double_1.replace(X_coord_double_3, "")
      
      // single digit X value
      var X_coord_single_string = new RegExp(" " + i + "_" + "[^,]", 'g')
      var X_coord_single_1 = String(X_locations.match(X_coord_single_string))
      var X_coord_single_2 = new RegExp(" " + i + "_", 'g')
      var X_coord_single_3 = X_coord_single_1.match(X_coord_single_2)
      var X_coord_single = X_coord_single_1.replace(X_coord_single_3, "")
      
      if (X_coord_triple_1 == null){
        if (X_coord_double_1 == null){
          if (X_coord_single_1 == null){
            // error
          } else {search_x = X_coord_single;}
        } else {search_x = X_coord_double;}
      } else {search_x = X_coord_double_1;}
    ////////////////////////////////////////////////////////////////
    
    
    // Z-COORDINATES ///////////////////////////////////////////////
      // triple digit Z value (currently unused)
      var Z_coord_triple_string = new RegExp(" " + i + "_" + "[0-9]" + "[0-9]" + "[^,]", 'g')
      var Z_coord_triple_1 = Z_locations.match(Z_coord_triple_string)
      
      // double digit Z value
      var Z_coord_double_string = new RegExp(" " + i + "_" + "[0-9]" + "[^,]", 'g')
      var Z_coord_double_1 = String(Z_locations.match(Z_coord_double_string))
      var Z_coord_double_2 = new RegExp(" " + i + "_", 'g')
      var Z_coord_double_3 = Z_coord_double_1.match(Z_coord_double_2)
      var Z_coord_double = Z_coord_double_1.replace(Z_coord_double_3, "")
      
      // single digit Z value
      var Z_coord_single_string = new RegExp(" " + i + "_" + "[^,]", 'g')
      var Z_coord_single_1 = String(Z_locations.match(Z_coord_single_string))
      var Z_coord_single_2 = new RegExp(" " + i + "_", 'g')
      var Z_coord_single_3 = Z_coord_single_1.match(Z_coord_single_2)
      var Z_coord_single = Z_coord_single_1.replace(Z_coord_single_3, "")
      
      if (Z_coord_triple_1 == null){
        if (Z_coord_double_1 == null){
          if (Z_coord_single_1 == null){
            // error
          } else {search_z = Z_coord_single;}
        } else {search_z = Z_coord_double;}
      } else {search_z = Z_coord_double_1;}
    ////////////////////////////////////////////////////////////////
    
    // GET CELL TYPE
    var cell_type_0 = i + "_" + string_of_cell_types.match(/\BTBD/i)
    var cell_type_1 = i + "_" + string_of_cell_types.match(/\Bsensory/i)
    var cell_type_2 = i + "_" + string_of_cell_types.match(/\Binterneuron/i)
    var cell_type_3 = i + "_" + string_of_cell_types.match(/\Bmotor/i)
    
    if (string_of_cell_types.match(cell_type_0)){ type_color = 0 }
    if (string_of_cell_types.match(cell_type_1)){ type_color = 1 }
    if (string_of_cell_types.match(cell_type_2)){ type_color = 2 }
    if (string_of_cell_types.match(cell_type_3)){ type_color = 3 }
    
      draw_neurons(i)
  }
  // Deletes the data so from this page so that it doesn't interfere with the display.
  document.getElementById("cell_locations").innerHTML = ""
  document.getElementById("cell_locations_z").innerHTML = ""
  document.getElementById("cell_types_list").innerHTML = ""
  ////////////////////////////////////////////////////////////////
  
  
  // PARSES PROJECTIONS ////////////////////////////////////////////////////////////////
  var string_of_projections = document.getElementById("out2").innerHTML
  var search_string_of_projections = ""
  
  for (var i = 1; i < neuron_names.length; i++){
    if (string_of_projections.match(neuron_names[i])){
      projection(x.value, i, "#666666")
      
      // Create projections for gap junctions.
      search_string_of_projections = neuron_names[i] + "_" + string_of_projections.match(/\Bgap/i)
      if (string_of_projections.match(search_string_of_projections)){
        projection(x.value, i, "#FF00FF")
      }
      
      // Create projections for inbound synapses.
      search_string_of_projections = neuron_names[i] + "_" + string_of_projections.match(/\Breceive/i)
      if (string_of_projections.match(search_string_of_projections)){
        projection(x.value, i, "#0000FF")
      }
      
      // Create projections for outbound synapses.
      search_string_of_projections = neuron_names[i] + "_" + string_of_projections.match(/\Bsend/i)
      if (string_of_projections.match(search_string_of_projections)){
        projection(x.value, i, "#FFAA00")
      }
    }
  }
  // Deletes the data so from this page so that it doesn't interfere with the display.
  document.getElementById("out2").innerHTML = ""
  ////////////////////////////////////////////////////////////////
}
</script>

<script>
// List of every neuron in the C. elegans hermaphrodite connectome.  This list is used to parse
// the SQL queries from the PHP back-end.
var list_of_neurons = ["","ADAL","ADAR","ADEL","ADER","ADFL","ADFR","ADLL","ADLR","AFDL","AFDR","AIAL","AIAR","AIBL","AIBR","AIML","AIMR","AINL","AINR","AIYL","AIYR","AIZL","AIZR","ALA","ALML","ALMR","ALNL","ALNR","AQR","AS1","AS2","AS3","AS4","AS5","AS6","AS7","AS8","AS9","AS10","AS11","ASEL","ASER","ASGL","ASGR","ASHL","ASHR","ASIL","ASIR","ASJL","ASJR","ASKL","ASKR","AUAL","AUAR","AVAL","AVAR","AVBL","AVBR","AVDL","AVDR","AVEL","AVER","AVFL","AVFR","AVG","AVHL","AVHR","AVJL","AVJR","AVKL","AVKR","AVL","AVM","AWAL","AWAR","AWBL","AWBR","AWCL","AWCR","BAGL","BAGR","BDUL","BDUR","CANL","CANR","CEPDL","CEPDR","CEPVL","CEPVR","DA1","DA2","DA3","DA4","DA5","DA6","DA7","DA8","DA9","DB1/3","DB2","DB3/1","DB4","DB5","DB6","DB7","DD1","DD2","DD3","DD4","DD5","DD6","DVA","DVB","DVC","FLPL","FLPR","HSNL","HSNR","I1L","I1R","I2L","I2R","I3","I4","I5","I6","IL1DL","IL1DR","IL1L","IL1R","IL1VL","IL1VR","IL2DL","IL2DR","IL2L","IL2R","IL2VL","IL2VR","LUAL","LUAR","M1","M2L","M2R","M3L","M3R","M4","M5","MCL","MCR","MI","NSML","NSMR","OLLL","OLLR","OLQDL","OLQDR","OLQVL","OLQVR","PDA","PDB","PDEL","PDER","PHAL","PHAR","PHBL","PHBR","PHCL","PHCR","PLML","PLMR","PLNL","PLNR","PQR","PVCL","PVCR","PVDL","PVDR","PVM","PVNL","PVNR","PVPL","PVPR","PVQL","PVQR","PVR","PVT","PVWL","PVWR","RIAL","RIAR","RIBL","RIBR","RICL","RICR","RID","RIFL","RIFR","RIGL","RIGR","RIH","RIML","RIMR","RIPL","RIPR","RIR","RIS","RIVL","RIVR","RMDDL","RMDDR","RMDL","RMDR","RMDVL","RMDVR","RMED","RMEL","RMER","RMEV","RMFL","RMFR","RMGL","RMGR","RMHL","RMHR","SAADL","SAADR","SAAVL","SAAVR","SABD","SABVL","SABVR","SDQL","SDQR","SIADL","SIADR","SIAVL","SIAVR","SIBDL","SIBDR","SIBVL","SIBVR","SMBDL","SMBDR","SMBVL","SMBVR","SMDDL","SMDDR","SMDVL","SMDVR","URADL","URADR","URAVL","URAVR","URBL","URBR","URXL","URXR","URYDL","URYDR","URYVL","URYVR","VA1","VA2","VA3","VA4","VA5","VA6","VA7","VA8","VA9","VA10","VA11","VA12","VB1","VB2","VB3","VB4","VB5","VB6","VB7","VB8","VB9","VB10","VB11","VC1","VC2","VC3","VC4","VC5","VC6","VD1","VD2","VD3","VD4","VD5","VD6","VD7","VD8","VD9","VD10","VD11","VD12","VD13"];

// Re-naming the list of neurons to adjust for list_of_neurons being 303 elements instead of 
// the 302 actual elements.  It goes back to the issue of the SQL neuron ID numbers starting at "1"
// instead of "0" as a JS array would.  As you can see in the list_of_neurons array, the first
// element is left blank, corresponding to no actual neuron existing in the SQL array for "0."
var neuron_names = []
for (var i = 1; i < 302; i++){
  neuron_names[i] = list_of_neurons[i]
}
</script>