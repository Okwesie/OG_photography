<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../public/login.php');
    exit();
}

$pageTitle = "Photographer Schedule";
include 'dbconnection.php';
include 'sidebar.php'; // Include the sidebar

// Render the sidebar for the photographer
renderSidebar($_SESSION['role']);

$user_id = $_SESSION['user_id'];

try {
    $schedule_query = "
        SELECT 'Booking' AS event_type, 
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
        SELECT 'Assignment' AS event_type, 
               a.due_date AS start_date, 
               '00:00:00' AS start_time, 
               a.description AS location,
               a.assignment_title AS event_title,
               NULL AS client_name,
               a.status AS booking_status
        FROM assignments_og a 
        WHERE a.photographer_id = ?
    ";

    $schedule_stmt = $conn->prepare($schedule_query);
    $schedule_stmt->bind_param("ii", $user_id, $user_id);
    $schedule_stmt->execute();
    $schedule_result = $schedule_stmt->get_result();

    $events = [];
    while ($event = $schedule_result->fetch_assoc()) {
        $events[] = [
            'title' => $event['event_title'],
            'start' => $event['start_date'] . 'T' . $event['start_time'],
            'location' => $event['location'],
            'client' => $event['client_name'],
            'status' => $event['booking_status']
        ];
    }

    $schedule_stmt->close();
    $conn->close();

} catch (Exception $e) {
    error_log("Schedule fetch error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Photographer Schedule</title>
    <link rel="stylesheet" href="photographer_global.css">

    <style>
        .calendar {
            max-width: 1100px;
            margin: 0 auto;
            font-family: Arial, sans-serif;
        }
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background-color: #f4f4f4;
        }
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
            text-align: center;
        }
        .calendar-day {
            border: 1px solid #ddd;
            padding: 10px;
            min-height: 100px;
            position: relative;
        }
        .event {
            background-color: #3498db;
            color: white;
            margin: 2px 0;
            padding: 3px;
            border-radius: 3px;
            font-size: 0.8em;
        }
        .event.confirmed { background-color: #2ecc71; }
        .event.pending { background-color: #f39c12; }
        .event.in_progress { background-color: #9b59b6; }
        .event.cancelled { background-color: #e74c3c; }
    </style>
</head>
<body>
<div class="main-content">
    <div class="calendar">
        <div class="calendar-header">
            <button id="prevMonth">&larr; Previous</button>
            <h2 id="currentMonth">Month</h2>
            <button id="nextMonth">Next &rarr;</button>
        </div>
        <div class="calendar-grid" id="calendarGrid"></div>
    </div>

    <script>
        class PhotographerCalendar {
            constructor(events) {
                this.events = events;
                this.currentDate = new Date();
                this.calendarGrid = document.getElementById('calendarGrid');
                this.currentMonthDisplay = document.getElementById('currentMonth');

                this.setupEventListeners();
                this.renderCalendar();
            }

            setupEventListeners() {
                document.getElementById('prevMonth').addEventListener('click', () => this.changeMonth(-1));
                document.getElementById('nextMonth').addEventListener('click', () => this.changeMonth(1));
            }

            changeMonth(change) {
                this.currentDate.setMonth(this.currentDate.getMonth() + change);
                this.renderCalendar();
            }

            renderCalendar() {
                this.calendarGrid.innerHTML = '';
                this.currentMonthDisplay.textContent = this.currentDate.toLocaleString('default', { month: 'long', year: 'numeric' });
                const firstDay = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), 1);
                const lastDay = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 0);

                for (let i = 0; i < firstDay.getDay(); i++) {
                    this.calendarGrid.appendChild(this.createDayElement(null));
                }

                for (let day = 1; day <= lastDay.getDate(); day++) {
                    const dayElement = this.createDayElement(day);
                    this.addEventsToDay(dayElement, day);
                    this.calendarGrid.appendChild(dayElement);
                }
            }

            createDayElement(day) {
                const dayElement = document.createElement('div');
                dayElement.classList.add('calendar-day');
                if (day) {
                    dayElement.innerHTML = `<strong>${day}</strong>`;
                }
                return dayElement;
            }

            addEventsToDay(dayElement, day) {
                if (!day) return;

                const currentMonth = this.currentDate.getMonth() + 1;
                const currentYear = this.currentDate.getFullYear();

                const dayEvents = this.events.filter(event => {
                    const eventDate = new Date(event.start);
                    return eventDate.getFullYear() === currentYear && 
                           eventDate.getMonth() + 1 === currentMonth && 
                           eventDate.getDate() === day;
                });

                dayEvents.forEach(event => {
                    const eventElement = document.createElement('div');
                    eventElement.classList.add('event', event.status || '');
                    eventElement.textContent = `${event.title}`;
                    eventElement.title = `Location: ${event.location || 'N/A'}\nClient: ${event.client || 'N/A'}`;
                    dayElement.appendChild(eventElement);
                });
            }
        }

        const events = <?php echo json_encode($events); ?>;

        document.addEventListener('DOMContentLoaded', () => {
            new PhotographerCalendar(events);
        });
    </script>
    </div>
</body>
</html>
