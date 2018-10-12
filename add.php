<?php

require(__DIR__."/config.php");
require_once(__DIR__."/statuscontroller.php");

/*
 * Make sure all pieces from the query string are present
 */
 
if (empty($_GET["channel"]) || empty($_GET["user"]))
{
    echo "Paremeters missing";
    exit;
}

$channel = $_GET["channel"];
$user = $_GET["user"];

/*
 * Connect to the database
 */
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
 * Make sure the queue is not closed
 */
$statusController = new StatusController();
$queueStatus = $statusController->isQueueOpen($conn, $channel);

if ($queueStatus == StatusController::CLOSED)
{
    echo "The queue is currently closed. Please wait for the streamer to open it up.";
    exit;
}


/*
 * Now that we have a connection, work our magic
 *
 * Nightbot command:
 * !commands add !addme $(urlfetch http://queue.christinakline.com/add.php?channel=bowler&user=$(user))
 */

/*
 * First, check if the user is already in the table
 */
try
{
    $stmt = $conn->prepare("SELECT id FROM ".$channel." WHERE user = \"".$user."\""); 
    $stmt->execute();

    // set the resulting array to associative
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $rows = $stmt->fetchAll();

    if (count($rows) == 1)
    {
        echo "You are already in line - try again after you play!";
        exit;
    }
}
catch (PDOException $e)
{
    echo "I'm having a brain fart - search for ".$user." failed!";
    exit;
}

/*
 * If no user found, add them to the queue
 */
try
{
    $sql = "INSERT INTO ".$channel." (user)
    VALUES ('".$user."')";
    // use exec() because no results are returned
    $conn->exec($sql);
    echo $user." has put their quarter down.";
}
catch (PDOException $e)
{
    echo "I'm having a brain fart - could not add ".$user." to the queue";
    exit;
}

/*
 * Close the connection
 */ 
$conn = null;