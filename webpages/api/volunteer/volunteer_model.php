<?php

class VolunteerJob {

    public $id;
    public $name;
    public $isOnline;
    public $description;

    function __construct($id, $name, $is_online, $description) {
        $this->id = $id;
        $this->name = $name;
        $this->isOnline = $is_online;
        $this->description = $description;
    }

    public static function findAll($db) {
        $query = <<<EOD
        SELECT
                V.id,
                V.job_name,
                V.is_online,
                V.job_description
            FROM
                volunteer_job V;
        EOD;
        
        $stmt = mysqli_prepare($db, $query);
        $records = [];
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_object($result)) {
                $record = new VolunteerJob($row->id, $row->job_name, $row->is_online ? true : false, $row->job_description);
                $records[] = $record;
            }
            mysqli_stmt_close($stmt);
            return $records;
        } else {
            throw new DatabaseSqlException("Query could not be executed: $query");
        }
    }

    public static function fromJson($json) {
        $job = new VolunteerJob(null, $json["name"], $json["isOnline"], $json["description"]);
        return $job;
    }

    public static function persist($db, $volunteerJob) {
        $query = <<<EOD
        INSERT INTO volunteer_job
                (job_name, is_online, job_description)
         VALUES (?, ?, ?);
        EOD;
        
        $stmt = mysqli_prepare($db, $query);
        $isOnline = $volunteerJob->isOnline ? 1 : 0;
        mysqli_stmt_bind_param($stmt, "sis", $volunteerJob->name, $isOnline, $volunteerJob->description);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
        } else {
            throw new DatabaseSqlException("Query could not be executed: $query");
        }
    }

    function asArray() {
        return array("id" => $this->id, 
            "name" => $this->name, 
            "isOnline" => $this->isOnline, 
            "description" => $this->description);
    }
}
?>