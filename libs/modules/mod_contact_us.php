<?php
class mod_contact_us
{
    public $clsDb;

    function __construct()
    {
        $this->clsDb = new clsDB();
    }

    function closeConnect()
    {
        $this->clsDb->close();
    }

    function GetContactUs()
    {
        $sql = "select * from contactus limit 1";

        return $this->clsDb->fetchAllArray($sql);
    }
}
?>