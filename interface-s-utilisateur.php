<?php
session_start();
?>


<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Trafic.CM - Utilisateur</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <style>
    :root {
      --bg: #ffffff;
      --bg-secondary: #25526d;
      --text: #f1f5f9;
      --accent: #1d3557;
      --warning: #ffffff;
      --danger: #049a3d;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
    body { background: var(--bg); color: var(--text); }
    header {
      padding: 2rem 1rem;
      text-align: center;
      background-color: var(--bg-secondary);
      box-shadow: 0 4px 6px rgba(0,0,0,0.4);
    }
    header h1 { font-size: 3rem; font-weight: 700; color: white; }
    main {
      padding: 2rem;
      max-width: 1200px;
      margin: auto;
    }
    .map-container {
      height: 500px;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
      margin-bottom: 2rem;
    }
    #map { width: 100%; height: 100%; }
    .section {
      margin-bottom: 2rem;
      background: var(--bg-secondary);
      padding: 1.5rem;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.3);
    }
    .section h2 {
      font-size: 1.5rem;
      margin-bottom: 1rem;
      color: var(--warning);
    }
    .button-group {
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
      margin-top: 1rem;
    }
    button {
      padding: 0.8rem 1.5rem;
      font-size: 1rem;
      font-weight: 600;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      color: rgb(248, 219, 1);
      transition: all 0.3s ease;
    }
    .signal-btn { background: linear-gradient(to right, #b75151, #700606); }
    .signal-btn:hover { background: linear-gradient(to right, #db8102, #db8102); }
    .urgence-btn { background: var(--danger); }
    .urgence-btn:hover { background: #00b803; }
    input {
      width: 100%;
      padding: 1rem;
      margin-top: 1rem;
      border-radius: 10px;
      border: 1px solid var(--accent);
    }
    ul { list-style: none; padding: 0; }
    li {
      background-color: rgba(255, 255, 255, 0.08);
      margin-bottom: 0.5rem;
      padding: 0.75rem 1rem;
      border-left: 4px solid var(--accent);
      border-radius: 8px;
    }
    footer {
      padding: 2rem;
      text-align: center;
      background: var(--bg-secondary);
      color: white;
    }
    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      25% { transform: translateX(-5px); }
      50% { transform: translateX(5px); }
      75% { transform: translateX(-5px); }
    }
    .animate-shake {
      animation: shake 0.5s;
    }
    .disabled {
      pointer-events: none;
      opacity: 0.5;
    }
  </style>
</head>
<body>
  <header>
    <h1>🚦 Trafic.CM - Interface S-Utilisateur</h1>
  </header>

  <main>
    <div class="map-container">
      <div id="map"></div>
    </div>

    <div class="section" id="incident-section">
      <h2>🚨 Signaler un incident</h2>
      <div class="button-group">
        <button class="signal-btn" onclick="signalerDepuisPosition('Embouteillage')">🚦 Embouteillage</button>
        <button class="signal-btn" onclick="signalerDepuisPosition('Accident')">🚗 Accident</button>
        <button class="signal-btn" onclick="signalerDepuisPosition('Inondation')">🌊 Inondation</button>
        <button class="signal-btn" onclick="signalerDepuisPosition('Voiture en feu')">🔥 Voiture en feu</button>
      </div>
    </div>

    <div class="section">
      <h2>📞 Services d'urgence</h2>
      <div class="button-group">
        <button class="urgence-btn" onclick="appeler('Ambulance', '112')">🚑 Ambulance - 112</button>
        <button class="urgence-btn" onclick="appeler('Police', '117')">👮 Police - 117</button>
        <button class="urgence-btn" onclick="appeler('Pompier', '118')">🚒 Pompier - 118</button>
        <button class="urgence-btn" onclick="appeler('Gendarmerie', '119')">🛡️ Gendarmerie - 119</button>
      </div>
    </div>

    <div class="section">
      <h2>📍 Historique des signalements</h2>
      <ul id="listeSignalements"></ul>
      <button class="signal-btn" onclick="reinitialiserHistorique(event)">🔄 Rafraîchir</button>
    </div>

    <div class="section">
      <h2>🗺️ Calculer un itinéraire</h2>
      <input type="text" id="depart" placeholder="Départ : rue ou lieu">
      <input type="text" id="arrivee" placeholder="Arrivée : rue ou lieu">
      <button class="signal-btn" onclick="rechercherItineraire()">🚘 Calculer l’itinéraire</button>
    </div>
  </main>

  <footer>
    &copy; 2025 Trafic.CM | Tous droits réservés
  </footer>

  <audio id="sound-bell" src="https://www.soundjay.com/buttons/sounds/bell-ring-01.mp3" preload="auto"></audio>

  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script>
    const map = L.map('map').setView([3.848, 11.502], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    let signalements = JSON.parse(localStorage.getItem("signalements")) || [];
    let marqueurs = [];

    function appeler(service, numero) {
      alert(`Appel d'urgence vers ${service} (${numero})`);
      window.location.href = `tel:${numero}`;
    }

    function signalerDepuisPosition(type) {
      if (signalements.length >= 5) return;

      navigator.geolocation.getCurrentPosition(pos => {
        const { latitude, longitude } = pos.coords;
        const nouveau = {
          type,
          description: "Signalement automatique",
          date: new Date().toLocaleString(),
          coords: [latitude.toFixed(5), longitude.toFixed(5)]
        };
        signalements.push(nouveau);
        localStorage.setItem("signalements", JSON.stringify(signalements));
        updateHistorique();

        const marker = L.marker([latitude, longitude])
          .addTo(map)
          .bindPopup(`${type} : ${nouveau.description}`)
          .openPopup();
        marqueurs.push(marker);

        document.getElementById("sound-bell").play();

        if (signalements.length >= 5) {
          document.getElementById("incident-section").classList.add("disabled");
        }
      }, () => alert("Position actuelle non accessible"));
    }

    function updateHistorique() {
      const liste = document.getElementById("listeSignalements");
      liste.innerHTML = '';
      signalements.forEach(sig => {
        const li = document.createElement('li');
        li.textContent = `${sig.date} - ${sig.type} (${sig.description}) à (${sig.coords.join(', ')})`;
        liste.appendChild(li);
      });
    }

    function reinitialiserHistorique(event) {
      signalements = [];
      localStorage.removeItem("signalements");
      updateHistorique();
      marqueurs.forEach(m => map.removeLayer(m));
      marqueurs = [];

      document.getElementById("incident-section").classList.remove("disabled");

      const son = document.getElementById("sound-bell");
      if (son) son.play();

      const btn = event.target;
      btn.classList.add("animate-shake");
      setTimeout(() => btn.classList.remove("animate-shake"), 500);
    }

    async function rechercherItineraire() {
      const depart = document.getElementById("depart").value;
      const arrivee = document.getElementById("arrivee").value;
      if (!depart || !arrivee) return alert("Veuillez remplir les deux champs.");

      try {
        const resDep = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(depart)}`);
        const depData = await resDep.json();
        const resArr = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(arrivee)}`);
        const arrData = await resArr.json();

        if (depData.length && arrData.length) {
          const start = [parseFloat(depData[0].lat), parseFloat(depData[0].lon)];
          const end = [parseFloat(arrData[0].lat), parseFloat(arrData[0].lon)];

          const routeRes = await fetch(`https://router.project-osrm.org/route/v1/driving/${start[1]},${start[0]};${end[1]},${end[0]}?overview=full&geometries=geojson`);
          const routeData = await routeRes.json();

          const coords = routeData.routes[0].geometry.coordinates.map(([lon, lat]) => [lat, lon]);
          const duration = routeData.routes[0].duration;
          const minutes = Math.round(duration / 60);
          const heures = Math.floor(minutes / 60);
          const resteMinutes = minutes % 60;

          L.polyline(coords, { color: 'lime', weight: 5 }).addTo(map);
          map.fitBounds(coords);

          L.marker(start).addTo(map).bindPopup("Départ").openPopup();
          L.marker(end).addTo(map).bindPopup(`Arrivée<br>Durée : ${heures}h ${resteMinutes}min`);
        } else {
          alert("Adresse(s) introuvable(s)");
        }
      } catch (err) {
        console.error(err);
        alert("Erreur lors du calcul d’itinéraire");
      }
    }

    updateHistorique();
    if (signalements.length >= 5) {
      document.getElementById("incident-section").classList.add("disabled");
    }
  </script>
</body>
</html>
