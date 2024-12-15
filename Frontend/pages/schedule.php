<?php
session_start();
// Check if user is logged in 
if (!isset($_SESSION['user_id'])) {
    header('Location: ../public/login.php');
    exit();
}

$pageTitle = "Photographer Schedule";
include 'dbconnection.php';

$user_id = $_SESSION['user_id'];

try {
    // Improved query with more context and joined tables
    $schedule_query = "
        SELECT 
            'Booking' AS event_type, 
            b.event_date AS start_date, 
            b.event_time AS start_time, 
            b.location AS location, 
            s.service_name AS event_title,
            u.full_name AS client_name,
            b.status AS booking_status
        FROM bookings_og b 
        JOIN services_og s ON b.service_id = s.service_id 
        JOIN users_og u ON b.user_id = u.user_id
        WHERE b.photographer_id = ? 
        
        UNION 
        
        SELECT 
            'Assignment' AS event_type, 
            a.due_date AS start_date, 
            '00:00:00' AS start_time, 
            a.description AS location,
            a.assignment_title AS event_title,
            NULL AS client_name,
            a.status AS booking_status
        FROM assignments_og a 
        WHERE a.photographer_id = ?
        ORDER BY start_date
    ";

    $schedule_stmt = $conn->prepare($schedule_query);
    $schedule_stmt->bind_param("ii", $user_id, $user_id);
    $schedule_stmt->execute();
    $schedule_result = $schedule_stmt->get_result();

    $events = [];
    if ($schedule_result->num_rows === 0) {
        // Handle no events scenario
        $events = [
            [
                'title' => 'No scheduled events',
                'start' => date('Y-m-d'),
                'color' => 'grey'
            ]
        ];
    } else {
        while ($event = $schedule_result->fetch_assoc()) {
            $eventColor = 'blue'; // Default color
            if ($event['event_type'] === 'Booking') {
                $eventColor = match($event['booking_status']) {
                    'confirmed' => 'green',
                    'pending' => 'orange',
                    'in_progress' => 'purple',
                    'completed' => 'gray',
                    'cancelled' => 'red',
                    default => 'blue'
                };
            } else {
                $eventColor = match($event['booking_status']) {
                    'pending' => 'orange',
                    'in_progress' => 'purple',
                    'completed' => 'gray',
                    'overdue' => 'red',
                    default => 'blue'
                };
            }

            $events[] = [
                'title' => $event['event_title'] . ' (' . $event['event_type'] . ')',
                'start' => $event['start_date'] . 'T' . $event['start_time'],
                'location' => $event['location'],
                'client' => $event['client_name'],
                'color' => $eventColor,
                'status' => $event['booking_status']
            ];
        }
    }

    $schedule_stmt->close();
    $conn->close();

} catch (Exception $e) {
    error_log("Schedule fetch error: " . $e->getMessage());
    $events = [
        [
            'title' => 'Error loading schedule',
            'start' => date('Y-m-d'),
            'color' => 'red'
        ]
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Photographer Schedule</title>
    <link rel="stylesheet" href="../Frontend/styles/dashboard.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js"></script>
    <style>
        #calendar {
            max-width: 1100px;
            margin: 0 auto;
            padding: 20px;
            background: #f4f4f4;
            border-radius: 8px;
        }
    </style>
</head>
<body>
<div class="dashboard-container">
    <div class="main-content">
        <h1>My Photography Schedule</h1>
        <div id="calendar"></div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            events: <?php echo json_encode($events); ?>,
            eventDisplay: 'block',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            eventClick: function(info) {
                var event = info.event;
                var detailsHtml = `
                    <strong>Event:</strong> ${event.title}<br>
                    <strong>Date:</strong> ${event.start.toLocaleString()}<br>
                    <strong>Location:</strong> ${event.extendedProps.location || 'Not specified'}<br>
                    <strong>Client:</strong> ${event.extendedProps.client || 'N/A'}<br>
                    <strong>Status:</strong> ${event.extendedProps.status || 'Unknown'}
                `;
                
                alert(detailsHtml);
            },
            height: 'auto',
            eventColor: 'blue',
            displayEventTime: true
        });

        calendar.render();
    });
</script>
</body>
</html>