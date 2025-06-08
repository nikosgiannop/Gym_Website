<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gym Management System</title>
    <link rel="stylesheet" href="index.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            //Fetch countries for drop down menu
            $.get("http://localhost:3000/countries", function(data) {
                const countriesDropdown = $("#country");
                countriesDropdown.empty();
                countriesDropdown.append('<option value="">Select Country</option>');
                data.forEach(country => {
                    countriesDropdown.append(`<option value="${country}">${country}</option>`);
                });
            });

            //Fetch cities only when a country is selected
            $("#country").change(function() {
                const country = $(this).val();
                if (country) {
                    $.get(`http://localhost:3000/cities/${country}`, function(data) {
                        const citiesDropdown = $("#city");
                        citiesDropdown.empty();
                        citiesDropdown.append('<option value="">Select City</option>');
                        data.forEach(city => {
                            citiesDropdown.append(`<option value="${city}">${city}</option>`);
                        });
                    });
                } else {
                    $("#city").empty().append('<option value="">Select City</option>');
                }
            });
        });
    </script>
</head>
<body>
        
    <div id="container">
        <header>
            <h1>Γυμναστήριο - Log In&Register</h1>
        </header>
        
        <main>
            <!-- Go to Dashboard Button -->
            <form action="dashboardpublic.html" method="get">
            <button type="submit" class="custom-button">Go to Dashboard</button>
        </form>
            <h2>Login</h2>
            <form method="post" action="index.php">     <!-- form for logging in -->
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required><br>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required><br>
                <input type="submit" name="login" value="Login">
            </form>

            <h2>Register</h2>
            <form method="post" action="index.php">    <!-- form for registering -->
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" required><br>
                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" required><br>
                <label for="country">Country:</label>
                <select id="country" name="country" required>   <!-- selecting country -->
                    <option value="">Select Country</option>
                </select><br>
                <label for="city">City:</label>
                <select id="city" name="city" required>  <!-- selecting city -->
                    <option value="">Select City</option>
                </select><br>
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" required><br>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required><br>
                <label for="username_reg">Username:</label>
                <input type="text" id="username_reg" name="username_reg" required><br>
                <label for="password_reg">Password:</label>
                <input type="password" id="password_reg" name="password_reg" required><br>
                <input type="submit" name="register" value="Register">
            </form>

            <?php
            session_start();

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (isset($_POST['login'])) {
                    $username = $_POST['username'];
                    $password = $_POST['password'];
                    $data = json_encode(["username" => $username, "password" => $password]);
                    $url = "http://localhost:3000/login";
                
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                
                    $response = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);
                
                    if ($httpCode == 200) {
                        $responseData = json_decode($response, true);
                        $_SESSION['role'] = $responseData['role'];
                
                        // Redirect based on role
                        $redirectPage = ($responseData['role'] === 'admin') ? 'admin-dashboard.php' : 'dashboard.php';  //redirect admin to admin-dashboard and user to user dashboard
                        
                        //token stored in local storage
                        echo "<script>
                            localStorage.setItem('token', '{$responseData['token']}');  
                            window.location.href = '{$redirectPage}';
                        </script>";
                        exit(); 
                    } else {    //error message for debugging
                        echo "<p>Login failed: $response</p>";
                    }

                } elseif (isset($_POST['register'])) {      //registering a new user
                    $data = [
                        "first_name" => $_POST['first_name'],
                        "last_name" => $_POST['last_name'],
                        "country" => $_POST['country'],
                        "city" => $_POST['city'],
                        "email" => $_POST['email'],
                        "username" => $_POST['username_reg'],
                        "password" => $_POST['password_reg']
                    ];
                    $jsonData = json_encode($data);
                    $url = "http://localhost:3000/registerRequest";     //calling API for registration
                    
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                    
                    $response = curl_exec($ch);
                    curl_close($ch);
                    echo "<p>Registration Response: $response</p>";
                }
            }
            ?>

        </main>
        <footer>
            <a>Contact us for more information: <a href="mailto:info@gym.com">info@gym.com</a>
        </footer>
    </div>
</body>
</html>