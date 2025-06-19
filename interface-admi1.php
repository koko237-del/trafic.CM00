<?php
session_start();
?>


<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Trafic.CM - Administratif</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <style>
    :root {
      --accent: #0284c7;
      --bg-light: #f0f9ff;
      --bg-dark: #1e293b;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      background: linear-gradient(to bottom right, #e0f2fe, #bae6fd);
      transition: background 0.3s ease;
    }

    body.dark-mode {
      background: linear-gradient(to bottom right, #0f172a, #1e293b);
      color: white;
    }

    header {
      background: var(--bg-dark);
      color: white;
      padding: 20px;
      text-align: center;
      font-size: 26px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.4);
      position: relative;
    }

    .top-right-controls {
      position: absolute;
      right: 20px;
      top: 20px;
      display: flex;
      gap: 12px;
    }

    .top-right-controls button {
      background: #f1f5f9;
      border: 2px solid #cbd5e1;
      border-radius: 100%;
      padding: 8px 12px;
      font-size: 16px;
      cursor: pointer;
      transition: 0.3s;
    }

    .top-right-controls button:hover {
      background: #469fda;
      border-color: var(--accent);
    }
    .top-right-controls a {
      background: #f1f5f9;
      border: 2px solid #cbd5e1;
      border-radius: 100%;
      padding: 8px 12px;
      font-size: 16px;
      cursor: pointer;
      transition: 0.3s;
    }

    .top-right-controls a:hover {
      background: #469fda;
      border-color: var(--accent);
    }

    #map {
      height: 400px;
      width: 80%;
      margin: 20px auto;
      border-radius: 15px;
      box-shadow: 0 0 20px rgba(0,0,0,0.2);
      border: 4px solid var(--bg-dark);
    }

    .controls {
      padding: 20px;
      background: white;
      display: flex;
      flex-direction: column;
      gap: 20px;
      align-items: center;
      box-shadow: 0 -4px 8px rgba(0,0,0,0.1);
    }

    body.dark-mode .controls {
      background: #0f172a;
    }

    .toggle-buttons {
      display: flex;
      gap: 15px;
    }

    .toggle-buttons button {
      padding: 12px 24px;
      border-radius: 8px;
      border: 2px solid #cbd5e1;
      background-color: #f1f5f9;
      cursor: pointer;
      font-weight: bold;
      font-size: 16px;
      transition: 0.3s;
    }

    .toggle-buttons button:hover {
      background-color: #e0f2fe;
      border-color: var(--accent);
    }

    .toggle-buttons button.active {
      border-color: #1e293b;
      background-color: #fde68a;
      box-shadow: 0 0 8px rgba(0,0,0,0.2);
    }

    .emergency-contacts {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 12px;
    }

    .emergency-contacts button {
      background-color: var(--bg-dark);
      color: white;
      border: none;
      padding: 10px 18px;
      border-radius: 6px;
      cursor: pointer;
      font-size: 15px;
      transition: 0.3s;
    }

    .emergency-contacts button:hover {
      background-color: #334155;
    }

    footer {
      text-align: center;
      padding: 10px;
      background: #f1f5f9;
      font-size: 14px;
      color: #334155;
    }

    #overlay {
      display: none;
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(0,0,0,0.4);
      z-index: 900;
    }

    #reportFormPopup {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%) scale(0.8);
      width: 330px;
      background: white;
      border-radius: 14px;
      box-shadow: 0 20px 40px rgba(0,0,0,0.3);
      z-index: 1000;
      display: none;
      animation: zoomIn 0.3s ease forwards;
    }

    @keyframes zoomIn {
      from { transform: translate(-50%, -50%) scale(0.7); opacity: 0; }
      to { transform: translate(-50%, -50%) scale(1); opacity: 1; }
    }

    .popup-header {
      background-color: var(--accent);
      color: white;
      padding: 14px;
      text-align: center;
      font-weight: bold;
      font-size: 18px;
      border-top-left-radius: 14px;
      border-top-right-radius: 14px;
    }

    .popup-body {
      padding: 16px;
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .popup-body textarea {
      resize: none;
      height: 100px;
      border: 1px solid #cbd5e1;
      border-radius: 8px;
      padding: 10px;
      font-size: 14px;
    }

    .popup-buttons {
      display: flex;
      justify-content: space-between;
      gap: 10px;
    }

    .popup-btn {
      flex: 1;
      padding: 10px;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      font-size: 15px;
      cursor: pointer;
      background-color: var(--accent);
      color: white;
      transition: 0.3s ease;
    }

    .popup-btn.cancel {
      background-color: #e11d48;
    }

    #progressBarContainer {
      width: 100%;
      height: 6px;
      background-color: #e2e8f0;
      border-radius: 4px;
      margin-top: 12px;
      overflow: hidden;
      display: none;
    }

    #progressBar {
      height: 100%;
      width: 0%;
      background: linear-gradient(to right, #0ea5e9, #22d3ee);
      transition: width 1s ease;
    }
  </style>
