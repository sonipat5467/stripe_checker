// websocket.js - placeholder content for demonstration.
// websocket.js
const logContainer = document.getElementById("live-logs");

const socket = new WebSocket("ws://your-server-ip:8080");

socket.onopen = function () {
    console.log("✅ WebSocket connection established.");
};

socket.onmessage = function (event) {
    const log = JSON.parse(event.data);
    const row = document.createElement("tr");
    row.innerHTML = `
        <td>${log.id}</td>
        <td>${log.card}</td>
        <td>${log.status}</td>
        <td>${log.bank}</td>
        <td>${log.country}</td>
        <td>${log.ip}</td>
        <td>${log.created_at}</td>
    `;
    logContainer.prepend(row); // Newest logs on top
};

socket.onerror = function (error) {
    console.error("❌ WebSocket Error:", error);
};

socket.onclose = function () {
    console.warn("⚠️ WebSocket closed.");
};
// websocket.js - placeholder content for demonstration.
