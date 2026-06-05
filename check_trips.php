<?php
$conn = new mysqli('localhost', 'root', '', 'lobibus');
if ($conn->connect_error) die('Connection failed: ' . $conn->connect_error);

$sql = "SELECT route_id, date(departure_time) as date, count(*) as count FROM trips GROUP BY route_id, date(departure_time) ORDER BY count DESC LIMIT 10";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "Route ID: " . $row["route_id"]. " - Date: " . $row["date"]. " - Count: " . $row["count"]. "\n";
    }
} else {
    echo "0 results";
}
$conn->close();
?>
