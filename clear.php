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
 * dealing with
 *
 * Nightbot command:
 * !commands add !clearqueue $(urlfetch http://www.example.com/clear.php?channel=dsc)
 */
$channel = $_GET["channel"];

/*
 * Now clear the table
 */
try
{
    // sql to delete
    $sql = "DELETE FROM ".$channel;

    // use exec() because no results are returned
    $conn->exec($sql);
}
catch (PDOException $e)
{
    echo "I'm having a brain fart - failed to clear all users from the queue!";
    exit;
}

echo "All quarters have been returned and the queue is now empty!";