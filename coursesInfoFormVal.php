<!DOCTYPE html>
<body>
<?php 
$db_url="127.0.0.1";
$db_password="";
$db_username="root";
$db_name="coursesInfo_2";
$tabName="courses";
$profTabName="professors";
$profCourseTabName="ProfCourses";

include './functions/myFunctions.php';

//establishing connection:
$dbc=connectServer($db_url, $db_username, $db_password);
createDB($dbc, $db_name);
selectDB($dbc, $db_name);

//create table
$createTabQuery="CREATE TABLE IF NOT EXISTS $tabName (
    code VARCHAR(5) primary key,
    title VARCHAR(255),
    ctype VARCHAR(255),
    dep VARCHAR(255),
    semester INT,
    prereq VARCHAR(255),
    coption INT,
    nbOfCrdts INT,
    maxNbOfStds INT)";

//create prof table query
$createProfTabQuery="CREATE TABLE IF NOT EXISTS $profTabName (
    profID INT AUTO_INCREMENT PRIMARY KEY,
    profName VARCHAR(255))";

//create prof table query
$createProfCourseTabQuery="CREATE TABLE IF NOT EXISTS $profCourseTabName (
    profID INT,
    code VARCHAR(5),
    FOREIGN KEY (profID) REFERENCES $profTabName(profID),
    FOREIGN KEY (code) REFERENCES $tabName(code))";


//creating the tables
createTable($dbc, $db_name,$createTabQuery, $tabName);
createTable($dbc, $db_name, $createProfTabQuery, $profTabName);
createTable($dbc, $db_name, $createProfCourseTabQuery, $profCourseTabName);
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


    //first make sure that all the fields are entered
    if(!empty($_POST['code']) && !empty($_POST['title']) && !empty($_POST['prof']) &&
      !empty($_POST['nbOfCrdts']) && !empty($_POST['maxNbOfStds'])){

            //if code exists in the initial table, return a bool if true
            $codeExistsBool = false;
            $codeExistsQuery="SELECT * FROM $tabName WHERE code='$code'";
            $codeExistsResult=mysqli_query($dbc, $codeExistsQuery);
            if(mysqli_num_rows($codeExistsResult)){
               $codeExistsBool = true;
            } else {
                $insertCode = "INSERT INTO $tabName VALUES('$code', '$title', '$ctype', '$dep', '$semester', '$prereq', '$coption', '$nbOfCrdts', '$maxNbOfStds')";
                insertDataToTab($dbc,$tabName, $insertCode);
            }

            //check if prof exists in the prof table
            $profExistsBool = false;
            $profExistsQuery="SELECT * FROM $profTabName WHERE profName='$prof'";
            $profExistsResult=mysqli_query($dbc, $profExistsQuery);
            if(mysqli_num_rows($profExistsResult)){
               $profExistsBool = true;
            } else {
                $insertProf = "INSERT INTO $profTabName (profName) VALUES('$prof')";
                insertDataToTab($dbc,$profTabName, $insertProf);
            }

            //check if prof teaches the code
                $checkProfQuery = "SELECT profID FROM $profTabName WHERE profName='$prof'";
                $data = mysqli_query($dbc,$checkProfQuery) or die(mysqli_error($dbc));
                $profidResult = mysqli_fetch_row($data);
                $profID=$profidResult[0];

                $profidExistsResult1=mysqli_query($dbc,"SELECT profID FROM $profCourseTabName WHERE profID='$profID'");
                $codeExistsResult1= mysqli_query($dbc,"SELECT code FROM $tabName WHERE code='$code'");
                if($profExistsResult1 && $codeExistsResult1 && mysqli_num_rows($codeExistsResult1)>0 && mysqli_num_rows($profExistsResult1)>0){
                    header("location: ./coursesInfoFormShow.php", true);
                } else {
                    $insertProfidCode = "INSERT INTO $profCourseTabName VALUES('$profID', '$code')";
                    insertDataToTab($dbc,$profCourseTabName, $insertProfidCode);
                 header("location: ./coursesInfoFormShow.php", true);
                }
            //close connection
            mysqli_close($dbc);
    }

}
else {
    echo "<h2 style = 'color: red'> submission error </h2>";
}

?>
</body>