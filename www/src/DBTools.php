<?php

namespace SearchTools;

class DBTools
{
    /** @var ADOConnection */
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function cleanInput($data) {
        foreach ($data as $key => $value) {
            $data[$key] =  preg_replace('/[\x00-\x1F\x80-\xFF]/', '',
                str_replace("\n", " ", $value)
            );
        }

        return $data;
    }

    public function upsert($table, $data, $field = "id") {
        $sql = "select * from " . $table . " where ".$field."='".$data[$field]."'";
        $result = $this->db->execute($sql);
        if ($result->fetchRow()) {
            $this->db->autoExecute($table, $data, 'UPDATE', $field . " = '".$data[$field]."'");
        } else {
            $this->db->autoExecute($table,$data,'INSERT');
        }
    }

}