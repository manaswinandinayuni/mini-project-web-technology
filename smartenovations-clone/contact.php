<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "smartenovations_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create contacts table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS contacts (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200),
    message TEXT NOT NULL,
    submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === FALSE) {
    die("Error creating table: " . $conn->error);
}

// Process form submission
$success = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $subject = isset($_POST['subject']) ? $conn->real_escape_string($_POST['subject']) : '';
    $message = $conn->real_escape_string($_POST['message']);
    
    $sql = "INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $email, $subject, $message);
    
    if ($stmt->execute()) {
        $success = "Thank you for contacting us! We'll get back to you soon.";
    } else {
        $success = "Error: " . $sql . "<br>" . $conn->error;
    }
    $stmt->close();
}

// Fetch all contacts
$contacts = [];
$sql = "SELECT * FROM contacts ORDER BY submission_date DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $contacts[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Smartenovations</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-section {
            margin: 40px auto;
            max-width: 1200px;
            padding: 20px;
        }
        .contacts-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .contacts-table th, .contacts-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .contacts-table th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        .contacts-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .contacts-table tr:hover {
            background-color: #f1f1f1;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <!-- Include the header -->
    <?php include 'header.php'; ?>

    <section class="contact" id="contact">
        <div class="container">
            <h2>Get In Touch</h2>
            <?php if (!empty($success)): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <div class="contact-container">
                <div class="contact-info">
                    <h3>Contact Information</h3>
                    <p><i class="fas fa-map-marker-alt"></i> 123 Tech Street, Silicon Valley, CA 94025</p>
                    <p><i class="fas fa-phone"></i> +1 (555) 123-4567</p>
                    <p><i class="fas fa-envelope"></i> info@smartenovations.com</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                
                <form class="contact-form" method="POST" action="">
                    <div class="form-group">
                        <input type="text" name="name" placeholder="Your Name" required>
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Your Email" required>
                    </div>
                    <div class="form-group">
                        <input type="text" name="subject" placeholder="Subject">
                    </div>
                    <div class="form-group">
                        <textarea name="message" placeholder="Your Message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>

            <!-- Admin Section to View Contacts -->
            <div class="admin-section">
                <h3>Customer Inquiries</h3>
                <?php if (count($contacts) > 0): ?>
                    <table class="contacts-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Subject</th>
                                <th>Message</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($contacts as $contact): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($contact['id']); ?></td>
                                    <td><?php echo htmlspecialchars($contact['name']); ?></td>
                                    <td><?php echo htmlspecialchars($contact['email']); ?></td>
                                    <td><?php echo htmlspecialchars($contact['subject']); ?></td>
                                    <td><?php echo nl2br(htmlspecialchars($contact['message'])); ?></td>
                                    <td><?php echo date('M j, Y g:i A', strtotime($contact['submission_date'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No contact submissions yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Include the footer -->
    <?php include 'footer.php'; ?>
</body>
</html>

<?php $conn->close(); ?>
