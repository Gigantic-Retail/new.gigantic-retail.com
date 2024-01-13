<?php
use Google\Cloud\SecretManager\V1\SecretManagerServiceClient;

// Create a Secret Manager client
$secrets = new SecretManagerServiceClient();

// Name of your secret
$secretName = ' projects/212055223570/secrets/customer-app-backend';

// Access the secret payload
$response = $secrets->accessSecretVersion(['name' => $secretName]);
$secretPayload = $response->getPayload()->getData();

// Decode the JSON payload (assuming JSON is used)
$secretsData = json_decode($secretPayload, true);

// Use the secret data in your application
$servername = $secretsData['DB_HOST'];
$username = $secretsData['DB_USER'];
$password = $secretsData['DB_PASS'];
$dbname = $secretsData['DB_NAME'];

// Now you can use these variables for your database connection
$conn = new mysqli($servername, $username, $password, $dbname);;

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Implement proper password hashing and validation here
    // For simplicity, let's assume plaintext passwords for this example

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        // Login successful
        // Redirect to another page or perform additional actions

        // Example: Redirect to dashboard.php
        header("Location: dashboard.php");
        exit();
    } else {
        // Login failed
        echo "Invalid username or password";
    }
}

$conn->close();
?>
