<?php

require(__DIR__."/config.php");

try 
{
    $conn = new PDO("mysql:host=$server;dbname=$datbaseName", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e)
{
    echo "Make me - your developer sucks!";
    exit;
}

/*
 * Now that we have a connection, we need to determine which channel we are 
 * dealing with and how many names we want
 *
 * Nightbot command:
 * !commands add !nextup $(urlfetch http://www.example.com/next.php?channel=TABLE_NAME&count=$(query))
 */
$channel = $_GET["channel"];
$count = $_GET["count"];
$rows = array();
$ids = array();
$idList = "";
$chosenUsers = array();
$chosenUserList = "";

/*
 * Grab the next amount of users from the database
 */
try
{
    $stmt = $conn->prepare("SELECT id, user FROM ".$channel." ORDER BY id ASC LIMIT ".$count); 
    $stmt->execute();

    // set the resulting array to associative
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $rows = $stmt->fetchAll();

    if (count($rows) == 0)
    {
        echo "There are no people in the queue right now...";
        exit;
    }
}
catch (PDOException $e)
{
    echo "I'm having a brain fart - search for ".$count." users failed!";
    exit;
}

/*
 * Build a list of ids
 */
foreach ($rows as $row)
{
    $ids[] = $row["id"];
    $chosenUsers[] = $row["user"];
}

$idList = implode(",", $ids);
$chosenUserList = implode(", ", $chosenUsers);

/*
 * Now remove the names that you found from the queue
 */
try
{
    // sql to delete a record
    $sql = "DELETE FROM ".$channel." WHERE id IN (".$idList.")";

    // use exec() because no results are returned
    $conn->exec($sql);
}
catch (PDOException $e)
{
    echo "I'm having a brain fart - deletion of ".$count." users failed!";
    exit;
}

echo $chosenUserList." - you're up next!";

?>
