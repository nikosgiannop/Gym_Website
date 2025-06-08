<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "user") {
    header("Location: index.php");
    exit();
}
?>
<!--page for viewing reservations  -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reservations</title>
    <link rel="stylesheet" href="dashboard.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            let userToken = localStorage.getItem("token");

            if (!userToken) {   //redirect to login if not logged in
                alert("You must be logged in to view reservations.");
                window.location.href = "index.php";
                return;
            }

            function formatDate(dateStr) {      //function to format date
                let date = new Date(dateStr);
                let options = { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' };
                return date.toLocaleDateString('en-US', options);
            }

            function fetchReservations() {    //function to fetch reservations
                $.ajax({
                    url: "http://localhost:3000/reservations/history",
                    method: "GET",
                    headers: { "Authorization": `Bearer ${userToken}` },
                    success: function(data) {
                        displayReservations(data);
                    },
                    error: function() {
                        $("#reservationsList").html("<p>Failed to load reservations.</p>");
                    }
                });
            }

            function cancelReservation(scheduleId) {    //function to cancel reservation
                $.ajax({
                    url: `http://localhost:3000/schedules/${scheduleId}/cancel`,
                    method: "DELETE",
                    headers: { "Authorization": `Bearer ${userToken}` },
                    success: function(response) {
                        alert(response.message);
                        fetchReservations(); // Refresh after canceling
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON?.error || "Failed to cancel reservation.");
                    }
                });
            }

            function displayReservations(reservations) {    //function to display reservations
                let html = "<table><tr><th>Date</th><th>Time</th><th>Program</th><th>Trainer</th><th>Status</th><th>Action</th></tr>";
                if (reservations.length === 0) {
                    html += "<tr><td colspan='6'>No reservations found.</td></tr>";
                } else {
                    reservations.forEach(r => {
                        let isCancelable = new Date(r.schedule_date) > new Date();
                        let button = isCancelable 
                            ? `<button class="cancel-btn" data-id="${r.schedule_id}">Cancel</button>` 
                            : `<span>Not cancellable</span>`;

                        html += `<tr>
                                    <td>${formatDate(r.schedule_date)}</td>
                                    <td>${r.schedule_time}</td>
                                    <td>${r.program_name}</td>
                                    <td>${r.trainer_first_name} ${r.trainer_last_name}</td>
                                    <td>${r.status}</td>
                                    <td>${button}</td>
                                </tr>`;
                    });
                }
                html += "</table>";
                $("#reservationsList").html(html);

                $(".cancel-btn").click(function() {
                    let scheduleId = $(this).attr("data-id");
                    cancelReservation(scheduleId);
                });
            }

            fetchReservations();
        });
    </script>
</head>
<body>
    <header>
        Οι κρατήσεις μου
    </header>

    <div class="container">
        <div class="section">
            <h1>Το ιστορικό κρατήσεων</h1>
            <div id="reservationsList"></div>
        </div>
    </div>

    <footer>
        Contact us for more information: <a href="mailto:info@gym.com">info@gym.com</a>
    </footer>
</body>
</html>