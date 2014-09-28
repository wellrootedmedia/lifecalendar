<?php
class Connect {
    protected $glob;
    private $_columnName;

    public function __construct() {
        global $wpdb;

        $this->glob = $wpdb;
    }

    public function wpdb() {
        return $this->glob;
    }

    public function getPrefix() {
        return $this->glob->prefix;
    }

    public function setColumnName( $columnName ) {
        $this->_columnName = $columnName;
    }

//    public function getColumnName() {
//        return $this->_columnName;
//    }

    public function getPostPrefix() {
        return $this->getPrefix() . $this->_columnName;
    }

//    private function getData() {
//        $query = "
//            SELECT * FROM ".$this->getPostPrefix()."
//            WHERE post_type = 'life_calendar_events'
//            AND post_status <> 'auto-draft'
//            ORDER BY post_date ASC
//        ";
//
//        return $this->glob->get_results($query, OBJECT);
//    }
//
//    public function getPosts() {
//        return $this->getData();
//    }
}