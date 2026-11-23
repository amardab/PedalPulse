<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Method not allowed.";
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$ride_id = trim($data['ride_id'] ?? '');
if ($ride_id === '') {
    http_response_code(400);
    echo "Ride ID is required.";
    exit;
}

$rideable_type = ($data['rideable_type'] ?? '') !== '' ? trim($data['rideable_type']) : null;
$started_at = ($data['started_at'] ?? '') !== '' ? trim($data['started_at']) : null;
$ended_at = ($data['ended_at'] ?? '') !== '' ? trim($data['ended_at']) : null;
$duration_minutes = ($data['duration_minutes'] ?? '') !== '' ? (float)$data['duration_minutes'] : null;
$start_station_id = ($data['start_station_id'] ?? '') !== '' ? trim($data['start_station_id']) : null;
$end_station_id = ($data['end_station_id'] ?? '') !== '' ? trim($data['end_station_id']) : null;
$member_casual = ($data['member_casual'] ?? '') !== '' ? trim($data['member_casual']) : null;

if ($rideable_type === null && $started_at === null && $ended_at === null &&
    $duration_minutes === null && $start_station_id === null &&
    $end_station_id === null && $member_casual === null) {
    http_response_code(400);
    echo "Please provide at least one field to update.";
    exit;
}

try {
    $sql = "
        UPDATE trips
        SET
            rideable_type = COALESCE(CAST(:rideable_type AS VARCHAR), rideable_type),
            started_at = COALESCE(CAST(:started_at AS TIMESTAMP), started_at),
            ended_at = COALESCE(CAST(:ended_at AS TIMESTAMP), ended_at),
            duration_minutes = COALESCE(CAST(:duration_minutes AS DOUBLE PRECISION), duration_minutes),
            start_station_id = COALESCE(CAST(:start_station_id AS VARCHAR), start_station_id),
            end_station_id = COALESCE(CAST(:end_station_id AS VARCHAR), end_station_id),
            member_casual = COALESCE(CAST(:member_casual AS VARCHAR), member_casual)
        WHERE ride_id = :ride_id
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':rideable_type' => $rideable_type,
        ':started_at' => $started_at,
        ':ended_at' => $ended_at,
        ':duration_minutes' => $duration_minutes,
        ':start_station_id' => $start_station_id,
        ':end_station_id' => $end_station_id,
        ':member_casual' => $member_casual,
        ':ride_id' => $ride_id
    ]);

    if ($stmt->rowCount() > 0) {
        echo "Trip updated successfully!";
    } else {
        echo "Trip not found or no changes made.";
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo "Database error: " . $e->getMessage();
}
?>