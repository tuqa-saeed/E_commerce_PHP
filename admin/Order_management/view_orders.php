<?php
include '../../includes/database/config.php';
// Handle updating order status
if (isset($_POST['update_status']) && isset($_POST['order_id']) && isset($_POST['status'])) {
  $order_id = $_POST['order_id'];
  $new_status = $_POST['status'];

  $update_sql = "UPDATE orders SET status = :status WHERE id = :order_id";
  $stmt = $pdo->prepare($update_sql);
  $stmt->bindParam(':status', $new_status, PDO::PARAM_STR);
  $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
  $stmt->execute();

  header("Location: view_orders.php");
  exit;
}

// Handle filtering orders by status
$status_filter = '';
if (isset($_GET['status']) && in_array($_GET['status'], ['pending', 'processing', 'completed', 'cancelled'])) {
  $status_filter = $_GET['status'];
}

// SQL query to fetch orders with optional status filter
$sql = "SELECT orders.id, users.name AS user_name, orders.total_price, orders.status, orders.created_at 
        FROM orders
        JOIN users ON orders.user_id = users.id";

if ($status_filter) {
  $sql .= " WHERE orders.status = :status";
}

$sql .= " ORDER BY orders.created_at DESC";

$stmt = $pdo->prepare($sql);

if ($status_filter) {
  $stmt->bindParam(':status', $status_filter, PDO::PARAM_STR);
}

$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Orders</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
  <div class="container mt-5">
    <h1>View Orders</h1>

    <!-- Filter Form -->
    <form method="GET" action="view_orders.php" class="mb-4">
      <select name="status" class="form-control w-25 d-inline-block">
        <option value="">All Statuses</option>
        <option value="pending" <?php echo (isset($_GET['status']) && $_GET['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
        <option value="processing" <?php echo (isset($_GET['status']) && $_GET['status'] == 'processing') ? 'selected' : ''; ?>>Processing</option>
        <option value="completed" <?php echo (isset($_GET['status']) && $_GET['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
        <option value="cancelled" <?php echo (isset($_GET['status']) && $_GET['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
      </select>
      <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <table class="table table-bordered">
      <thead>
        <tr>
          <th>#</th>
          <th>User Name</th>
          <th>Total Price</th>
          <th>Status</th>
          <th>Date</th>
          <th>Update Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $order): ?>
          <tr>
            <td><?php echo $order['id']; ?></td>
            <td><?php echo $order['user_name']; ?></td>
            <td><?php echo '$' . number_format($order['total_price'], 2); ?></td>
            <td><?php echo ucfirst($order['status']); ?></td>
            <td><?php echo date('Y-m-d H:i:s', strtotime($order['created_at'])); ?></td>
            <td>
              <form method="POST" action="view_orders.php">
                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                <select name="status" class="form-control">
                  <option value="pending" <?php echo ($order['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                  <option value="processing" <?php echo ($order['status'] == 'processing') ? 'selected' : ''; ?>>Processing</option>
                  <option value="completed" <?php echo ($order['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                  <option value="cancelled" <?php echo ($order['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                </select>
                <button type="submit" name="update_status" class="btn btn-success mt-2">Update</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>


</body>

</html>