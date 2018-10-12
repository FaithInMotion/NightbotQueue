<?php

require_once(__DIR__."/config.php");
require_once(__DIR__."/statuscontroller.php");

/*
 * Make sure all pieces from the query string are present
 */
if (empty($_GET["channel"]) || empty($_GET["desired"]))
{
    echo "Paremeters missing";
    exit;
}

$channel = $_GET["channel"];
$desiredStatus = null;

switch($_GET["desired"])
{
    case "open":
        $desiredStatus = StatusController::OPEN;
        break;
    case "close":
        $desiredStatus = StatusController::CLOSED;
        break;
    default:
        echo "Parameter options were incorrect";
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
 * Now that we have a connection, we need to decide whether that channel is accepting queues or not
 *
 * Nightbot command:
 * !commands add !openqueue $(urlfetch http://queue.christinakline.com/status.php?channel=bowler&desired=open)
 * 
 * Find out if the queue was previously open or not (strictly for talking)
 */
$statusController = new StatusController();
$previousQueueStatus = $statusController->isQueueOpen($conn, $channel);

/*
 * Make sure we actually found a status
 */
if ($previousQueueStatus == StatusController::NOT_FOUND)
{
    echo "I'm having a brain fart - couldn't get previous channel status!";
    exit;
}

/*
 * Make sure we aren't already set up as desired
 */
if ($desiredStatus == $previousQueueStatus)
{
    echo "The Queue is already ".$desiredStatus;
    exit;
}

/*
 * Change the status
 */
$changeQueueStatusResult = $statusController->changeQueueStatus($conn, $channel);

/*
 * Close the connection
 */
$conn = null;

/*
 * Talk to the user accordingly
 */
if ($changeQueueStatusResult == StatusController::STATUS_NOT_CHANGED)
{
    echo "I'm having a brain fart - could not change queue status!";
    exit;
}

if ($previousQueueStatus == StatusController::CLOSED)
{
    echo "NOW ACCEPTING PLAYERS! Come put your quarter down with !addme";
    exit;
}
else
{
    echo "The queue is now CLOSED.";
    exit;
}