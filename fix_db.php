<?php

$conn = new mysqli('localhost', 'furniture_user', 'StrongPassword123!', 'furniture_db');
if ($conn->connect_error) {
    exit('Connection failed: '.$conn->connect_error);
}

$sql = "ALTER TABLE orders MODIFY COLUMN status VARCHAR(255) NOT NULL DEFAULT 'pending'";
if ($conn->query($sql) === true) {
    echo "Table altered successfully\n";
} else {
    echo 'Error altering table: '.$conn->error."\n";
}

$conn->close();
