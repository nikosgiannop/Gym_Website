const express = require('express');
const router = express.Router();
const mysql = require('mysql');
const port = 3000;
const app = express();
router.use(express.json());
const axios = require('axios');     //needed for country/city API
const cors = require('cors');       //needed to allow cross-origin requests for country/city
const jwt = require('jsonwebtoken');

const db = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'GymManagement'
});

db.connect((err) => {
    if (err) {
        console.error('Error connecting to MySQL:', err.stack);
        return;
    }
    console.log('Connected to MySQL database');
});

app.get('/Test', (req, res) => {
    res.send('Test');
});

router.use(express.json());
router.use(cors());

//user Registration Request API
router.post('/registerRequest', (req, res) => {
    const { first_name, last_name, country, city, address, email, username, password } = req.body;
    const query = 'INSERT INTO RegistrationRequests (first_name, last_name, country, city, address, email, username, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
    db.query(query, [first_name, last_name, country, city, address, email, username, password], (err) => {
        if (err) {
            res.status(500).send(err.message);
            return;
        }
        res.status(201).json({ message: 'Registration request submitted successfully' });
    });
});

//admin-only endpoint to update registration request status
router.put('/registration-requests/:id/status', (req, res) => {
    const { id } = req.params;
    const { status, role } = req.body;

    if (!['APPROVED', 'REJECTED'].includes(status)) {   //check if status is valid
        return res.status(400).json({ error: 'Invalid status. Use APPROVED or REJECTED.' });
    }

    if (status === 'APPROVED' && !['user', 'admin'].includes(role)) {   //check if role is valid
        return res.status(400).json({ error: 'Invalid role. Use user or admin.' });
    }

    const selectQuery = "SELECT * FROM RegistrationRequests WHERE request_id = ?";  //fetch the request by ID
    db.query(selectQuery, [id], (err, results) => {
        if (err) return res.status(500).json({ error: err.message });
        if (results.length === 0) return res.status(404).json({ error: 'Registration request not found.' });

        const request = results[0];

        if (status === 'APPROVED') {        //if status is approved, insert user data into Users table
            const insertQuery = `
                INSERT INTO Users (first_name, last_name, country, city, address, email, username, password, role) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)`;

            db.query(insertQuery, 
                [request.first_name, request.last_name, request.country, request.city, request.address, request.email, request.username, request.password, role], 
                (insertErr) => {
                    if (insertErr) {
                        console.error("User insertion failed:", insertErr.message);
                        return res.status(500).json({ error: 'User insertion failed: ' + insertErr.message });
                    }

                    console.log("User inserted successfully!");

                    const updateQuery = "UPDATE RegistrationRequests SET status = 'APPROVED' WHERE request_id = ?"; //update request status
                    db.query(updateQuery, [id], (updateErr) => {
                        if (updateErr) {
                            console.error("Status update failed:", updateErr.message);
                            return res.status(500).json({ error: 'Failed to update request status: ' + updateErr.message });
                        }

                        console.log(`Registration request ${id} updated to status: APPROVED`);
                        return res.json({ message: "Registration request approved successfully." });
                    });
                }
            );
        } else {    //if status is rejected, update request status to REJECTED
            const updateQuery = "UPDATE RegistrationRequests SET status = 'REJECTED' WHERE request_id = ?";
            db.query(updateQuery, [id], (updateErr) => {
                if (updateErr) {
                    console.error("Status update failed:", updateErr.message);
                    return res.status(500).json({ error: 'Failed to update request status: ' + updateErr.message });
                }

                console.log(`Registration request ${id} updated to status: REJECTED`);
                return res.json({ message: "Registration request rejected successfully." });
            });
        }
    });
});

//get all users
router.get('/users', (req, res) => {
    const query = "SELECT * FROM Users";
    db.query(query, (err, results) => {
        if (err) return res.status(500).json({ error: err.message });
        res.json(results);
    });
});

//get specific user (used in updating)
router.get('/users/:id', (req, res) => {    
    const { id } = req.params; 
    const query = "SELECT * FROM Users WHERE user_id = ?"; 

    db.query(query, [id], (err, rows) => {
        if (err) return res.status(500).json({ error: err.message });  
        if (rows.length === 0) return res.status(404).json({ error: 'User not found' }); 

        res.json(rows[0]);  
    });
});

