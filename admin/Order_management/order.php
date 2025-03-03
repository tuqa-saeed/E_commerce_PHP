<?php
require_once '../../includes/database/config.php'; // Include your existing database connection

class Order
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllOrders()
    {
        $sql = "SELECT orders.id, users.name AS user_name, orders.total_price, orders.status, orders.created_at 
                FROM orders
                JOIN users ON orders.user_id = users.id
                ORDER BY orders.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
