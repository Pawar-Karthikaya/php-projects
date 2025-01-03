<?php
// Start session to handle user authentication
//session_start();

// Include the database connection file
require "db.php";

// Check if the form is submitted using the POST method
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize input values to prevent XSS attacks
    $email = trim(htmlspecialchars($_POST["email"]));
    $loginPassword = trim(htmlspecialchars($_POST["password"]));

    // Check if both email and password fields are filled
    if (!empty($email) && !empty($loginPassword)) {
        try {
            // Prepare the SQL statement to fetch the user with the provided email
            $stmt = $conn->prepare("SELECT * FROM system_user WHERE email = ?");
            if (!$stmt) {
                throw new Exception("Failed to prepare SQL statement.");
            }
            $stmt->bind_param("s", $email); // Bind the email parameter
            $stmt->execute(); // Execute the statement
            $result = $stmt->get_result(); // Get the result set

            // Check if the user exists in the database
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc(); // Fetch user details

                // Verify the provided password against the stored hashed password
                if (password_verify($loginPassword, $user['password'])) {
                    // Set session variable for the authenticated user
                    $_SESSION['username'] = $user['user_name'];

                    // Redirect to the welcome page after successful login
                    header("Location: welcome.php");
                    exit(); // Stop further script execution after redirection
                } else {
                    // Display an error message for invalid password
                    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong> Invalid password. Please try again.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>';
                }
            } else {
                // Display an error message if the email is not registered
                echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong>Warning!</strong> No account found with this email. Please register first.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>';
            }
            $stmt->close(); // Close the prepared statement
        } catch (Exception $e) {
            // Handle unexpected errors
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong> An unexpected error occurred: ' . htmlspecialchars($e->getMessage()) . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
        }
    } else {
        // Display an error message if any required field is empty
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> Both email and password are required.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
    }
}
?>
