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
        $db_name="coursesInfo_2";
        $tabName="courses";
        $profTabName="professors";
        $profCourseTabName="ProfCourses";
        
        include './functions/myFunctions.php';

        //establishing connection:
        $dbc=connectServer($db_url, $db_username, $db_password);
        selectDB($dbc, $db_name);

        /*$tableData=getTableData($dbc,$tabName);
                $_SESSION['tableData'] = $tableData;
        $profData=getTableData($dbc, $profTabName);
                $_SESSION['profData'] = $profData;
                mysqli_close($dbc);

                $mergeData = array_merge($tableData, $profData);*/
                $pdo = new PDO("mysql:host=$db_url;dbname=$db_name", $db_username, $db_password);
                $sqlMergeTables ="SELECT * FROM $tabName UNION SELECT profName FROM $profTabName";
                $mergeTablesResult = $pdo->query($sqlMergeTables);
                $mergeDate = $mergeTablesResult->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($mergeData)) {

                    // Display  data
                    echo '<table border="1">';
                    echo '<tr><th>Code</th><th>Title</th><th>Ctype</th><th>Dep</th><th>Semester</th><th>Prereq</th><th>Coption</th><th>NbOfCrdts</th><th>MaxNbOfStds</th><th>profName</th><th>profID</th></tr>';
                    foreach ($mergeData as $row) {
                        echo '<tr>';
                        echo '<td>' . $row['code'] . '</td>';
                        echo '<td>' . $row['title'] . '</td>';
                        echo '<td>' . $row['ctype'] . '</td>';
                        echo '<td>' . $row['dep'] . '</td>';
                        echo '<td>' . $row['semester'] . '</td>';
                        echo '<td>' . $row['prereq'] . '</td>';
                        echo '<td>' . $row['coption'] . '</td>';
                        echo '<td>' . $row['nbOfCrdts'] . '</td>';
                        echo '<td>' . $row['maxNbOfStds'] . '</td>';
                        echo '<td>' . $row['profID'] . '</td>';
                        echo '<td>' . $row['profName'] . '</td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                } else {
                    echo 'Session data not found.';
                }
        ?>
    </body>
</html>