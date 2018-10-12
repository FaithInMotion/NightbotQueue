<?php

require(__DIR__."/config.php");

/*
 * Make sure all pieces from the query string are present
 */
if (empty($_GET["channel"]))
{
    echo "Paremeters missing";
    exit;
}

/*
 * Connect to the database
 */
try 
{
    $conn = new PDO("mysql:host=$servername;dbname=$datbaseName", $username, $password);
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
 * dealing with and how many names are in the queue
 *
 * Nightbot command:
 * !commands add !showqueue $(urlfetch http://queue.christinakline.com/list.php?channel=bowler)
 */
$channel = $_GET["channel"];
$rows = array();
$chosenUsers = array();
$chosenUserList = "";

/*
 * Grab the next amount of users from the database
 */
try
{
    $stmt = $conn->prepare("SELECT id, user FROM ".$channel." ORDER BY id ASC"); 
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
    echo "I'm having a brain fart - search for queued users failed!";
    exit;
}

/*
 * Build a list of names
 */
foreach ($rows as $row)
{
    $chosenUsers[] = $row["user"];
}

$chosenUserList = implode(", ", $chosenUsers);

if (strlen($chosenUserList) > 100)
{
    echo "Next up: ".substr($chosenUserList, 0, 100)."...";
}
else
{
    echo "Next up: ".$chosenUserList;
}

?>