<!DOCTYPE html>
<html>
<body>
    <h3>Interview Scheduled</h3>

    <p><strong>Title:</strong> {{ $event['title'] }}</p>
    <p><strong>Date:</strong> {{ $event['date'] }}</p>
    <p><strong>Time:</strong> {{ $event['time'] }}</p>
    <p><strong>Location:</strong> {{ $event['location'] }}</p>

    <br>

    <!-- Google Calendar Auto-Add -->
    <a href="{{ $event['google_link'] }}" target="_blank">
        Add to Google Calendar
    </a>
</body>
</html>
