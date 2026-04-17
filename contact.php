<?php 
$pageTitle = "Contact Us - Vstar SSD";
include 'includes/header.php';

// Define recipient email
$recipient_email = "nrintellitech@gmail.com";

// Initialize variables
$name = $email = $message = "";
$error = $success = "";
$name_error = $email_error = $message_error = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Validation
    $valid = true;
    
    if (empty($name)) {
        $name_error = "Name is required";
        $valid = false;
    }
    
    if (empty($email)) {
        $email_error = "Email is required";
        $valid = false;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_error = "Invalid email format";
        $valid = false;
    }
    
    if (empty($message)) {
        $message_error = "Message is required";
        $valid = false;
    }
    
    // If validation passes, send email
    if ($valid) {
        // Email subject
        $subject = "New Contact Form Message from Vstar SSD Website";
        
        // Email body (HTML)
        $email_body = "
        <html>
        <head>
            <title>Contact Form Submission</title>
        </head>
        <body>
            <h2>New Contact Form Submission</h2>
            <p><strong>Name:</strong> " . htmlspecialchars($name) . "</p>
            <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
            <p><strong>Message:</strong></p>
            <p>" . nl2br(htmlspecialchars($message)) . "</p>
            <hr>
            <p><small>Submitted on: " . date('F j, Y, g:i a') . "</small></p>
            <p><small>IP Address: " . $_SERVER['REMOTE_ADDR'] . "</small></p>
        </body>
        </html>";
        
        // Email body (Plain text)
        $plain_body = "New Contact Form Submission\n\n";
        $plain_body .= "Name: " . $name . "\n";
        $plain_body .= "Email: " . $email . "\n";
        $plain_body .= "Message:\n" . $message . "\n\n";
        $plain_body .= "Submitted on: " . date('F j, Y, g:i a') . "\n";
        $plain_body .= "IP Address: " . $_SERVER['REMOTE_ADDR'] . "\n";
        
        // MULTIPART EMAIL HEADERS (Most reliable)
        $boundary = uniqid('np');
        
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/alternative;boundary=" . $boundary . "\r\n";
        
        // Set FROM address properly (use a valid domain email if possible)
        $from_email = "webmaster@" . $_SERVER['HTTP_HOST'];
        $headers .= "From: Vstar SSD Website <" . $from_email . ">\r\n";
        $headers .= "Reply-To: " . $email . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        
        // Email message with both plain and HTML parts
        $message_body = "--" . $boundary . "\r\n";
        $message_body .= "Content-type: text/plain;charset=utf-8\r\n\r\n";
        $message_body .= $plain_body . "\r\n";
        $message_body .= "--" . $boundary . "\r\n";
        $message_body .= "Content-type: text/html;charset=utf-8\r\n\r\n";
        $message_body .= $email_body . "\r\n";
        $message_body .= "--" . $boundary . "--";
        
        // Send email
        $mail_sent = @mail($recipient_email, $subject, $message_body, $headers);
        
        if ($mail_sent) {
            $success = "Thank you for your message. We will respond within 24 hours.";
            // Clear form
            $name = $email = $message = "";
            
            // Log the submission
            $log_entry = date('Y-m-d H:i:s') . " - Message sent to " . $recipient_email . " from " . $email . " - IP: " . $_SERVER['REMOTE_ADDR'] . "\n";
            file_put_contents('contact_log.txt', $log_entry, FILE_APPEND);
        } else {
            // More detailed error information
            $error_info = error_get_last();
            $error_msg = "Failed to send email.";
            
            if ($error_info) {
                $error_msg .= " Error: " . $error_info['message'];
            }
            
            // Check mail() configuration
            if (!function_exists('mail')) {
                $error_msg .= " PHP mail() function is not configured on this server.";
            }
            
            // Try alternative: SMTP via PHPMailer (if available)
            $error_msg .= " Alternatively, you can email us directly at nrintellitect@gmail.com";
            
            $error = $error_msg;
            
            // Log the error
            $error_log = date('Y-m-d H:i:s') . " - Email failed. From: " . $email . ", Error: " . ($error_info['message'] ?? 'Unknown') . "\n";
            file_put_contents('contact_errors.txt', $error_log, FILE_APPEND);
        }
    } else {
        $error = "Please correct the errors below.";
    }
}
?>

<!-- Contact Hero -->
<section class="contact-hero">
    <div class="container">
        <h1>Contact Us</h1>
        <p>Have questions about our SSDs or need assistance? We're here to help you find the right storage solution.</p>
    </div>
</section>

<!-- Contact Section -->
<section class="contact-section section">
    <div class="container">
        <?php if ($success): ?>
            <div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 4px; border: 1px solid #c3e6cb;">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error" style="background-color: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border-radius: 4px; border: 1px solid #f5c6cb;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <div class="contact-container">
            <div class="contact-form-container">
                <h2>Send a Message</h2>
                
                <form id="contactForm" class="contact-form" method="POST" action="">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" class="form-control" 
                               placeholder="Your name" value="<?php echo htmlspecialchars($name); ?>" required>
                        <?php if ($name_error): ?>
                            <span class="error-text" style="color: #dc3545; font-size: 14px; margin-top: 5px; display: block;">
                                <?php echo htmlspecialchars($name_error); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" 
                               placeholder="Your email address" value="<?php echo htmlspecialchars($email); ?>" required>
                        <?php if ($email_error): ?>
                            <span class="error-text" style="color: #dc3545; font-size: 14px; margin-top: 5px; display: block;">
                                <?php echo htmlspecialchars($email_error); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" class="form-control" 
                                  placeholder="How can we help you?" rows="5" required><?php echo htmlspecialchars($message); ?></textarea>
                        <?php if ($message_error): ?>
                            <span class="error-text" style="color: #dc3545; font-size: 14px; margin-top: 5px; display: block;">
                                <?php echo htmlspecialchars($message_error); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <button type="submit" class="btn">Send Message</button>
                </form>
            </div>
            
            <div class="contact-info-section">
                <h3>Contact Information</h3>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="info-content">
                        <h4>Office Location</h4>
                        <p>Lot #5.34, 5th floor<br>Imbi plaza, 28 Jalan Imbi, <br>55100 Kuala Lumpur, Malaysia</p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="info-content">
                        <h4>Phone Number</h4>
                        <p>(+60)3 2145 3006<br>(+60)12 316 2006<br>Mon-Fri: 11:00 AM - 6:00 PM</p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="info-content">
                        <h4>Email Address</h4>
                        <p>nrintellitect@gmail.com</p>
                        <p><small>Or use the contact form above</small></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>