<!DOCTYPE html>
<html lang="en">
<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "user") {
    header("Location: index.php");
    exit();
}
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gym Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            //fetch Announcements
            $.ajax({
                url: "http://localhost:3000/announcements",
                method: "GET",
                headers: { "Authorization": localStorage.getItem("token") },
                success: function(data) {
                    let html = "<ul>";
                    data.forEach(a => {
                        html += `<li class="card">
                                    <span class="card-title">${a.title}</span>
                                    <p class="card-desc">${a.content}</p>
                                    <small class="card-date">Posted on: ${new Date(a.created_at).toLocaleDateString()}</small>
                                </li>`;
                    });
                    html += "</ul>";
                    $("#announcementsList").html(html);
                },
                error: function() {
                    $("#announcementsList").html("<p>Failed to load announcements.</p>");
                }
            });

            //fetch Programs
            $.ajax({
                url: "http://localhost:3000/programs",
                method: "GET",
                headers: { "Authorization": localStorage.getItem("token") },
                success: function(data) {
                    let html = "<ul>";
                    data.forEach(p => { //includes link to schedules.php with program name as parameter
                        html += `<li class="card">
                                    <a href="schedules.php?program_name=${encodeURIComponent(p.name)}" class="program-link">        
                                        <span class="card-title">${p.name}</span>
                                        <p class="card-desc">${p.description}</p>
                                    </a>
                                </li>`;
                    });
                    html += "</ul>";
                    $("#programsList").html(html);
                },
                error: function() {
                    $("#programsList").html("<p>Failed to load programs.</p>");
                }
            });

            //fetch Trainers
            $.ajax({
                url: "http://localhost:3000/trainers",
                method: "GET",
                headers: { "Authorization": localStorage.getItem("token") },
                success: function(data) {
                    let html = "<ul>";
                    data.forEach(t => {
                        html += `<li class="card">
                                    <span class="card-title">${t.first_name} ${t.last_name}</span>
                                    <p class="card-desc">Expertise: ${t.expertise}</p>
                                </li>`;
                    });
                    html += "</ul>";
                    $("#trainersList").html(html);
                },
                error: function() {
                    $("#trainersList").html("<p>Failed to load trainers.</p>");
                }
            });
        });
    </script>
</head>
<body>

    <header>
        Γυμναστήριο - Dashboard
    </header>

    <div class="container">

        <form action="index.php" method="get">
            <button type="submit" class="custom-button">Back to Home</button>   <!-- button to go back to home page -->
        </form>
        <form action="reservations.php" method="get">
            <button type="submit" class="custom-button">View Reservation History</button>   <!-- button to view reservation history (user specific)-->
        </form>
        <!-- Announcements Section -->
        <div class="section">
            <h1>Ανακοινώσεις</h1>
            <div id="announcementsList"></div>
        </div>

        <!-- Programs Section -->
        <div class="section">
            <h1>Προγράμματα Γυμναστικής</h1>   
            <div id="programsList"></div>
        </div>

        <!-- Trainers Section -->
        <div class="section">
            <h1>Οι Προπονητές μας</h1>
            <div id="trainersList"></div>
        </div>
    </div>
    <button onclick="window.location.href='index.php'" class="custom-button">Log Out</button>   <!-- button to log out -->
    
    <footer>
        Contact us for more information: <a href="mailto:info@gym.com">info@gym.com</a>
    </footer>

</body>
</html>