<?php 

 //connect to server
 function connectServer($db_url,$db_username,$db_password){
    $dbc = @mysqli_connect($db_url,$db_username,$db_password)
    or die("connection error: " . @mysqli_errno($dbc) . ": " . @mysqli_errno($dbc));
    return $dbc;
}

function createDB($dbc,$db)
{
	$query= "CREATE DATABASE IF NOT EXISTS ".$db;
	mysqli_query($dbc,$query) 
	 or die('<p style="color: red;">'.
	        "Could not create the database ".
			$db." because:<br>".mysqli_error($dbc).
			".</p>");
}

//select db
function selectDB($dbc, $db_name) {
mysqli_select_db($dbc ,$db_name)
 or die ('<p style="color: red;">'.
         "Could not select the database ".$db_name.
         "because:<br/>".mysqli_error($dbc).
         ".</p>");
}

//checking if the database exists or not and create table
function createTabVal($dbc, $db_name, $tabName, $createTabQuery){
    $selectResult=mysqli_select_db($dbc,$db_name);
    if($selectResult){
        createTable($dbc,$createTabQuery,$tabName);
        
    }
    else{
        createDB($dbc,$db_name);
        selectDB($dbc,$db_name);
        createTable($dbc,$createTabQuery,$tabName);
    }
    }

//fetch data from table
function getTableData($dbc, $tableName) {
    $result = mysqli_query($dbc, "SELECT * FROM $tableName");

    if ($result) {
        $tableData = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $tableData[] = $row;
        }
        mysqli_free_result($result);
        return $tableData;
    } else {
        die('Error: ' . mysqli_error($dbc));
    }
}

function insertDataToTab($dbc, $Tab, $query)
{
    @mysqli_query($dbc,$query) 
      or die ("DB Error: Could not insert $Tab! <br>".
			  @mysqli_error($dbc));
   
    print ("<h2 style = 'color: blue'> The data was added successfully! </h2>");	
}
function createTable($dbc,$db,$query,$Tab)
{
	selectDB($dbc, $db); 
	// Execute the query:
	if (!@mysqli_query($dbc,$query))
	{
	
		$str='<p style="color: red;">';
		$str.="Could not create the table $Tab because:<br>";
		$str.=mysqli_error($dbc);
		$str.=".</p><p>The query being run was:".$query."</p>";
		print $str;		    
	}
}
?>