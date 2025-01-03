<?php
// Include the database connection file
require "db.php";

// Check if the form is submitted using the POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize input values
    $user_name = trim(htmlspecialchars($_POST["user_name"])); // Sanitize user name
    $email = trim(htmlspecialchars($_POST["email"])); // Sanitize email
    $password = trim(htmlspecialchars($_POST["password"])); // Sanitize password
    $confirmPassword = trim(htmlspecialchars($_POST["confirmPassword"])); // Sanitize confirm password

    // Check if all fields are filled
    if (!empty($user_name) && !empty($email) && !empty($password) && !empty($confirmPassword)) {
        // Check if passwords match
        if ($password !== $confirmPassword) {
            // Display a warning alert if passwords do not match
            echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <strong>Warning!</strong> Passwords do not match.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
        } else {
            // Prepare a SQL statement to check if the email already exists
            $checkEmailStmt = $conn->prepare("SELECT email FROM system_user WHERE email = ?");
            $checkEmailStmt->bind_param("s", $email); // Bind the email parameter
            $checkEmailStmt->execute(); // Execute the query
            $checkEmailStmt->store_result(); // Store the result

            // Check if email already exists in the database
            if ($checkEmailStmt->num_rows > 0) {
                // Display an error alert for duplicate email
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong> This email is already registered. Please use a different email.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>';
            } else {
                // Hash the password for security
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

                // Prepare a SQL statement to insert the new user into the database
                $stmt = $conn->prepare("INSERT INTO system_user (user_name, email, password) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $user_name, $email, $hashedPassword); // Bind parameters

                // Execute the insert query
                if ($stmt->execute()) {
                    // Display a success alert if account creation is successful
                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Success!</strong> Your account was created successfully.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>';
                } else {
                    // Display an error alert if account creation fails
                    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong> Failed to create account. Try again!
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>';
                }
                $stmt->close(); // Close the insert statement
            }
            $checkEmailStmt->close(); // Close the email check statement
        }
    } else {
        // Display an error alert if any required field is empty
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> All fields are required.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
    }
}
?>
