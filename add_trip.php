<?php
header('Content-Type: application/json');
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$ride_id = trim($input['ride_id'] ?? '');
$rideable_type = trim($input['rideable_type'] ?? '');
$started_at = trim($input['started_at'] ?? '');
$ended_at = trim($input['ended_at'] ?? '');
$duration_minutes = $input['duration_minutes'] ?? null;
$start_station_id = trim($input['start_station_id'] ?? '');
$end_station_id = trim($input['end_station_id'] ?? '');
$member_casual = trim($input['member_casual'] ?? '');

if ($ride_id === '' || $rideable_type === '' || $started_at === '' || $ended_at === '' ||
    $duration_minutes === null || $start_station_id === '' || $end_station_id === '' || $member_casual === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

try {
    $checkStmt = $pdo->prepare("SELECT 1 FROM trips WHERE ride_id = :ride_id");
    $checkStmt->execute([':ride_id' => $ride_id]);
    if ($checkStmt->fetch()) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => "Error: Ride ID '$ride_id' already exists in the database."]);
        exit;
    }

    $stationStmt = $pdo->prepare("SELECT station_id FROM stations WHERE station_id IN (:start_id, :end_id)");
    $stationStmt->execute([':start_id' => $start_station_id, ':end_id' => $end_station_id]);
    $foundStations = $stationStmt->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array($start_station_id, $foundStations, true)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Error: Start Station ID '$start_station_id' does not exist."]);
        exit;
    }
    if (!in_array($end_station_id, $foundStations, true)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Error: End Station ID '$end_station_id' does not exist."]);
        exit;
    }

    $sql = "INSERT INTO trips (ride_id, rideable_type, started_at, ended_at, duration_minutes, start_station_id, end_station_id, member_casual)
            VALUES (:ride_id, :rideable_type, :started_at, :ended_at, :duration_minutes, :start_station_id, :end_station_id, :member_casual)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':ride_id' => $ride_id,
        ':rideable_type' => $rideable_type,
        ':started_at' => $started_at,
        ':ended_at' => $ended_at,
        ':duration_minutes' => $duration_minutes,
        ':start_station_id' => $start_station_id,
        ':end_station_id' => $end_station_id,
        ':member_casual' => $member_casual
    ]);

    echo json_encode(['success' => true, 'message' => 'Trip added successfully!']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>