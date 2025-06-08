<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "user") {
    header("Location: index.php");
    exit();
}

$program_name = isset($_GET['program_name']) ? urldecode($_GET['program_name']) : null;
if (!$program_name) {
    header("Location: dashboard.php"); // Redirect if no program name is provided
    exit();
}
?>
<!-- Page for viewing schedules for specific gym program -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Program Schedules</title>
    <link rel="stylesheet" href="dashboard.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            let programName = "<?php echo addslashes($program_name); ?>";
            let scheduleData = []; //store fetched schedules
            let userToken = localStorage.getItem("token"); //retrieve the stored user token (needed for booking)

            //function to format date with the day of the week
            function formatDate(dateStr) {
                let date = new Date(dateStr);
                let options = { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' };
                return date.toLocaleDateString('en-US', options); //change 'en-US' to 'el-GR' for Greek days (in order to display the day of the week in Greek)
            }

            //fetch Schedules for the Selected Program
            function fetchSchedules() {
                $.ajax({
                    url: "http://localhost:3000/schedules",
                    method: "GET",
                    success: function(data) {
                        scheduleData = data.filter(s => s.program_name === programName); //store filtered data
                        displaySchedules(scheduleData);
                    },
                    error: function() {
                        $("#schedulesList").html("<p>Failed to load schedules.</p>");
                    }
                });
            }

            //handle Booking Request
            function bookSchedule(scheduleId) {
                let userToken = localStorage.getItem("token"); //retrieve stored token

                if (!userToken) {   //redirect to login if not logged in
                    alert("You must be logged in to book a schedule.");
                return;
                }

                $.ajax({    //send booking request
                    url: `http://localhost:3000/schedules/${scheduleId}/book`,
                    method: "POST",
                    headers: { "Authorization": `Bearer ${userToken}` }, //include token in the header
                    success: function(response) {
                        alert(response.message);
                    fetchSchedules(); //refresh schedule data after booking
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON?.error || "Failed to book schedule.");
                    }
                });
            }

            //display schedules in the table
            function displaySchedules(filteredData) {
                let html = "<table><tr><th>Date</th><th>Time</th><th>Trainer</th><th>Reservations</th><th>Action</th></tr>";
                if (filteredData.length === 0) {
                    html += "<tr><td colspan='5'>No schedules found.</td></tr>";
                } else {
                    filteredData.forEach(s => {
                        let isFull = s.current_capacity >= s.max_capacity;
                        let button = isFull 
                            ? `<button class="full-btn" disabled>Full</button>`
                            : `<button class="book-btn" data-id="${s.schedule_id}">Book Now</button>`;

                        html += `<tr>
                                    <td>${formatDate(s.schedule_date)}</td>
                                    <td>${s.schedule_time}</td>
                                    <td>${s.trainer_first_name} ${s.trainer_last_name}</td>
                                    <td>${s.current_capacity} / ${s.max_capacity}</td>
                                    <td>${button}</td>
                                </tr>`;
                    });
                }
                html += "</table>";
                $("#schedulesList").html(html);

                //attach click event for booking buttons
                $(".book-btn").click(function() {
                    let scheduleId = $(this).attr("data-id");
                    bookSchedule(scheduleId);
                });
            }

            //filter schedules based on selected date range
            function filterSchedules() {
                let startDate = $("#startDate").val();
                let endDate = $("#endDate").val();

                let filteredData = scheduleData.filter(s => {
                    let scheduleDate = new Date(s.schedule_date);
                    return (!startDate || scheduleDate >= new Date(startDate)) &&
                           (!endDate || scheduleDate <= new Date(endDate));
                });

                displaySchedules(filteredData);
            }

            //fetch schedules when page loads
            fetchSchedules();

            //apply date filter when user selects a date
            $("#startDate, #endDate").on("change", filterSchedules);
        });
    </script>
</head>
<body>

    <header>
        Program Schedules
    </header>

    <div class="container">
        <div class="back-button-container">
            <button class="back-button" onclick="window.location.href='dashboard.php'">← Back to Dashboard</button> <!-- button to go back to dashboard -->
        </div>
        <div class="section">
            <h1>Πρόγραμμα για: <?php echo htmlspecialchars($program_name); ?></h1>  

            <!-- Search Bar for filtering by Date -->
            <div class="search-container">
                <label for="startDate">Start Date:</label>
                <input type="date" id="startDate">
                
                <label for="endDate">End Date:</label>
                <input type="date" id="endDate">
            </div>

            <!-- Schedules Table -->
            <div id="schedulesList"></div>
        </div>
    </div>

    <footer>
        Contact us for more information: <a href="mailto:info@gym.com">info@gym.com</a>
    </footer>

</body>
</html>