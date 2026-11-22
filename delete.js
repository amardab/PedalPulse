
document.getElementById("deleteTripForm").addEventListener("submit", async function (event) {
    event.preventDefault();

    const rideId = document.getElementById("ride_id").value.trim();
    const message = document.getElementById("deleteMessage");

    if (!rideId) {
        message.textContent = "Please enter a Ride ID.";
        return;
    }

    // Ask the user to confirm before deleting.
    if (!confirm("Are you sure you want to delete this trip?")) {
        return;
    }

    message.textContent = "Deleting trip...";

    try {
        const response = await fetch("delete_trip.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ ride_id: rideId })
        });

        const result = await response.json();

        message.textContent = result.message;

        if (response.ok && result.success) {
            document.getElementById("deleteTripForm").reset();
        }
    } catch (error) {
        message.textContent = "Unable to delete trip. Please try again.";
        console.error(error);
    }
});
