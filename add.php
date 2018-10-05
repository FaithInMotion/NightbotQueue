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
 * dealing with and who the user is
 *
 * Nightbot command:
 * !commands add !addme $(urlfetch http://www.example.com/add.php?channel=TABLE_NAME&user=$(user))
 */
$channel = $_GET["channel"];
$user = $_GET["user"];

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
?>
