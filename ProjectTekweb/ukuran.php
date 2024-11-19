<?php
class Ukuran {
    private $conn;

    public $id_ukuran;
    public $ukuran;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Function to get ukuran by id
    public function getUkuran($id_ukuran) {
        $sql = "SELECT * FROM ukuran WHERE id_ukuran = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_ukuran);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>
