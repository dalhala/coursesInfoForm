<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600;700&display=swap" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200&display=swap');

        body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
}

h1, h2 {
    color: #333;
}

form {
    max-width: 600px;
    margin: 20px auto;
    padding: 20px;
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

label {
    display: block;
    margin-bottom: 8px;
}

input {
    width: 100%;
    padding: 8px;
    margin-bottom: 15px;
    box-sizing: border-box;
}

input[type="submit"] {
    background-color: #4caf50;
    color: #fff;
    cursor: pointer;
}

input[type="submit"]:hover {
    background-color: #45a049;
}

button{
    background-color: #4caf50;
    color: #fff;
    cursor: pointer;
    width: 100%;
    padding: 8px;
    margin-bottom: 15px;
    box-sizing: border-box;
}
button:hover {
    background-color: #45a049;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

table, th, td {
    border: 1px solid #ddd;
}

th, td {
    padding: 10px;
    text-align: left;
}

th {
    background-color: #4caf50;
    color: #fff;
}

</style>
</head>
    <body>
    <?php
    session_start();

    $db_url="127.0.0.1";
    $db_password="";
    $db_username="root";
    $db_name="coursesInfo_1";
    $tabName="courses";

    include './functions/DB_Functions.php';

    //connect to server
    function connectServer1($db_url,$db_username,$db_password){
        $dbc = @mysqli_connect($db_url,$db_username,$db_password)
        or die("connection error: " . @mysqli_errno($dbc) . ": " . @mysqli_errno($dbc));
        echo "<p> Successfully connected to database <p>";
        return $dbc;
    }
    

    //select db
    function selectDB1($dbc, $db_name) {
	mysqli_select_db($dbc ,$db_name)
	 or die ('<p style="color: red;">'.
			 "Could not select the database ".$db_name.
			 "because:<br/>".mysqli_error($dbc).
			 ".</p>");
	
	 echo "<p>The database $db_name has been selected.</p>";
    }

    //create table
    $createTabQuery="CREATE TABLE IF NOT EXISTS $tabName (
                     code VARCHAR(5) primary key,
                     title VARCHAR(255),
                     ctype VARCHAR(255),
                     dep VARCHAR(255),
                     prof VARCHAR(255),
                     semester VARCHAR(255),
                     prereq VARCHAR(255),
                     coption VARCHAR(255),
                     nbOfCrdts INT(2),
                     maxNbOfStds INT)";

    

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

    //establishing connection:
    $dbc=connectServer1($db_url, $db_username, $db_password);

    //checking if the database exists or not
    $selectResult=mysqli_select_db($dbc,$db_name);
    if($selectResult){
        createTable($dbc,$createTabQuery,$tabName);
    }
    else{
        createDB($dbc,$db_name);
        selectDB1($dbc,$db_name);

        //checks if table already exists in the database
        $tableExistsQuery="SHOW TABLES LIKE $tabName";
        $tableExistsResult=mysqli_query($dbc,$tableExistsQuery);
        if(mysqli_num_rows($tableExistsResult)>0);
        else{
            createTable($dbc,$createTabQuery,$tabName);
        }
    }
   
    //insert infos via form
    if($_SERVER["REQUEST_METHOD"] == "POST"){

        $code=$_POST['code'];
        $title=$_POST['title'];
        $ctype=$_POST['ctype'];
        $dep=$_POST['dep'];
        $prof=$_POST['prof'];
        $semester=$_POST['semester'];
        $prereq=$_POST['prereq'];
        $coption=$_POST['coption'];
        $nbOfCrdts=$_POST['nbOfCrdts'];
        $maxNbOfStds=$_POST['maxNbOfStds'];

        //checks if the infos of a certain course already exists
        $codeExistsQuery="SELECT * FROM $tabName WHERE code='$code' AND prof='$prof'";
        $codeExistsResult=mysqli_query($dbc, $codeExistsQuery);
        if(mysqli_num_rows($codeExistsResult)){
            print ("<h2 style = 'color: orange'> $code already exists taught by $prof </h2>");	
        } else {
             //checks if the form fields are not empty
             if(!empty($_POST['code']) && !empty($_POST['title']) && !empty($_POST['prof']) &&
              !empty($_POST['nbOfCrdts']) && !empty($_POST['maxNbOfStds'])){
                $insertFromFrom = "INSERT INTO $tabName VALUES('$code', '$title', '$ctype', '$dep', '$prof', '$semester', '$prereq', '$coption', '$nbOfCrdts', '$maxNbOfStds')";
                insertDataToTab($dbc,$tabName, $insertFromFrom);
                }

        }
        $tableData=getTableData($dbc,$tabName);
                $_SESSION['tableData'] = $tableData;
                mysqli_close($dbc);
    }
    else {
        echo "submition error";
    }
    if (!empty($_SESSION['tableData'])) {
        $tableData = $_SESSION['tableData'];

        // Display table data
        echo '<table border="1">';
        echo '<tr><th>Code</th><th>Title</th><th>Ctype</th><th>Dep</th><th>Prof</th><th>Semester</th><th>Prereq</th><th>Coption</th><th>NbOfCrdts</th><th>MaxNbOfStds</th></tr>';
        foreach ($tableData as $row) {
            echo '<tr>';
            echo '<td>' . $row['code'] . '</td>';
            echo '<td>' . $row['title'] . '</td>';
            echo '<td>' . $row['ctype'] . '</td>';
            echo '<td>' . $row['dep'] . '</td>';
            echo '<td>' . $row['prof'] . '</td>';
            echo '<td>' . $row['semester'] . '</td>';
            echo '<td>' . $row['prereq'] . '</td>';
            echo '<td>' . $row['coption'] . '</td>';
            echo '<td>' . $row['nbOfCrdts'] . '</td>';
            echo '<td>' . $row['maxNbOfStds'] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo 'Session data not found.';
    }
    
   
  ?>
  <script type=""></script> 
  <button action='goBack()'>
                <span>go back</span>
            </button>
    </body>
</html>