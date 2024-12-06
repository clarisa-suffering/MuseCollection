<?php
class Ukuran {
    private $conn;

    public $id_ukuran;
    public $ukuran;

    public function __construct($conn) {
        $this->conn = $conn;
    }
}
?>
