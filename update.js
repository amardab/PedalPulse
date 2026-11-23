document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("updateTripForm");
    const message = document.getElementById("updateMessage");

    form.addEventListener("submit", async function (e) {
        e.preventDefault();

        const rideId = document.getElementById("ride_id").value.trim();
        const startedAtRaw = document.getElementById("started_at").value;
        const endedAtRaw = document.getElementById("ended_at").value;

        if (!rideId) {
            message.textContent = "Please enter an existing Ride ID.";
            return;
        }

        const tripData = {
            ride_id: rideId,
            rideable_type: document.getElementById("rideable_type").value,
            started_at: startedAtRaw ? startedAtRaw.replace("T", " ") + ":00" : "",
            ended_at: endedAtRaw ? endedAtRaw.replace("T", " ") + ":00" : "",
            duration_minutes: document.getElementById("duration_minutes").value,
            start_station_id: document.getElementById("start_station_id").value.trim(),
            end_station_id: document.getElementById("end_station_id").value.trim(),
            member_casual: document.getElementById("member_casual").value
        };

        message.textContent = "Updating trip...";

        try {
            const response = await fetch("update.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(tripData)
            });

            const data = await response.text();
            message.textContent = data;
            alert(data);

            if (response.ok && data.includes("successfully")) {
                form.reset();
            }
        } catch (error) {
            message.textContent = "Update failed. Please try again.";
            alert("Update failed.");
            console.error(error);
        }
    });
});