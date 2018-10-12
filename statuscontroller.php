<?php

class StatusController
{
    const CLOSED = "CLOSED";
    const OPEN = "OPEN";

    const STATUS_CHANGED = "STATUS_CHANGED";
    const STATUS_NOT_CHANGED = "STATUS_NOT_CHANGED";

    const NOT_FOUND = "NOT_FOUND";


    /**
     * Function for other endpoints to determine if queue is even open
     *
     * @param $conn PDO
     * @param $channel string
     * @return string
     */
    public function isQueueOpen($conn, $channel)
    {
        $row = null;

        /*
         * Grab the next amount of users from the database
         */
        try
        {
            $stmt = $conn->prepare("SELECT id, channel_name, is_open FROM channel_statuses WHERE channel_name = \"".$channel."\"");
            $stmt->execute();

            // set the resulting array to associative
            $result = $stmt->setFetchMode(PDO::FETCH_OBJ);
            $row = $stmt->fetch();

            if (empty($row->is_open) != 1)
            {
                return self::NOT_FOUND;
            }
        }
        catch (PDOException $e)
        {
            return self::NOT_FOUND;
        }

        /*
         * Return the status of our 1 row
         */
        if ($row->is_open)
        {
            return self::OPEN;
        }
        else
        {
            return self::CLOSED;
        }
    }

    public function changeQueueStatus($conn, $channel)
    {
        /*
         * Set the status
         */
        try
        {
            $sql = "UPDATE channel_statuses
SET is_open = NOT is_open
WHERE channel_name = \"".$channel."\"";

            // use exec() because no results are returned
            $result = $conn->exec($sql);

            if ($result == 1)
            {
                return self::STATUS_CHANGED;
            }
            return self::STATUS_NOT_CHANGED;
        }
        catch (PDOException $e)
        {
            return self::STATUS_NOT_CHANGED;
        }
    }
}