//create a new user
router.post('/users', (req, res) => {
    const { first_name, last_name, email, username, role, password, country, city, address } = req.body;
  
    //check if all required fields are provided
    if (!first_name || !last_name || !email || !username || !role || !password || !country || !city || !address) {
      return res.status(400).json({ error: 'All fields are required' });
    }
  
    const query = `
      INSERT INTO Users (first_name, last_name, email, username, role, password, country, city, address)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    `;
  
    db.query(query, [first_name, last_name, email, username, role, password, country, city, address], (err, results) => {
      if (err) {
        console.error(err);
        return res.status(500).json({ error: err.message });
      }
  
      //return the new user_id in the response
      res.status(201).json({
        message: 'User created successfully',
        user_id: results.insertId  //return the new user_id from the inserted row
      });
    });
  });


router.put('/users/:id',  async (req, res) => {      //updating user data
    const { id } = req.params;
    const { first_name, last_name, email, country, city, address } = req.body;

    try {
        db.query("UPDATE Users SET first_name = ?, last_name = ?, email = ?, country = ?, city = ?, address = ? WHERE user_id = ?", [first_name, last_name, email, country, city, address, id]);
        res.json({ message: 'User information updated' });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

router.put('/users/:id/role', async (req, res) => {     //updating a User's role (user or admin)
    const { id } = req.params;
    const { role } = req.body;

    try {
        const validRoles = ['user', 'trainer', 'admin']; //define valid roles for security
        if (!validRoles.includes(role)) {
            return res.status(400).json({ error: 'Invalid role' });
        }

        const [result] = db.query("UPDATE Users SET role = ? WHERE user_id = ?", [role, id]);
        if (result.affectedRows === 0) {
            return res.status(404).json({ error: 'User not found' });
        }
        res.json({ message: 'User role updated successfully' });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

router.delete('/users/:id', (req, res) => { //deleting users
    const { id } = req.params;

    const deleteReservations = "DELETE FROM Reservations WHERE user_id = ?";
    const deleteCancellations = "DELETE FROM WeeklyCancellationTracking WHERE user_id = ?";
    const deleteUser = "DELETE FROM Users WHERE user_id = ?";

    db.query(deleteReservations, [id], (err) => {
        if (err) return res.status(500).json({ error: "Error deleting reservations: " + err.message });

        db.query(deleteCancellations, [id], (err) => {
            if (err) return res.status(500).json({ error: "Error deleting cancellations: " + err.message });

            db.query(deleteUser, [id], (err) => {
                if (err) return res.status(500).json({ error: "Error deleting user: " + err.message });

                res.json({ message: "User and all related data deleted successfully." });
            });
        });
    });
});

//getting all user requests for admin API
router.get('/registration-requests', (req, res) => {
    const sql = "SELECT * FROM RegistrationRequests ORDER BY request_id DESC";
    db.query(sql, (err, results) => {
        if (err) {
            console.error('Error fetching requests:', err);
            res.status(500).json({ error: 'An error occurred while fetching registration requests.' });
        } else {
            res.status(200).json(results);
        }
    });
});

function authenticateToken(req, res, next) {    //middleware to authenticate JWT token
    const token = req.headers['authorization'];
    if (!token) {
        return res.status(401).json({ error: 'Access Denied. No token provided.' });
    }

    //extract the actual token after Bearer
    const tokenValue = token.split(' ')[1];

    jwt.verify(tokenValue, 'secret_key', (err, user) => {
        if (err) {
            return res.status(403).json({ error: 'Invalid Token' });
        }
        req.user = user; //store user info in request
        next();
    });
}

router.post('/login', (req, res) => {   //login API
    const { username, password } = req.body;
    const query = 'SELECT * FROM Users WHERE username = ? AND password = ?';

    db.query(query, [username, password], (err, results) => {
        if (err) {
            return res.status(500).json({ error: err.message });
        }

        if (results.length > 0) {
            const user = results[0];

            // Generate JWT token
            const token = jwt.sign(
                { id: user.user_id, role: user.role }, //store user ID & role in token
                'secret_key', //this is an example because this is an assignment, if this was a real website this would be a an actual secret key
                { expiresIn: '1h' } // token expires in 1 hour
            );

            res.status(200).json({
                message: 'Login successful',
                token: token,   //return the JWT token
                role: user.role
            });
        } else {
            res.status(401).json({ error: 'Invalid username or password' });
        }
    });
});

//CRUD Operations for Gym Programs
router.get('/programs', (req, res) => {
    db.query('SELECT * FROM GymPrograms', (err, results) => {
        if (err) {
            res.status(500).send(err.message);
            return;
        }
        res.json(results);
    });
});

//fetches specific gym program (used in editing a program)
router.get('/programs/:id', (req, res) => {
    const { id } = req.params;
    const query = "SELECT * FROM GymPrograms WHERE program_id = ?";

    db.query(query, [id], (err, rows) => {
        if (err) return res.status(500).json({ error: err.message });
        if (rows.length === 0) return res.status(404).json({ error: 'Program not found' });

        res.json(rows[0]); 
    });
});

//delete a program (Admins Only)
router.delete('/programs/:id', (req, res) => {
    const { id } = req.params;

    //delete all reservations associated with schedules of this program
    const deleteReservationsQuery = `
        DELETE FROM Reservations 
        WHERE schedule_id IN (SELECT schedule_id FROM ProgramSchedules WHERE program_id = ?)`;

    db.query(deleteReservationsQuery, [id], (err, results) => {
        if (err) {
            console.error("Error deleting reservations:", err);
            return res.status(500).json({ error: 'Failed to delete reservations.', details: err.message });
        }

        console.log(`Deleted ${results.affectedRows} reservations for program ID ${id}`);

        //delete all schedules associated with this program
        const deleteSchedulesQuery = `
            DELETE FROM ProgramSchedules 
            WHERE program_id = ?`;

        db.query(deleteSchedulesQuery, [id], (err, results) => {
            if (err) {
                console.error("Error deleting schedules:", err);
                return res.status(500).json({ error: 'Failed to delete schedules.', details: err.message });
            }

            console.log(`Deleted ${results.affectedRows} schedules for program ID ${id}`);

            //delete the program itself
            const deleteProgramQuery = `
                DELETE FROM GymPrograms 
                WHERE program_id = ?`;

            db.query(deleteProgramQuery, [id], (err, results) => {
                if (err) {
                    console.error("Error deleting program:", err);
                    return res.status(500).json({ error: 'Failed to delete program.', details: err.message });
                }

                if (results.affectedRows === 0) {
                    return res.status(404).json({ error: 'Program not found or already deleted.' });
                }

                console.log(`Deleted program ID ${id}`);
                res.json({ message: 'Program, schedules, and reservations deleted successfully.' });
            });
        });
    });
});

//get all visible announcements
router.get('/announcements', (req, res) => {

    const query = "SELECT * FROM Announcements WHERE visible = TRUE";
    db.query(query, (err, rows) => {
        if (err) return res.status(500).json({ error: err.message });
        res.json(rows);
    });
});

//get all announcements (Admins Only)
router.get('/admin/announcements', (req, res) => {
    const query = "SELECT * FROM Announcements"; //admin gets all the announcements, regardless of visibility
    db.query(query, (err, rows) => {
        if (err) return res.status(500).json({ error: err.message });
        res.json(rows);
    });
});

//get a single announcement (used in editing an announcement)
router.get('/announcements/:id', (req, res) => {
    const { id } = req.params;
    const query = "SELECT * FROM Announcements WHERE announcement_id = ?";

    db.query(query, [id], (err, rows) => {
        if (err) return res.status(500).json({ error: err.message });
        if (rows.length === 0) return res.status(404).json({ error: 'Announcement not found' });
        res.json(rows[0]); //return the announcement object
    });
});

//create a new announcement (Admins Only)
router.post('/announcements', (req, res) => {
    const { title, content, visible } = req.body; 
    const query = "INSERT INTO Announcements (title, content, visible) VALUES (?, ?, ?)";
    
    db.query(query, [title, content, visible], (err) => { 
        if (err) return res.status(500).json({ error: err.message });
        res.status(201).json({ message: 'Announcement created successfully' });
    });
});

//update an existing announcement (Admins Only)
router.put('/announcements/:id', (req, res) => {
    const { id } = req.params;
    const { title, content, visible } = req.body;
    const query = "UPDATE Announcements SET title = ?, content = ?, visible = ? WHERE announcement_id = ?";
    
    db.query(query, [title, content, visible, id], (err, results) => {
        if (err) return res.status(500).json({ error: err.message });
        res.json({ message: 'Announcement updated successfully' });
    });
});

//delete an announcement (Admins Only)
router.delete('/announcements/:id', (req, res) => {
    const { id } = req.params;
    const query = "DELETE FROM Announcements WHERE announcement_id = ?";
    
    db.query(query, [id], (err, results) => {
        if (err) return res.status(500).json({ error: err.message });
        res.json({ message: 'Announcement deleted successfully' });
    });
});

//helper function to get the start of the current week  (used in booking/canceling schedules)
const getWeekStartDate = () => {
    const now = new Date();
    const dayOfWeek = now.getDay(); //sunday = 0, Monday = 1, ...
    const diff = now.getDate() - dayOfWeek + (dayOfWeek === 0 ? -6 : 1); //adjust for Monday as start of the week
    const weekStart = new Date(now.setDate(diff));
    return weekStart.toISOString().split("T")[0]; //return as YYYY-MM-DD
};

//use this middleware in protected routes
router.use('/schedules/:id/book', authenticateToken);
router.use('/schedules/:id/cancel', authenticateToken);

//book a program if capacity allows and cancellations are within limits
router.post('/schedules/:id/book', authenticateToken, (req, res) => {
    const { id } = req.params; //schedule ID
    const userId = req.user.id;  //retrieve from authenticated token
    const weekStart = getWeekStartDate(); 

    console.log("User Booking:", { userId, scheduleId: id });

    //check if the user has exceeded their weekly cancellations
    const checkCancellationsQuery = `
        SELECT cancellations FROM WeeklyCancellationTracking 
        WHERE user_id = ? AND week_start = ?`;
    
    db.query(checkCancellationsQuery, [userId, weekStart], (err, results) => {
        if (err) {
            console.error("Error checking cancellations:", err);
            return res.status(500).json({ error: 'Failed to check weekly cancellations.' });
        }

        const cancellations = results.length > 0 ? results[0].cancellations : 0;
        if (cancellations >= 2) {
            return res.status(400).json({ error: 'Booking limit reached due to excessive cancellations.' });
        }

        //check if the schedule has available capacity
        const capacityQuery = `
            SELECT max_capacity, current_capacity 
            FROM ProgramSchedules 
            WHERE schedule_id = ?`;
        
        db.query(capacityQuery, [id], (capacityErr, capacityResults) => {
            if (capacityErr) {
                console.error("Error checking capacity:", capacityErr);
                return res.status(500).json({ error: 'Failed to check schedule capacity.' });
            }
            if (capacityResults.length === 0) {
                console.warn("Schedule not found.");
                return res.status(404).json({ error: 'Schedule not found.' });
            }

            const { max_capacity, current_capacity } = capacityResults[0];
            console.log("Capacity Check:", { max_capacity, current_capacity });

            if (current_capacity >= max_capacity) {
                console.warn("Schedule is fully booked.");
                return res.status(400).json({ error: 'Schedule is fully booked.' });
            }

            //insert the booking into the Reservations table
            const bookingQuery = `
                INSERT INTO Reservations (user_id, schedule_id, reservation_date, status) 
                VALUES (?, ?, NOW(), 'active')`;
            
            db.query(bookingQuery, [userId, id], (bookingErr) => {
                if (bookingErr) {
                    console.error("Error inserting booking:", bookingErr);
                    return res.status(500).json({ error: 'Failed to book the schedule.' });
                }

                //update the current capacity in `ProgramSchedules`
                const updateCapacityQuery = `
                    UPDATE ProgramSchedules 
                    SET current_capacity = current_capacity + 1 
                    WHERE schedule_id = ?`;
                
                db.query(updateCapacityQuery, [id], (updateErr) => {
                    if (updateErr) {
                        console.error("Error updating capacity:", updateErr);
                        return res.status(500).json({ error: 'Failed to update schedule capacity.' });
                    }

                    console.log("Booking successful!");
                    res.status(201).json({ message: 'Booking successful!' });
                });
            });
        });
    });
});

//cancel a booking
router.delete('/schedules/:id/cancel', (req, res) => {
    const { id } = req.params; //schedule ID
    const userId = req.user.id;
    const weekStart = getWeekStartDate();

    //check if booking exists and get the session time
    const checkBookingQuery = `
        SELECT R.*, PS.schedule_date, PS.schedule_time 
        FROM Reservations R
        JOIN ProgramSchedules PS ON R.schedule_id = PS.schedule_id
        WHERE R.user_id = ? AND R.schedule_id = ? AND R.status = 'active'`;
    
    db.query(checkBookingQuery, [userId, id], (err, results) => {
        if (err) return res.status(500).json({ error: 'Failed to check booking.' });
        if (results.length === 0) return res.status(404).json({ error: 'Booking not found.' });

        //ensure cancellation is at least 2 hours before the scheduled time
        const { schedule_date, schedule_time } = results[0];
        const sessionDateTime = new Date(`${schedule_date}T${schedule_time}`); //combine date and time
        const currentTime = new Date();
        const timeDifference = (sessionDateTime - currentTime) / (1000 * 60 * 60); //convert to hours

        if (timeDifference <= 2) {
            return res.status(400).json({ error: 'Cannot cancel less than 2 hours before the session starts.' });
        }

        //cancel the booking by updating status to 'cancelled'
        const cancelBookingQuery = `
            UPDATE Reservations 
            SET status = 'cancelled' 
            WHERE user_id = ? AND schedule_id = ?`;
        
        db.query(cancelBookingQuery, [userId, id], (cancelErr) => {
            if (cancelErr) return res.status(500).json({ error: 'Failed to cancel booking.' });

            //decrement schedule capacity
            const updateCapacityQuery = `
                UPDATE ProgramSchedules 
                SET current_capacity = current_capacity - 1 
                WHERE schedule_id = ?`;
            
            db.query(updateCapacityQuery, [id], (capacityErr) => {
                if (capacityErr) return res.status(500).json({ error: 'Failed to update schedule capacity.' });

                //update WeeklyCancellationTracking
                const updateTrackingQuery = `
                    INSERT INTO WeeklyCancellationTracking (user_id, week_start, cancellations) 
                    VALUES (?, ?, 1) 
                    ON DUPLICATE KEY UPDATE cancellations = cancellations + 1`;
                
                db.query(updateTrackingQuery, [userId, weekStart], (trackingErr) => {
                    if (trackingErr) return res.status(500).json({ error: 'Failed to update cancellation tracking.' });
                    
                    res.json({ message: 'Booking canceled successfully!' });
                });
            });
        });
    });
});

//api to fetch reservations for a specific user
router.get('/reservations/history', authenticateToken, (req, res) => {
    const userId = req.user.id;

    //fetch reservations for the user and include program and trainer details
    const query = ` 
        SELECT 
            R.schedule_id,
            PS.schedule_date,
            PS.schedule_time,
            GP.name AS program_name,
            T.first_name AS trainer_first_name, 
            T.last_name AS trainer_last_name,
            R.status
        FROM 
            Reservations AS R
        JOIN 
            ProgramSchedules AS PS ON R.schedule_id = PS.schedule_id
        JOIN 
            GymPrograms AS GP ON PS.program_id = GP.program_id
        JOIN 
            Trainers AS T ON PS.trainer_id = T.trainer_id
        WHERE 
            R.user_id = ?
        ORDER BY 
            PS.schedule_date DESC, PS.schedule_time DESC;
    `;

    db.query(query, [userId], (err, results) => {
        if (err) return res.status(500).json({ error: 'Failed to fetch reservation history.' });
        res.json(results);
    });
});

//api that fetches all trainers
router.get('/trainers',(req, res) => {
    const query = "SELECT * FROM Trainers";
    db.query(query, (err, results) => {
        if (err) return res.status(500).json({ error: 'Failed to fetch trainers.' });
        res.json(results);
    });
});

//api that fetches a specific trainer (used in editing)
router.get('/trainers/:id', (req, res) => {
    const { id } = req.params;
    const query = "SELECT * FROM Trainers WHERE trainer_id = ?";

    db.query(query, [id], (err, rows) => {
        if (err) return res.status(500).json({ error: err.message });
        if (rows.length === 0) return res.status(404).json({ error: 'Trainer not found' });

        res.json(rows[0]); //send only the first result
    });
});

//create a new trainer (Admins Only)
router.post('/trainers', (req, res) => {
    const { first_name, last_name, expertise } = req.body;
    const query = "INSERT INTO Trainers (first_name, last_name, expertise) VALUES (?, ?, ?)";

    db.query(query, [first_name, last_name, expertise], (err) => {
        if (err) return res.status(500).json({ error: err.message });
        res.status(201).json({ message: 'Trainer created successfully' });
    });
});

//update a trainer (Admins Only)
router.put('/trainers/:id', (req, res) => {
    const { id } = req.params;
    const { first_name, last_name, expertise } = req.body;
    const query = "UPDATE Trainers SET first_name = ?, last_name = ?, expertise = ? WHERE trainer_id = ?";

    db.query(query, [first_name, last_name, expertise, id], (err, results) => {
        if (err) return res.status(500).json({ error: err.message });
        res.json({ message: 'Trainer updated successfully' });
    });
});

//delete a trainer (Admins Only)
router.delete('/trainers/:id', (req, res) => {
    const { id } = req.params;

    //delete all reservations associated with schedules by this trainer
    const deleteReservationsQuery = `
        DELETE FROM Reservations 
        WHERE schedule_id IN (SELECT schedule_id FROM ProgramSchedules WHERE trainer_id = ?)`;

    db.query(deleteReservationsQuery, [id], (err, results) => {
        if (err) {
            console.error("Error deleting reservations:", err);
            return res.status(500).json({ error: 'Failed to delete reservations.', details: err.message });
        }

        console.log(`Deleted ${results.affectedRows} reservations for trainer ID ${id}`);

        //delete all schedules associated with this trainer
        const deleteSchedulesQuery = `
            DELETE FROM ProgramSchedules 
            WHERE trainer_id = ?`;

        db.query(deleteSchedulesQuery, [id], (err, results) => {
            if (err) {
                console.error("Error deleting schedules:", err);
                return res.status(500).json({ error: 'Failed to delete schedules.', details: err.message });
            }

            console.log(`Deleted ${results.affectedRows} schedules for trainer ID ${id}`);

            //delete the trainer itself
            const deleteTrainerQuery = `
                DELETE FROM Trainers 
                WHERE trainer_id = ?`;

            db.query(deleteTrainerQuery, [id], (err, results) => {
                if (err) {
                    console.error("Error deleting trainer:", err);
                    return res.status(500).json({ error: 'Failed to delete trainer.', details: err.message });
                }

                if (results.affectedRows === 0) {
                    return res.status(404).json({ error: 'Trainer not found or already deleted.' });
                }

                console.log(`Deleted trainer ID ${id}`);
                res.json({ message: 'Trainer, associated schedules, and reservations deleted successfully.' });
            });
        });
    });
});

//fetch all schedules (with program and trainer details)
router.get('/schedules', (req, res) => {
    const query = `
        SELECT 
            PS.schedule_id, PS.schedule_date, PS.schedule_time,
            GP.name AS program_name,
            T.first_name AS trainer_first_name, T.last_name AS trainer_last_name,
            PS.max_capacity, PS.current_capacity  -- Fetch capacities from ProgramSchedules
        FROM 
            ProgramSchedules AS PS
        JOIN 
            GymPrograms AS GP ON PS.program_id = GP.program_id
        JOIN 
            Trainers AS T ON PS.trainer_id = T.trainer_id
        ORDER BY 
            PS.schedule_date ASC, PS.schedule_time ASC;  -- Orders by date first, then by time
    `;

    db.query(query, (err, results) => {
        if (err) return res.status(500).json({ error: 'Failed to fetch schedules.' });
        res.json(results);
    });
});

//fetch specific schedule (and data like trainer and gym program)
router.get('/schedules/:id', (req, res) => {
    const { id } = req.params;
    const query = `
        SELECT 
            PS.schedule_id, PS.schedule_date, PS.schedule_time,
            GP.name AS program_name,
            T.first_name AS trainer_first_name, T.last_name AS trainer_last_name,
            PS.max_capacity, PS.current_capacity
        FROM 
            ProgramSchedules AS PS
        JOIN 
            GymPrograms AS GP ON PS.program_id = GP.program_id
        JOIN 
            Trainers AS T ON PS.trainer_id = T.trainer_id
        WHERE 
            PS.schedule_id = ?`;

    db.query(query, [id], (err, rows) => {
        if (err) return res.status(500).json({ error: err.message });
        if (rows.length === 0) return res.status(404).json({ error: 'Schedule not found' });

        res.json(rows[0]); //return the schedule object
    });
});

//create new schedule
router.post('/schedules', (req, res) => {
    const { program_id, schedule_date, schedule_time, trainer_id, max_capacity, current_capacity } = req.body;

    if (!program_id || !trainer_id) {
        return res.status(400).json({ error: "Program and Trainer must be selected." });
    }

    const insertQuery = `
        INSERT INTO ProgramSchedules (program_id, trainer_id, schedule_date, schedule_time, max_capacity, current_capacity) 
        VALUES (?, ?, ?, ?, ?, ?)`;

    db.query(insertQuery, [program_id, trainer_id, schedule_date, schedule_time, max_capacity, current_capacity], (err, results) => {
        if (err) return res.status(500).json({ error: err.message });

        res.status(201).json({
            message: 'Schedule created successfully',
            schedule_id: results.insertId
        });
    });
});

//update schedules
router.put('/schedules/:id', (req, res) => {
    const { id } = req.params;
    const { program_id, schedule_date, schedule_time, trainer_id, max_capacity } = req.body;

    const updateQuery = `
        UPDATE ProgramSchedules 
        SET program_id = ?, trainer_id = ?, schedule_date = ?, schedule_time = ?, max_capacity = ?
        WHERE schedule_id = ?`;

    db.query(updateQuery, [program_id, trainer_id, schedule_date, schedule_time, max_capacity, id], (err, result) => {
        if (err) return res.status(500).json({ error: err.message });
        res.json({ message: "Schedule updated successfully" });
    });
});

//delete a schedule (Admins Only)
router.delete('/schedules/:id', (req, res) => {
    const { id } = req.params;

    //check if there are existing reservations for this schedule
    const checkReservationsQuery = "SELECT COUNT(*) AS count FROM Reservations WHERE schedule_id = ?";
    db.query(checkReservationsQuery, [id], (err, results) => {
        if (err) return res.status(500).json({ error: "Failed to check reservations." });

        if (results[0].count > 0) {
            return res.status(400).json({ error: "Cannot delete schedule with active reservations." });
        }

        //if no reservations exist, proceed with deletion
        const deleteScheduleQuery = "DELETE FROM ProgramSchedules WHERE schedule_id = ?";
        db.query(deleteScheduleQuery, [id], (err, results) => {
            if (err) return res.status(500).json({ error: "Failed to delete schedule." });
            if (results.affectedRows === 0) return res.status(404).json({ error: "Schedule not found." });

            res.json({ message: "Schedule deleted successfully." });
        });
    });
});


//create a new program (Admins Only)
router.post('/programs', (req, res) => {
    const { name, description } = req.body;
    const query = "INSERT INTO GymPrograms (name, description) VALUES (?, ?)";

    db.query(query, [name, description], (err, results) => {
        if (err) return res.status(500).json({ error: err.message });
        
        res.status(201).json({
            message: 'Program created successfully',
            program_id: results.insertId //return the new program_id
        });
    });
});

//update an existing program (Admins Only)
router.put('/programs/:id', (req, res) => {
    const { id } = req.params;
    const { name, description } = req.body;
    const query = "UPDATE GymPrograms SET name = ?, description = ? WHERE program_id = ?";

    db.query(query, [name, description, id], (err, results) => {
        if (err) return res.status(500).json({ error: err.message });
        res.json({ message: 'Program updated successfully' });
    });
});

//fetch all countries
router.get('/countries', async (req, res) => {
    try {
        const response = await axios.get('https://countriesnow.space/api/v0.1/countries');
        const countries = response.data.data.map(country => country.country);
        res.json(countries);
    } catch (error) {
        res.status(500).json({ error: 'Failed to fetch countries.' });
    }
});

//fetch cities for a specific country
router.get('/cities/:country', async (req, res) => {
    const { country } = req.params;
    try {
        const response = await axios.post('https://countriesnow.space/api/v0.1/countries/cities', {
            country: country
        });
        const cities = response.data.data;
        res.json(cities);
    } catch (error) {
        res.status(500).json({ error: 'Failed to fetch cities for the specified country.' });
    }
});

app.use('/', router);
app.listen(port, () => console.log(`Server running on http://localhost:${port}`));
module.exports = router;
