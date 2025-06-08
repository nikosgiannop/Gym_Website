<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin-dashboard.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>     
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <header>
        <h1>Admin Dashboard</h1>
    </header>

    <!-- Tab Navigation for announcements, trainers etc.-->
    <nav class="admin-tabs">
        <button class="tab-button active" data-tab="announcements">Announcements</button>
        <button class="tab-button" data-tab="trainers">Trainers</button>
        <button class="tab-button" data-tab="programs">Gym Programs</button>
        <button class="tab-button" data-tab="schedules">Schedules</button>
        <button class="tab-button" data-tab="requests">Registration Requests</button>
        <button class="tab-button" data-tab="users">Users</button>
        <button class="logout-button" onclick="window.location.href='index.php'">Logout</button>
    </nav>

    <!-- Tab Content -->
    <div id="tab-content">
        <div id="announcements" class="tab-pane active"></div>
        <div id="trainers" class="tab-pane"></div>
        <div id="programs" class="tab-pane"></div>
        <div id="schedules" class="tab-pane"></div>
        <div id="requests" class="tab-pane"></div>
        <div id="users" class="tab-pane"> </div>
    </div>

    <footer>
        <p>Contact us for more information: <a href="mailto:info@gym.com">info@gym.com</a></p>
    </footer>

    <!-- Modal used when creating an announcement-->
    <div id="announcementModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('announcementModal')">&times;</span>
            <h2>Add Announcement</h2>
            <form id="announcementForm">
                <label for="announcementTitle">Title:</label>
                <input type="text" id="announcementTitle" name="title" required><br>
                <label for="announcementContent">Content:</label>
                <textarea id="announcementContent" name="content" required></textarea><br>
                <label for="announcementVisible">Visible:</label>
                <input type="checkbox" id="announcementVisible" name="visible"><br>
                <button type="submit">Submit</button>
            </form>
        </div>
    </div>

    <!-- Edit Announcement Modal -->
    <div id="editAnnouncementModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editAnnouncementModal')">&times;</span>
            <h2>Edit Announcement</h2>
            <form id="editAnnouncementForm">
                <input type="hidden" id="editAnnouncementId"> <!-- Hidden ID -->
                <label for="editAnnouncementTitle">Title:</label>
                <input type="text" id="editAnnouncementTitle" name="title" required><br>
                <label for="editAnnouncementContent">Content:</label>
                <textarea id="editAnnouncementContent" name="content" required></textarea><br>
                <label for="editAnnouncementVisible">Visible:</label>
                <input type="checkbox" id="editAnnouncementVisible"><br>
                <button type="submit">Save Changes</button>
            </form>
        </div>
    </div>
        <!-- Trainer Modal -->
    <div id="trainerModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('trainerModal')">&times;</span>
            <h2>Add Trainer</h2>
            <form id="trainerForm">
                <label for="trainerFirstName">First Name:</label>
                <input type="text" id="trainerFirstName" name="first_name" required><br>
                <label for="trainerLastName">Last Name:</label>
                <input type="text" id="trainerLastName" name="last_name" required><br>
                <label for="trainerExpertise">Expertise:</label>
                <input type="text" id="trainerExpertise" name="expertise" required><br>
                <button type="submit">Submit</button>
            </form>
        </div>
    </div>

    <!-- Edit Trainer Modal -->
    <div id="editTrainerModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editTrainerModal')">&times;</span>
            <h2>Edit Trainer</h2>
            <form id="editTrainerForm">
                <input type="hidden" id="editTrainerId"> <!-- Hidden ID -->
                <label for="editTrainerFirstName">First Name:</label>
                <input type="text" id="editTrainerFirstName" name="first_name" required><br>
                <label for="editTrainerLastName">Last Name:</label>
                <input type="text" id="editTrainerLastName" name="last_name" required><br>
                <label for="editTrainerExpertise">Expertise:</label>
                <input type="text" id="editTrainerExpertise" name="expertise" required><br>
                <button type="submit">Save Changes</button>
            </form>
        </div>
    </div>

    <!-- Program Modal -->
    <div id="programModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('programModal')">&times;</span>
            <h2>Add Program</h2>
            <form id="programForm">
                <label for="programName">Name:</label>
                <input type="text" id="programName" name="name" required><br>
                <label for="programDescription">Description:</label>
                <textarea id="programDescription" name="description" required></textarea><br>
                <button type="submit">Submit</button>
            </form>
        </div>
    </div>

    <!-- Edit Gym Program Modal -->
    <div id="editProgramModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editProgramModal')">&times;</span>
            <h2>Edit Gym Program</h2>
            <form id="editProgramForm">
                <input type="hidden" id="editProgramId"> <!-- Hidden ID -->
                <label for="editProgramName">Name:</label>
                <input type="text" id="editProgramName" name="name" required><br>
                <label for="editProgramDescription">Description:</label>
                <textarea id="editProgramDescription" name="description" required></textarea><br>
                <button type="submit">Save Changes</button>
            </form>
        </div>
    </div>

        <!-- Schedule Modal -->
        <div id="scheduleModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal('scheduleModal')">&times;</span>
                <h2>Add Schedule</h2>
                <form id="scheduleForm">
                    <label for="scheduleProgram">Program:</label>
                    <select id="scheduleProgram" name="program_id" required></select><br>

                    <label for="scheduleDate">Date:</label>
                    <input type="date" id="scheduleDate" name="schedule_date" required><br>

                    <label for="scheduleTime">Time:</label>
                    <input type="time" id="scheduleTime" name="schedule_time" required><br>

                    <label for="scheduleTrainer">Trainer:</label>
                    <select id="scheduleTrainer" name="trainer_id" required></select><br>

                    <label for="scheduleMaxCapacity">Max Capacity:</label>
                    <input type="number" id="scheduleMaxCapacity" name="max_capacity" required><br>

                    <button type="submit">Create Schedule</button>
                </form>
            </div>
        </div>


        <!-- Edit Schedule Modal -->
        <div id="editScheduleModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal('editScheduleModal')">&times;</span>
                <h2>Edit Schedule</h2>
                <form id="editScheduleForm">
                    <input type="hidden" id="editScheduleId"> <!-- Hidden ID -->
            
                    <label for="editScheduleProgram">Program:</label>
                    <select id="editScheduleProgram" name="program_id" required></select><br>
            
                    <label for="editScheduleDate">Date:</label>
                    <input type="date" id="editScheduleDate" name="schedule_date" required><br>
            
                    <label for="editScheduleTime">Time:</label>
                    <input type="time" id="editScheduleTime" name="schedule_time" required><br>
            
                    <label for="editScheduleTrainer">Trainer:</label>
                    <select id="editScheduleTrainer" name="trainer_id" required></select><br>
            
                    <label for="editScheduleMaxCapacity">Max Capacity:</label>
                    <input type="number" id="editScheduleMaxCapacity" name="max_capacity" required><br>
            
                    <button type="submit">Save Changes</button>
                </form>
            </div>
        </div>

        <!-- New User Modal -->
        <div id="createUserModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal('createUserModal')">&times;</span>
                <h2>Create New User</h2>
                <form id="createUserForm">
                    <label for="createUserFirstName">First Name:</label>
                    <input type="text" id="createUserFirstName" name="first_name" required><br>
                    <label for="createUserLastName">Last Name:</label>
                    <input type="text" id="createUserLastName" name="last_name" required><br>
                    <label for="createUserEmail">Email:</label>
                    <input type="email" id="createUserEmail" name="email" required><br>
                    <label for="createUserUsername">Username:</label>
                    <input type="text" id="createUserUsername" name="username" required><br>
                    <label for="createUserRole">Role:</label>
                    <input type="text" id="createUserRole" name="role" required><br>
                    <label for="createUserPassword">Password:</label>
                    <input type="password" id="createUserPassword" name="password" required><br>
                    <label for="createUserCountry">Country:</label>
                    <select id="createUserCountry" name="country" required>
                        <option value="">Select Country</option>
                    </select><br>
                    <label for="createUserCity">City:</label>
                    <select id="createUserCity" name="city" required>
                        <option value="">Select City</option>
                    </select><br>
                    <label for="createUserAddress">Address:</label>
                    <input type="text" id="createUserAddress" name="address" required><br>
                    <button type="submit">Create User</button>
                </form>
            </div>
        </div>

        <!-- Edit User Modal -->
        <div id="editUserModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal('editUserModal')">&times;</span>
                <h2>Edit User</h2>
                <form id="editUserForm">
                    <input type="hidden" id="editUserId"> <!-- Hidden ID -->
                    <label for="editUserFirstName">First Name:</label>
                    <input type="text" id="editUserFirstName" name="first_name" required><br>
                    <label for="editUserLastName">Last Name:</label>
                    <input type="text" id="editUserLastName" name="last_name" required><br>
                    <select id="editUserCountry" name="country" required>
                        <option value="">Select Country</option>
                    </select><br>
                    <label for="editUserCity">City:</label>
                    <select id="editUserCity" name="city" required>
                        <option value="">Select City</option>
                    </select><br>
                    <label for="editUserAddress">Address:</label>
                    <input type="text" id="editUserAddress" name="address" required><br>
                    <label for="editUserEmail">Email:</label>
                    <input type="email" id="editUserEmail" name="email" required><br>
                    <label for="editUserUsername">Username:</label>
                    <input type="text" id="editUserUsername" name="username" required><br>
                    <label for="editUserRole">Role:</label>
                    <input type="text" id="editUserRole" name="role" required><br>
                    <button type="submit">Save Changes</button>
                </form>
            </div>
        </div>

    <script>
        $(document).ready(function() {
            $(".tab-button").click(function() {
                $(".tab-button").removeClass("active");
                $(this).addClass("active");

                $(".tab-pane").removeClass("active");
                $("#" + $(this).attr("data-tab")).addClass("active");
            });

            loadAnnouncements();        //functions that load the announcements, trainers, programs, schedules and registration requests
            loadTrainers();
            loadPrograms();
            loadSchedules();
            loadRequests();
            loadUsers();

            $("#announcementForm").submit(function(event) {
                event.preventDefault();
                addAnnouncement();
            });

            $("#trainerForm").submit(function(event) {
                event.preventDefault();
                addTrainer();
            });

            $("#programForm").submit(function(event) {
                event.preventDefault();
                addProgram();
            });

            $("#scheduleForm").submit(function(event) {
                event.preventDefault();
                addSchedule();
            });
        });

        function loadAnnouncements() {
            $.get("http://localhost:3000/admin/announcements", function(data) { //Use the admin API that fetches the announcements
                let html = "<h2>Announcements</h2>";
                html += `<button onclick="openModal('announcementModal')">Add New</button>`;
                html += `<div class="item-container">`; // Grid layout

                data.forEach(a => { //if 1 then it is visible, onclick editAnnouncement or deleteAnnouncement
                    html += `
                        <div class="item-card">
                            <h3>${a.title}</h3>
                            <p>${a.content}</p>
                            <p><strong>Visible:</strong> ${a.visible === 1 ? "Yes" : "No"}</p> 
                            <div class="actions">
                                <button onclick="editAnnouncement(${a.announcement_id})">Edit</button>      
                                <button class="delete" onclick="deleteAnnouncement(${a.announcement_id})">Delete</button>
                            </div>
                        </div>
                    `;
                });

                html += `</div>`;
                $("#announcements").html(html);
            });
        }

        function loadTrainers() {       //function that loads trainers, same with announcements
            $.get("http://localhost:3000/trainers", function(data) {
                let html = "<h2>Trainers</h2>";
                html += `<button onclick="openModal('trainerModal')">Add New</button>`;
                html += `<div class="item-container">`; 

                data.forEach(t => {
                    html += `
                        <div class="item-card">
                            <h3>${t.first_name} ${t.last_name}</h3>
                            <p><strong>Expertise:</strong> ${t.expertise}</p>
                            <div class="actions">
                                <button onclick="editTrainer(${t.trainer_id})">Edit</button>
                                <button class="delete" onclick="deleteTrainer(${t.trainer_id})">Delete</button>
                            </div>
                        </div>
                    `;
                });

                html += `</div>`;
                $("#trainers").html(html);
            });
        }

        function loadPrograms() {   //function that loads programs
            $.get("http://localhost:3000/programs", function(data) {
                let html = "<h2>Gym Programs</h2>";
                html += `<button onclick="openModal('programModal')">Add New</button>`;
                html += `<div class="item-container">`; // Grid layout

                data.forEach(p => {
                    html += `
                        <div class="item-card">
                            <h3>${p.name}</h3>
                            <p>${p.description}</p>
                            <div class="actions">
                                <button onclick="editProgram(${p.program_id})">Edit</button>
                                <button class="delete" onclick="deleteProgram(${p.program_id})">Delete</button>
                            </div>
                        </div>
                    `;
                });

                html += `</div>`;
                $("#programs").html(html);
            });
        }

        function loadSchedules() {
            $.get("http://localhost:3000/schedules", function(data) {
                let html = "<h2>Schedules</h2>";
                html += `<button onclick="openCreateScheduleModal()">Add New</button>`;
                html += `<div class="item-container">`;

                data.forEach(s => {
                    html += `
                        <div class="item-card">
                            <h3>${s.program_name}</h3>
                            <p><strong>Date:</strong> ${s.schedule_date}</p>
                            <p><strong>Time:</strong> ${s.schedule_time}</p>
                            <p><strong>Trainer:</strong> ${s.trainer_first_name} ${s.trainer_last_name}</p>
                            <p><strong>Max Capacity:</strong> ${s.max_capacity}</p>
                            <div class="actions">
                                <button onclick="editSchedule(${s.schedule_id})">Edit</button>
                                <button class="delete" onclick="deleteSchedule(${s.schedule_id})">Delete</button>
                            </div>
                        </div>
                    `;
                });

                html += `</div>`;
                $("#schedules").html(html);
            });
        }

        function loadProgramsDropdown() {   //used when editing a schedule for the dropdown of programs
            $.get("http://localhost:3000/programs", function(data) {
                let programDropdown = $("#editScheduleProgram");
                programDropdown.empty();    //clear existing options
                programDropdown.append(`<option value="">Select a Program</option>`);   //default option (should maybe have put the already exisitng one)
        
                data.forEach(program => {
                    programDropdown.append(`<option value="${program.program_id}">${program.name}</option>`);
                });
            });
        }

        function loadProgramsDropdownForCreate() {  //used when creating a schedule
            $.get("http://localhost:3000/programs", function(data) {
                let programDropdown = $("#scheduleProgram");
                programDropdown.empty(); //clear existing options
                programDropdown.append('<option value="">Select a Program</option>'); // Default option
     
                data.forEach(program => {
                    programDropdown.append(`<option value="${program.program_id}">${program.name}</option>`);
                });
            });
        }

        function loadTrainersDropdown() {   //used when editing schedule for the dropdown of trainers
            $.get("http://localhost:3000/trainers", function(data) {
                let trainerDropdown = $("#editScheduleTrainer");
                trainerDropdown.empty(); //clear existing options
                trainerDropdown.append(`<option value="">Select a Trainer</option>`); //defaut option
        
                data.forEach(trainer => {
                    trainerDropdown.append(`<option value="${trainer.trainer_id}">${trainer.first_name} ${trainer.last_name}</option>`);
                });
            });
        }
        function loadTrainersDropdownForCreate() {   //used when creating schedule for the dropdown of trainers
            $.get("http://localhost:3000/trainers", function(data) {
                let trainerDropdown = $("#scheduleTrainer");
                trainerDropdown.empty(); //clear existing options
                trainerDropdown.append('<option value="">Select a Trainer</option>'); // Default option
     
                data.forEach(trainer => {
                    trainerDropdown.append(`<option value="${trainer.trainer_id}">${trainer.first_name} ${trainer.last_name}</option>`);
                });
            });
        }

        function loadRequests() {   //function that loads all requests
            $.get("http://localhost:3000/registration-requests", function(data) {
                let html = "<h2>Registration Requests</h2>";
                html += "<ul>";

                data.forEach(r => { //for each request, display the user's info and status
                    html += `
                        <li>
                            <strong>Name:</strong> ${r.first_name}<br>
                            <strong>Surname:</strong> ${r.last_name}<br>
                            <strong>Email:</strong> ${r.email}<br>
                            <strong>Country:</strong> ${r.country}<br>
                            <strong>City:</strong> ${r.city}<br>
                            <strong>Address:</strong> ${r.address}<br>
                            <strong>Username:</strong> ${r.username}<br>
                            <strong>Status:</strong> <span style="color:${r.status === 'APPROVED' ? 'green' : r.status === 'REJECTED' ? 'red' : 'blue'};">${r.status}</span><br>
                    `;

                    if (r.status.toLowerCase() === "pending") {
                        html += `
                            <label for="role-${r.request_id}">Assign Role:</label>
                            <select id="role-${r.request_id}">
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                            <button onclick="approveRequest(${r.request_id})">Approve</button>
                            <button onclick="rejectRequest(${r.request_id})">Reject</button>
                        `;
                    }

                    html += `</li><hr>`; //add a horizontal line after each request
                });

                html += "</ul>";
                $("#requests").html(html);
            });
        }

        function loadUsers() {  //function that loads all users
            $.get("http://localhost:3000/users", function(data) {
                let html = "<h2>User Management</h2>";
                html += `
                    <button id="createUserBtn" onclick="openCreateUserModal()">Create New User</button>
                    <br><br>
                `;

                html += "<ul>";
                data.forEach(user => {
                    html += `
                        <li>
                            <strong>Name:</strong> ${user.first_name}<br>
                            <strong>Surname:</strong> ${user.last_name}<br>
                            <strong>Email:</strong> ${user.email}<br>
                            <strong>Username:</strong> ${user.username}<br>
                            <strong>Country:</strong> ${user.country}<br>
                            <strong>City:</strong> ${user.city}<br>
                            <strong>Address:</strong> ${user.address}<br>
                            <strong>Role:</strong> ${user.role}<br>
                            <button onclick="editUser(${user.user_id})">Edit</button>
                            <button onclick="deleteUser(${user.user_id})">Delete</button>
                        </li><hr>
                    `;
                });

                html += "</ul>";
                $("#users").html(html);
            });
        }

        function approveRequest(id) {       //function to approve registration requests
            let selectedRole = $(`#role-${id}`).val();  //get selected role from dropdown

            $.ajax({
                url: `http://localhost:3000/registration-requests/${id}/status`,
                method: "PUT",
                contentType: "application/json",
                data: JSON.stringify({ status: "APPROVED", role: selectedRole }),
                success: function(response) {
                    alert(response.message);
                    loadRequests();  // Reload requests to reflect status change
                },
                error: function(xhr) {
                    alert(xhr.responseJSON?.error || "Failed to approve request.");
                }
            });
        }

        function rejectRequest(id) {        //function to reject registration requests
            //no need to select role as the request is rejected
            $.ajax({
                url: `http://localhost:3000/registration-requests/${id}/status`,
                method: "PUT",
                headers: { "Content-Type": "application/json" },
                data: JSON.stringify({ status: "REJECTED" }),
                success: function(response) {
                    alert(response.message);
                    loadRequests();
                },
                error: function(xhr) {
                    alert(xhr.responseJSON?.error || "Failed to reject request.");
                }
            });
        }

        function addAnnouncement() {    //function to add an announcement
            let title = $("#announcementTitle").val();
            let content = $("#announcementContent").val();
            let visible = $("#announcementVisible").is(":checked") ? 1 : 0; //convert boolean to 1 or 0

            if (title && content) {
                $.ajax({
                    url: "http://localhost:3000/announcements",
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    data: JSON.stringify({ title: title, content: content, visible: visible }), //send the data to the server
                    success: function(response) {       
                        alert(response.message);
                        loadAnnouncements();
                        closeModal('announcementModal');
                    },
                    error: function(xhr) {      //if there is an error, alert the user
                        alert(xhr.responseJSON?.error || "Failed to create announcement.");
                    }
                });
            }
        }   

        function addTrainer() {     //function to add a trainer
            let firstName = $("#trainerFirstName").val();
            let lastName = $("#trainerLastName").val();
            let expertise = $("#trainerExpertise").val();

            if (firstName && lastName && expertise) {       
                $.ajax({
                    url: "http://localhost:3000/trainers",  //API call
                    method: "POST",
                    headers: { 
                        "Content-Type": "application/json"
                    },
                    data: JSON.stringify({ first_name: firstName, last_name: lastName, expertise: expertise }), 
                    success: function(response) {
                        alert(response.message);
                        loadTrainers();
                        closeModal('trainerModal');
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON?.error || "Failed to create trainer.");
                    }
                });
            }
        }

        function addProgram() {    //function to add a program
            let name = $("#programName").val();
            let description = $("#programDescription").val();

            if (name && description) {
                $.ajax({
                    url: "http://localhost:3000/programs",  //API call
                    method: "POST",
                    headers: { 
                        "Content-Type": "application/json"
                    },
                    data: JSON.stringify({ name: name, description: description }),
                    success: function(response) {
                        alert(response.message);
                        loadPrograms();
                        closeModal('programModal');
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        alert(xhr.responseJSON?.error || "Failed to create program.");
                    }
                });
            }
        }

        function openCreateScheduleModal() {    //function used to load the modals with the correct data for dropdowns
            loadProgramsDropdownForCreate();
            loadTrainersDropdownForCreate()
            openModal('scheduleModal'); //open the "Add Schedule" modal
        }
        

        function loadSchedules() {
            $.get("http://localhost:3000/schedules", function(data) {
                let html = "<h2>Schedules</h2>";
                html += `<button onclick="openCreateScheduleModal()">Add New</button>`; //loads dropdowns before opening the modal
                html += `<div class="item-container">`;

                data.forEach(s => { //for each schedule, display the program name, date, time, trainer, max capacity and buttons to edit or delete
                    html += `
                        <div class="item-card">
                            <h3>${s.program_name}</h3>
                            <p><strong>Date:</strong> ${s.schedule_date}</p>
                            <p><strong>Time:</strong> ${s.schedule_time}</p>
                            <p><strong>Trainer:</strong> ${s.trainer_first_name} ${s.trainer_last_name}</p>
                            <p><strong>Max Capacity:</strong> ${s.max_capacity}</p>
                            <div class="actions">
                                <button onclick="editSchedule(${s.schedule_id})">Edit</button>
                                <button class="delete" onclick="deleteSchedule(${s.schedule_id})">Delete</button>
                            </div>
                        </div>
                    `;
                });

                html += `</div>`;
                $("#schedules").html(html);
            });
        }

        function openModal(modalId) {       //function to open the modal
            $("#" + modalId).css("display", "block");
        }

        function closeModal(modalId) {      //function to close the modal
            $("#" + modalId).css("display", "none");
        }

        function editAnnouncement(id) {   //function to edit an announcement
            $.get(`http://localhost:3000/announcements/${id}`, function(data) {
                $("#editAnnouncementId").val(data.announcement_id);
                $("#editAnnouncementTitle").val(data.title);
                $("#editAnnouncementContent").val(data.content);
                $("#editAnnouncementVisible").prop("checked", data.visible === 1);

                openModal('editAnnouncementModal'); //open modal
            });
        }

        $("#editAnnouncementForm").submit(function(event) {     //function to submit the edited announcement
            event.preventDefault();
    
            let id = $("#editAnnouncementId").val();
            let title = $("#editAnnouncementTitle").val();
            let content = $("#editAnnouncementContent").val();
            let visible = $("#editAnnouncementVisible").is(":checked") ? 1 : 0;

            $.ajax({
                url: `http://localhost:3000/announcements/${id}`,   //calling API to update the announcement
                method: "PUT",
                headers: { "Content-Type": "application/json" },
                data: JSON.stringify({ title, content, visible }),
                success: function(response) {
                    alert(response.message);
                    loadAnnouncements();
                    closeModal('editAnnouncementModal'); //close modal after saving
                },
                error: function(xhr) {
                    alert(xhr.responseJSON?.error || "Failed to update announcement.");
                }
            });
        });

        function deleteAnnouncement(id) {   //function to delete an announcement
            if (confirm("Are you sure?")) {
                $.ajax({
                    url: `http://localhost:3000/announcements/${id}`,
                    method: "DELETE",
                    success: function() { loadAnnouncements(); }
                });
            }
        }

        function editTrainer(id) {    //function to edit a trainer
            $.get(`http://localhost:3000/trainers/${id}`, function(data) {
                if (!data) {
                    alert("Error: Trainer data not found.");
                    return;
                }

                $("#editTrainerId").val(data.trainer_id);
                $("#editTrainerFirstName").val(data.first_name);
                $("#editTrainerLastName").val(data.last_name);
                $("#editTrainerExpertise").val(data.expertise);

                openModal('editTrainerModal'); 
            }).fail(function(xhr) {
                alert(xhr.responseJSON?.error || "Failed to fetch trainer data.");
            });
        }

        $("#editTrainerForm").submit(function(event) {      //function to submit the edited trainer
            event.preventDefault();
    
            let id = $("#editTrainerId").val();
            let firstName = $("#editTrainerFirstName").val();
            let lastName = $("#editTrainerLastName").val();
            let expertise = $("#editTrainerExpertise").val();

            $.ajax({
                url: `http://localhost:3000/trainers/${id}`,
                method: "PUT",
                headers: { "Content-Type": "application/json" },
                data: JSON.stringify({ first_name: firstName, last_name: lastName, expertise }),
                success: function(response) {
                    alert(response.message);
                    loadTrainers();
                    closeModal('editTrainerModal');
                },
                error: function(xhr) {
                    alert(xhr.responseJSON?.error || "Failed to update trainer.");
                }
            });
        });

        function editProgram(id) {      //function to edit a program
            $.get(`http://localhost:3000/programs/${id}`, function(data) {
                if (!data) {
                    alert("Error: Gym Program not found.");
                    return;
                }

                $("#editProgramId").val(data.program_id);
                $("#editProgramName").val(data.name);
                $("#editProgramDescription").val(data.description);

                openModal('editProgramModal'); // Open modal
            }).fail(function(xhr) {
                alert(xhr.responseJSON?.error || "Failed to fetch program data.");
            });
        }

        $("#editProgramForm").submit(function(event) {      //function to submit the edited program
            event.preventDefault();
    
            let id = $("#editProgramId").val();
            let name = $("#editProgramName").val();
            let description = $("#editProgramDescription").val();

            $.ajax({
                url: `http://localhost:3000/programs/${id}`,
                method: "PUT",
                headers: { "Content-Type": "application/json" },
                data: JSON.stringify({ name, description }),
                success: function(response) {
                    alert(response.message);
                    loadPrograms();
                    closeModal('editProgramModal');
                },
                error: function(xhr) {
                    alert(xhr.responseJSON?.error || "Failed to update gym program.");
                }
            });
        });

        function deleteProgram(id) {        //function to delete a program
            if (confirm("Are you sure? This will also delete related schedules and reservations.")) {       //notifying that by deleting a program, related schedules and reservations will also be deleted
                $.ajax({
                    url: `http://localhost:3000/programs/${id}`,
                    method: "DELETE",
                    success: function(response) {
                        alert(response.message);
                        loadPrograms();
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON?.error || "Failed to delete program.");
                    }
                });
            }
        }

        function deleteTrainer(id) {    //function to delete a trainer
            if (confirm("Are you sure? This will also delete related schedules and reservations.")) {
                $.ajax({
                    url: `http://localhost:3000/trainers/${id}`,
                    method: "DELETE",
                    success: function() { loadTrainers(); }
                });
            }
        }

        function deleteSchedule(id) {   //function to delete a schedule
            if (confirm("Are you sure?")) {
                $.ajax({
                    url: `http://localhost:3000/schedules/${id}`,
                    method: "DELETE",
                    success: function() { loadSchedules(); }
                });
            }
        }
        
        function deleteUser(userId) {   //function to delete a user
            if (confirm("Are you sure you want to delete this user?")) {
                $.ajax({
                    url: `http://localhost:3000/users/${userId}`,
                    type: "DELETE",
                    success: function(response) {
                        alert(response.message);
                        loadUsers();
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON?.error || "Failed to delete user.");
                    }
                });
            }
        }


        function editSchedule(id) {    //function to edit a schedule
            $.get(`http://localhost:3000/schedules/${id}`, function(data) {
                if (!data) {
                    alert("Error: Schedule not found.");
                    return;
                }

                $("#editScheduleId").val(data.schedule_id);
                $("#editScheduleDate").val(data.schedule_date);
                $("#editScheduleTime").val(data.schedule_time);
                $("#editScheduleMaxCapacity").val(data.max_capacity);

                //load dropdowns and pre-select values
                loadProgramsDropdown();
                loadTrainersDropdown();

                setTimeout(() => {
                    $("#editScheduleProgram").val(data.program_id);
                    $("#editScheduleTrainer").val(data.trainer_id);
                }, 500); //delay setting values to ensure dropdowns are populated first (not ideal but works, found this fix online)

                openModal('editScheduleModal'); // Open modal
            }).fail(function(xhr) {
                alert(xhr.responseJSON?.error || "Failed to fetch schedule data.");
            });
        }

        function addSchedule() {    //function to add a schedule
            //load programs and trainers dropdowns
            loadProgramsDropdownForCreate();
            loadTrainersDropdownForCreate();

            openModal('scheduleModal');

            $("#scheduleForm").submit(function(event) {
                event.preventDefault();

                let programId = $("#scheduleProgram").val();
                let date = $("#scheduleDate").val();
                let time = $("#scheduleTime").val();
                let trainerId = $("#scheduleTrainer").val();
                let maxCapacity = $("#scheduleMaxCapacity").val();

                //validate the form fields (optional)
                if (!programId || !date || !time || !trainerId || !maxCapacity) {
                    alert("Please fill all the fields.");
                    return;
                }

                $.ajax({
                    url: "http://localhost:3000/schedules",
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    data: JSON.stringify({
                        program_id: programId,
                        schedule_date: date,
                        schedule_time: time,
                        trainer_id: trainerId,
                        max_capacity: maxCapacity,
                        current_capacity: 0 // Default to 0 when creating a schedule
                    }),
                    success: function(response) {
                        alert(response.message); // Success message
                        loadSchedules(); // Reload the schedules to update the list
                        closeModal('scheduleModal'); // Close the modal after creating the schedule
                    },
                    error: function(xhr) {
                    alert(xhr.responseJSON?.error || "Failed to create schedule.");
                    }
                });
            });
        }




        $("#editScheduleForm").submit(function(event) {
            event.preventDefault();
    
            let id = $("#editScheduleId").val();
            let programId = $("#editScheduleProgram").val();
            let date = $("#editScheduleDate").val();
            let time = $("#editScheduleTime").val();
            let trainerId = $("#editScheduleTrainer").val();
            let maxCapacity = $("#editScheduleMaxCapacity").val();

            $.ajax({
                url: `http://localhost:3000/schedules/${id}`,
                method: "PUT",
                headers: { "Content-Type": "application/json" },
                data: JSON.stringify({
                    program_id: programId,
                    schedule_date: date,
                    schedule_time: time,
                    trainer_id: trainerId,
                    max_capacity: maxCapacity
                }),
                success: function(response) {
                    alert(response.message);
                    loadSchedules();
                    closeModal('editScheduleModal');
                },
                error: function(xhr) {
                    alert(xhr.responseJSON?.error || "Failed to update schedule.");
                }
            });
        });

        function openCreateUserModal() {    //function to open the "Create User" modal
            $.get("http://localhost:3000/countries", function(countries) {  //fetch countries for the dropdown
                const countryDropdown = $("#createUserCountry");
                countryDropdown.empty();
                countryDropdown.append('<option value="">Select Country</option>');
                countries.forEach(country => {
                    countryDropdown.append(`<option value="${country}">${country}</option>`);
                });
            });

            // Empty the city dropdown in case it's already populated
            $("#createUserCity").empty().append('<option value="">Select City</option>');

            openModal('createUserModal');
        }


        $("#createUserCountry").change(function() {
            const selectedCountry = $(this).val();
            if (selectedCountry) {
                $.get(`http://localhost:3000/cities/${selectedCountry}`, function(cities) {
                    const cityDropdown = $("#createUserCity");
                    cityDropdown.empty();
                    cityDropdown.append('<option value="">Select City</option>');
                    cities.forEach(city => {
                        cityDropdown.append(`<option value="${city}">${city}</option>`);
                    });
                });
            } else {
                // Reset city dropdown if no country is selected
                $("#createUserCity").empty().append('<option value="">Select City</option>');
            }
        });

        //function to create a new user
        function createUser() {
            let first_name = $("#createUserFirstName").val();
            let last_name = $("#createUserLastName").val();
            let email = $("#createUserEmail").val();
            let username = $("#createUserUsername").val();
            let role = $("#createUserRole").val();
            let country = $("#createUserCountry").val();
            let city = $("#createUserCity").val();
            let address = $("#createUserAddress").val();

            //check if all required fields are filled
            if (first_name && last_name && email && username && role && country && city && address) {
                $.ajax({
                    url: "http://localhost:3000/users", // API call to create a new user
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    data: JSON.stringify({ 
                        first_name, 
                        last_name, 
                        email, 
                        username, 
                        role, 
                        country, 
                        city, 
                        address 
                    }),
                    success: function(response) {
                        alert(response.message); // Show success message
                        loadUsers();  // Refresh the users list or page
                        closeModal('createUserModal');  // Close the modal after creating the user
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);  // Log error for debugging
                        alert(xhr.responseJSON?.error || "Failed to create user.");
                    }
                });
            } else {
                alert("All fields are required.");
            }
        }

        $("#createUserForm").submit(function(e) {
            e.preventDefault();  //prevent the form from submitting the traditional way

            let first_name = $("#createUserFirstName").val();
            let last_name = $("#createUserLastName").val();
            let email = $("#createUserEmail").val();
            let username = $("#createUserUsername").val();
            let role = $("#createUserRole").val();
            let password = $("#createUserPassword").val();
            let country = $("#createUserCountry").val();
            let city = $("#createUserCity").val();
            let address = $("#createUserAddress").val();

            if (first_name && last_name && email && username && role && country && city && address) {
                $.ajax({
                    url: "http://localhost:3000/users", 
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    data: JSON.stringify({ 
                        first_name, 
                        last_name, 
                        email, 
                        username, 
                        role, 
                        password,
                        country, 
                        city, 
                        address 
                    }),
                    success: function(response) {
                        alert(response.message);
                        loadUsers();  //reload the users list
                        closeModal('createUserModal'); 
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON?.error || "Failed to create user.");
                    }
                });
            } else {
                alert("All fields are required.");
            }
        });

        
        //function to fetch user data and open the modal
        function editUser(id) {
            $.get(`http://localhost:3000/users/${id}`, function(data) {
                // Populate user details in the form
                $("#editUserId").val(data.user_id);
                $("#editUserFirstName").val(data.first_name);
                $("#editUserLastName").val(data.last_name);
                $("#editUserEmail").val(data.email);
                $("#editUserUsername").val(data.username);
                $("#editUserRole").val(data.role);

                // Populate the country dropdown
                $.get("http://localhost:3000/countries", function(countries) {
                    const countryDropdown = $("#editUserCountry");
                    countryDropdown.empty();
                    countryDropdown.append('<option value="">Select Country</option>');
                    countries.forEach(country => {
                        countryDropdown.append(`<option value="${country}" ${country === data.country ? 'selected' : ''}>${country}</option>`);
                    });

                    //after country is populated fetch cities for the selected country
                    const selectedCountry = data.country;
                    fetchCities(selectedCountry);
                });

                //function to fetch cities based on the selected country
                function fetchCities(country) {
                    $.get(`http://localhost:3000/cities/${country}`, function(cities) {
                        const cityDropdown = $("#editUserCity");
                        cityDropdown.empty();
                        cityDropdown.append('<option value="">Select City</option>');
                        cities.forEach(city => {
                            cityDropdown.append(`<option value="${city}" ${city === data.city ? 'selected' : ''}>${city}</option>`);
                        });
                    });
                }
                $("#editUserAddress").val(data.address);

                openModal('editUserModal');
            });
        }

        $("#editUserForm").submit(function(event) {
            event.preventDefault();

            let id = $("#editUserId").val();
            let first_name = $("#editUserFirstName").val();
            let last_name = $("#editUserLastName").val();
            let country = $("#editUserCountry").val();
            let city = $("#editUserCity").val();
            let address = $("#editUserAddress").val();
            let email = $("#editUserEmail").val();
            let username = $("#editUserUsername").val();
            let role = $("#editUserRole").val();

            $.ajax({
                url: `http://localhost:3000/users/${id}`,
                method: "PUT",
                headers: { "Content-Type": "application/json" },
                data: JSON.stringify({ first_name, last_name, country, city, address, email, username, role }),
                success: function(response) {
                    alert(response.message);
                    loadUsers();  //refresh the users list or page
                    closeModal('editUserModal');  //close the modal after saving
                },
                error: function(xhr) {
                    alert(xhr.responseJSON?.error || "Failed to update user.");
                }
            });
        });

    </script>
</body>
</html>