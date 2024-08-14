<!DOCTYPE html>
<html>
<head>
    <title>Task Reminder</title>
</head>
<body>
    <h1>Task Reminder</h1>
    <p>Hello {{ $task->user->name }},</p>
    <p>This is a reminder that your task titled "{{ $task->title }}" has been marked as "In Progress" for more than 24 hours.</p>
    <p>Please ensure to complete the task as soon as possible.</p>
    <p>Thank you!</p>
</body>
</html>

