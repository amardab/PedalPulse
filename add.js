document.getElementById("addTripForm").addEventListener("submit", async function (event) {
    event.preventDefault();

    const rideId = document.getElementById("ride_id").value.trim();
    const rideableType = document.getElementById("rideable_type").value;
    const startedAtRaw = document.getElementById("started_at").value;
    const endedAtRaw = document.getElementById("ended_at").value;
    const durationMinutes = document.getElementById("duration_minutes").value;
    const startStationId = document.getElementById("start_station_id").value.trim();
    const endStationId = document.getElementById("end_station_id").value.trim();
    const memberCasual = document.getElementById("member_casual").value;
    const message = document.getElementById("addMessage");

    if (!rideId || !rideableType || !startedAtRaw || !endedAtRaw || !durationMinutes || !startStationId || !endStationId || !memberCasual) {
        message.textContent = "Please complete all required fields.";
        return;
    }

    const startedAt = startedAtRaw.replace("T", " ") + ":00";
    const endedAt = endedAtRaw.replace("T", " ") + ":00";

    if (new Date(endedAtRaw) <= new Date(startedAtRaw)) {
        message.textContent = "End time must be after start time.";
        return;
    }

    message.textContent = "Adding trip...";

    const payload = {
        ride_id: rideId,
        rideable_type: rideableType,
        started_at: startedAt,
        ended_at: endedAt,
        duration_minutes: parseFloat(durationMinutes),
        start_station_id: startStationId,
        end_station_id: endStationId,
        member_casual: memberCasual
    };

    try {
        const response = await fetch("add_trip.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(payload)
        });

        const result = await response.json();
        message.textContent = result.message;

        if (response.ok && result.success) {
            document.getElementById("addTripForm").reset();
        }
    } catch (error) {
        message.textContent = "Unable to add trip. Please try again.";
        console.error(error);
    }
});