</head>
<body>
  <header>
    <h1>Trafic.CM - Espace Administratif</h1>
    <p>Signalez les embouteillages, routes fermées ou manifestations</p>
    <div class="top-right-controls">
      <button onclick="toggleTheme()">🌓</button>
      <button onclick="location.reload()">🔄</button>
      <a href="tableau de bore.php">📊</a>
    </div>
  </header>

  <div id="map"></div>

  <div class="controls">
    <div class="toggle-buttons">
      <button id="btn-embouteillage" onclick="toggleMode('embouteillage')">🚗 Embouteillage</button>
      <button id="btn-route" onclick="toggleMode('route')">🛑 Route barrée</button>
      <button id="btn-manifestation" onclick="toggleMode('manifestation')">📢 Manifestation</button>
    </div>

    <h3>Contacts d'urgence</h3>
    <div class="emergency-contacts">
      <button onclick="callNumber('117')">👮‍♂️ Police</button>
      <button onclick="callNumber('118')">👮 Gendarmerie</button>
      <button onclick="callNumber('119')">🚒 Pompiers</button>
      <button onclick="callNumber('112')">🚑 Ambulance</button>
    </div>
  </div>

  <div id="overlay"></div>
  <div id="reportFormPopup">
    <div class="popup-header">📝 Nouveau Signalement</div>
    <div class="popup-body">
      <label for="reportDescription">Description :</label>
      <textarea id="reportDescription" placeholder="Ex : gros embouteillage à Douala..."></textarea>
      <div class="popup-buttons">
        <button class="popup-btn cancel" onclick="closeReportPopup()">❌ Annuler</button>
        <button class="popup-btn" onclick="submitReport()">✅ Valider</button>
      </div>
      <div id="progressBarContainer"><div id="progressBar"></div></div>
    </div>
  </div>

  <footer>
    &copy; 2025 Trafic.CM | Tous droits réservés
  </footer>

  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script>
    let map = L.map('map').setView([3.848, 11.502], 7);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        (position) => {
          map.setView([position.coords.latitude, position.coords.longitude], 13);
        },
        () => alert("Impossible d'obtenir votre position actuelle.")
      );
    }

    let currentMode = null;
    let currentLatLng = null;

    function toggleMode(type) {
      currentMode = type;
      document.querySelectorAll('.toggle-buttons button').forEach(btn => btn.classList.remove('active'));
      const btn = document.getElementById('btn-' + type);
      if (btn) btn.classList.add('active');
    }

    function callNumber(num) {
      window.location.href = `tel:${num}`;
    }

    map.on('click', function(e) {
      if (!currentMode) return;
      currentLatLng = e.latlng;
      document.getElementById('overlay').style.display = 'block';
      document.getElementById('reportFormPopup').style.display = 'block';
    });

    function closeReportPopup() {
      document.getElementById('reportFormPopup').style.display = 'none';
      document.getElementById('overlay').style.display = 'none';
      document.getElementById('reportDescription').value = '';
      document.querySelectorAll('.toggle-buttons button').forEach(btn => btn.classList.remove('active'));
      currentMode = null;
    }

    function submitReport() {
      if (!currentMode || !currentLatLng) return;

      const description = document.getElementById('reportDescription').value || '-';
      const latlng = currentLatLng;
      const type = currentMode;

      addToMap(type, description, latlng);

      document.getElementById('progressBarContainer').style.display = 'block';
      document.getElementById('progressBar').style.width = '0%';

      setTimeout(() => {
        document.getElementById('progressBar').style.width = '100%';
      }, 50);

      setTimeout(() => {
        closeReportPopup();
        document.getElementById('progressBarContainer').style.display = 'none';
      }, 1000);
    }

    function addToMap(type, description, coords) {
      let iconUrl = {
        embouteillage: 'https://cdn-icons-png.flaticon.com/512/252/252025.png',
        route: 'https://cdn-icons-png.flaticon.com/512/2991/2991179.png',
        manifestation: 'https://cdn-icons-png.flaticon.com/512/1055/1055646.png'
      }[type];

      const icon = L.icon({
        iconUrl,
        iconSize: [30, 30],
        iconAnchor: [15, 30],
        popupAnchor: [0, -30]
      });

      L.marker([coords.lat, coords.lng], { icon })
        .addTo(map)
        .bindPopup(`<strong>${type}</strong><br>${description}`);
    }

    function toggleTheme() {
      document.body.classList.toggle('dark-mode');
    }

    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') closeReportPopup();
    });
  </script>
</body>
</html>
