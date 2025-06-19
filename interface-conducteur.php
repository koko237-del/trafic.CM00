<?php
session_start();
?>


<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Trafic.CM - Utilisateur</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <style>
    :root {
      --bg: #0f172a;
      --bg-secondary: #1e293b;
      --text: #f1f5f9;
      --accent: #3b82f6;
      --warning: #000000;
      --danger: #01ad32;
    }
    body.light {
      --bg: #f1f5f9;
      --bg-secondary: #e2e8f0;
      --text: #0f172a;
    }
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Inter', sans-serif;
    }
    body {
      background: var(--bg);
      color: var(--text);
      transition: background 0.3s, color 0.3s;
    }
    header {
      padding: 2rem 1rem;
      text-align: center;
      background-color: var(--bg-secondary);
      box-shadow: 0 4px 6px rgba(0,0,0,0.4);
      position: relative;
    }
    header h1 {
      font-size: 3rem;
      font-weight: 700;
      color: var(--text);
    }
    .theme-btn, .pdf-btn {
      position: absolute;
      top: 1rem;
      background: var(--accent);
      color: white;
      border: none;
      padding: 0.5rem 1rem;
      border-radius: 10px;
      cursor: pointer;
    }
    .theme-btn { right: 4rem; }
    .pdf-btn { left: 1rem; }

    .dropdown {
      position: absolute;
      top: 1rem;
      right: 8rem;
      display: inline-block;
    }
    
    .dropbtn {
      background-color: var(--accent);
      color: white;
      padding: 8px 10px;
      font-size: 14px;
      border: none;
      border-radius: 10px;
      cursor: pointer;
    }
    .dropdown-content {
      display: none;
      position: absolute;
      background-color: var(--bg-secondary);
      min-width: 200px;
      box-shadow: 0px 8px 16px rgba(0,0,0,0.3);
      z-index: 1000;
      border-radius: 10px;
      overflow: hidden;
    }
    .dropdown-content a {
      color: var(--text);
      padding: 12px 16px;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .dropdown-content a:hover {
      background-color: var(--accent);
      color: white;
    }
    .dropdown:hover .dropdown-content {
      display: block;
    }
    .dropdown:hover .dropbtn {
      background-color: #2563eb;
    }

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
      position: relative;
    }
    #map { width: 100%; height: 100%; }
    #popupForm {
      position: absolute;
      top: 10%;
      left: 50%;
      transform: translateX(-50%);
      background: var(--bg-secondary);
      padding: 1rem;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.3);
      display: none;
      z-index: 1000;
      width: 300px;
    }
    #popupForm textarea {
      width: 100%;
      padding: 0.8rem;
      border-radius: 8px;
      margin-bottom: 1rem;
      border: 1px solid var(--accent);
      background: rgba(255,255,255,0.05);
      color: var(--text);
    }
    #popupForm button {
      width: 100%;
      background: var(--accent);
      border: none;
      padding: 0.8rem;
      border-radius: 8px;
      color: white;
      font-weight: 600;
      cursor: pointer;
    }
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
      color: white;
      transition: all 0.3s ease;
    }
    .signal-btn { background: linear-gradient(to right, #cf1e1e, #cf1e1e); }
    .signal-btn:hover { background: linear-gradient(to right, #760606, #02752a); }
    .urgence-btn { background: var(--danger); }
    .urgence-btn:hover { background: #01ad32; }
    input, textarea {
      width: 100%;
      padding: 1rem;
      margin-top: 1rem;
      border-radius: 10px;
      border: 1px solid var(--accent);
      background: rgba(255,255,255,0.05);
      color: var(--text);
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
    #confirmSuppression {
      color: limegreen;
      text-align: center;
      font-weight: bold;
      margin-top: 1rem;
      display: none;
    }

    @keyframes tremblement {
      0%, 100% { transform: translateX(0); }
      25% { transform: translateX(-5px); }
      50% { transform: translateX(5px); }
      75% { transform: translateX(-5px); }
    }
    .tremble { animation: tremblement 0.4s ease; }

    /* Popup couleur */
    .leaflet-popup-content-wrapper {
      color: rgb(1, 110, 253);
      font-weight: bold;
    }
    .popup-embouteillage .leaflet-popup-content-wrapper { background-color: #cf1e1e; }
    .popup-accident .leaflet-popup-content-wrapper { background-color: orange; }
    .popup-inondation .leaflet-popup-content-wrapper { background-color: #0077ff; }
    .popup-incendie .leaflet-popup-content-wrapper { background-color: #ffd700; }
  </style>
</head>

<body>
  <header>
    <h1>🚦 Trafic.CM - Interface Conducteur</h1>
    <button class="theme-btn" onclick="toggleTheme()">🌓</button>
    <button class="pdf-btn" onclick="ouvrirPDF()">📘 Mitransport</button>
    <div class="dropdown">
      <button class="dropbtn">Menu</button>
        <div class="dropdown-content">
          <a href="https://www.google.com/maps/search/Auto+%C3%A9cole+italienne+(Mvan,+Yaound%C3%A9),+Mvan,+Yaound%C3%A9,+Cameroun/@3.8396248,11.5071955,13z?entry=ttu&g_ep=EgoyMDI1MDYwOS4xIKXMDSoASAFQAw%3D%3D" target="_blank">🚗 Auto-ecole</a>
        <a href="https://durrellmarket.com/categorie-produit/divers/pieces-automobile/" target="_blank">⚙️ Ventes pièces</a>
        <a href="https://ioverlander.com/places/465d888e-cd4b-4b33-81c0-f2cf36009d11"_blank">🛠️ Garage </a>
        <a href="https://camerounblog.com/code-de-la-route-cameroun-pdf/?utm_source=chatgpt.com" target="_blank">📘 Code de la Route</a>
        <a href="https://www.goafricaonline.com/cm/840927-cemex-visite-technique-automobiles-controles-technique-yaounde-cameroun?utm_source=chatgpt.com" target="_blank">🧾 Visite technique</a>
      </div>
    </div>
  </header>

  <main>
    <div class="map-container">
      <div id="map"></div>
      <div id="popupForm">
        <textarea id="incidentDesc" placeholder="Décrivez l'incident..."></textarea>
        <button onclick="validerIncident()">✅ Valider</button>
      </div>
    </div>

    <div class="section">
      <h2>🚨 Signaler un incident</h2>
      <div class="button-group">
        <button class="signal-btn" onclick="ouvrirForm('Embouteillage')">🚦Embouteillage (manuel)</button>
<button class="signal-btn" onclick="signalementAuto('Embouteillage')">🚦(auto)</button>

<button class="signal-btn" onclick="ouvrirForm('Accident')">🚗Accident (manuel)</button>
<button class="signal-btn" onclick="signalementAuto('Accident')">🚗(auto)</button>

<button class="signal-btn" onclick="ouvrirForm('Inondation')">🌊Inondation (manuel)</button>
<button class="signal-btn" onclick="signalementAuto('Inondation')">🌊(auto)</button>

<button class="signal-btn" onclick="ouvrirForm('incendie')">🔥incendie (manuel)</button>
<button class="signal-btn" onclick="signalementAuto('incendie')">🔥(auto)</button>

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
      <div class="button-group">
        <button class="signal-btn" onclick="updateHistorique()">🔄 Historique</button>
        <button class="urgence-btn" onclick="supprimerHistorique(event)">🗑️ Supprimer</button>
      </div>
      <div id="confirmSuppression">✅ Historique supprimé avec succès !</div>
    </div>

    <div class="section">
      <h2>🗺️ Calculer un itinéraire</h2>
      <input type="text" id="depart" placeholder="Départ : rue ou lieu">
      <input type="text" id="arrivee" placeholder="Arrivée : rue ou lieu">
      <button class="signal-btn" onclick="rechercherItineraire()">🚘 Calculer l’itinéraire</button>
      <div id="tempsItineraire" style="margin-top: 1rem; font-weight: bold;"></div>

    </div>
  </main>

  <footer>
    &copy; 2025 Trafic.CM | Tous droits réservés
  </footer>

  <audio id="validateSound" src="https://assets.mixkit.co/sfx/preview/mixkit-positive-interface-beep-221.mp3" preload="auto"></audio>
  <audio id="deleteSound" src="https://assets.mixkit.co/sfx/preview/mixkit-trash-can-empty-2333.mp3" preload="auto"></audio>

  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script>

    function nombreTotalSignalements() {
  return signalements.length;
}

    function compterSignalementsParType(type) {
  return signalements.filter(s => s.type.toLowerCase() === type.toLowerCase()).length;
}

    const map = L.map('map').setView([3.848, 11.502], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    let signalements = [];
    let typeActuel = "";
    let selectionMode = false;
    let tempMarker = null;
    let markerLayerGroup = L.layerGroup().addTo(map);
    let itineraireLayer = null;
    let itineraireStartMarker = null;
    let itineraireEndMarker = null;

    function ouvrirForm(type) {
      typeActuel = type;
      document.getElementById("incidentDesc").value = "";
      document.getElementById("popupForm").style.display = "none";
      selectionMode = true;
      alert("Cliquez sur la carte pour sélectionner l'emplacement de l’incident.");
    }

    map.on("click", function(e) {
      if (selectionMode) {
        const coords = e.latlng;
        if (tempMarker) map.removeLayer(tempMarker);
        tempMarker = L.marker(coords).addTo(map).bindPopup("Emplacement sélectionné").openPopup();
        document.getElementById("popupForm").style.display = "block";
        selectionMode = false;
        tempMarker._customCoords = coords;
      }
    });

    function validerIncident() {
      const desc = document.getElementById("incidentDesc").value.trim();
      if (!desc || !tempMarker || !tempMarker._customCoords) return alert("Veuillez remplir tous les champs.");
      const coords = tempMarker._customCoords;
      if (nombreTotalSignalements() >= 5) {
  return alert("⚠️ Limite atteinte : vous avez déjà signalé 5 incidents au total.");
}

      const signalement = {
        type: typeActuel,
        description: desc,
        date: new Date().toLocaleString(),
        coords: [coords.lat.toFixed(5), coords.lng.toFixed(5)]
      };
      signalements.push(signalement);
      localStorage.setItem("signalements", JSON.stringify(signalements));

      let cssClass = '';
      switch (typeActuel.toLowerCase()) {
        case 'embouteillage': cssClass = 'popup-embouteillage'; break;
        case 'accident': cssClass = 'popup-accident'; break;
        case 'inondation': cssClass = 'popup-inondation'; break;
        case 'incendie': cssClass = 'popup-incendie'; break;
      }

      const customPopup = L.popup({ className: cssClass }).setContent(`${typeActuel} : ${desc}`);
      L.marker(coords).addTo(markerLayerGroup).bindPopup(customPopup).openPopup();

      document.getElementById("popupForm").style.display = "none";
      document.getElementById("validateSound").play();
    }

    function appeler(service, numero) {
      alert(`Appel d'urgence vers ${service} (${numero})`);
      window.location.href = `tel:${numero}`;
    }

    function updateHistorique() {
      const liste = document.getElementById('listeSignalements');
      liste.innerHTML = '';
      markerLayerGroup.clearLayers();
      signalements = JSON.parse(localStorage.getItem("signalements")) || [];
      signalements.forEach(sig => {
        const li = document.createElement('li');
        li.textContent = `${sig.date} - ${sig.type} (${sig.description}) à (${sig.coords.join(', ')})`;
        liste.appendChild(li);
        let cssClass = '';
        switch (sig.type.toLowerCase()) {
          case 'embouteillage': cssClass = 'popup-embouteillage'; break;
          case 'accident': cssClass = 'popup-accident'; break;
          case 'inondation': cssClass = 'popup-inondation'; break;
          case 'incendie': cssClass = 'popup-incendie'; break;
        }
        const customPopup = L.popup({ className: cssClass }).setContent(`${sig.type} : ${sig.description}`);
        L.marker(sig.coords).addTo(markerLayerGroup).bindPopup(customPopup);
      });
    }

    function supprimerHistorique(event) {
      if (confirm("Voulez-vous vraiment supprimer l’historique ?")) {
        localStorage.removeItem("signalements");
        signalements = [];
        document.getElementById("listeSignalements").innerHTML = '';
        markerLayerGroup.clearLayers();
        if (tempMarker) { map.removeLayer(tempMarker); tempMarker = null; }
        if (itineraireLayer) { map.removeLayer(itineraireLayer); itineraireLayer = null; }
        if (itineraireStartMarker) { map.removeLayer(itineraireStartMarker); itineraireStartMarker = null; }
        if (itineraireEndMarker) { map.removeLayer(itineraireEndMarker); itineraireEndMarker = null; }

        document.getElementById("deleteSound").play();
        const bouton = event.target;
        bouton.classList.add("tremble");
        setTimeout(() => bouton.classList.remove("tremble"), 400);
        const confirmBox = document.getElementById("confirmSuppression");
        confirmBox.textContent = "Carte nettoyée avec succès !";
        confirmBox.style.display = "block";
        setTimeout(() => { confirmBox.style.display = "none"; }, 3000);
      }
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


    function ouvrirPDF() {
      window.open('https://www.mintransports.cm/', '_blank');
    }

    function toggleTheme() {
      document.body.classList.toggle('light');
    }

    updateHistorique();

    async function signalementAuto(type) {
  typeActuel = type;
  const description = prompt("Décrivez brièvement l'incident :", "");
  if (!description) return alert("Description requise pour le signalement automatique.");

  if (!navigator.geolocation) {
    return alert("La géolocalisation n'est pas prise en charge par ce navigateur.");
  }

  navigator.geolocation.getCurrentPosition(function(position) {
    const lat = position.coords.latitude;
    const lng = position.coords.longitude;
    const coords = [lat, lng];

    const signalement = {
      type: typeActuel,
      description: description,
      date: new Date().toLocaleString(),
      coords: [lat.toFixed(5), lng.toFixed(5)]
    };
    signalements.push(signalement);
    localStorage.setItem("signalements", JSON.stringify(signalements));

    let cssClass = '';
    switch (typeActuel.toLowerCase()) {
      case 'embouteillage': cssClass = 'popup-embouteillage'; break;
      case 'accident': cssClass = 'popup-accident'; break;
      case 'inondation': cssClass = 'popup-inondation'; break;
      case 'voiture en feu': cssClass = 'popup-incendie'; break;
    }

    const customPopup = L.popup({ className: cssClass }).setContent(`${typeActuel} : ${description}`);
    L.marker(coords).addTo(markerLayerGroup).bindPopup(customPopup).openPopup();
    map.setView(coords, 15);
    document.getElementById("validateSound").play();
  }, function() {
    alert("Impossible d'obtenir votre position.");
  });
}


async function signalementAuto(type) {
  if (nombreTotalSignalements() >= 5) {
  return alert("⚠️ Limite atteinte : vous avez déjà signalé 5 incidents au total.");
}


  const description = prompt("Décrivez brièvement l'incident :", "");
  if (!description) return alert("Description requise pour le signalement automatique.");

  if (!navigator.geolocation) {
    return alert("La géolocalisation n'est pas prise en charge par ce navigateur.");
  }

  navigator.geolocation.getCurrentPosition(function(position) {
    const lat = position.coords.latitude;
    const lng = position.coords.longitude;
    const coords = [lat, lng];

    const signalement = {
      type: type,
      description: description,
      date: new Date().toLocaleString(),
      coords: [lat.toFixed(5), lng.toFixed(5)]
    };
    signalements.push(signalement);
    localStorage.setItem("signalements", JSON.stringify(signalements));

    let cssClass = '';
    switch (type.toLowerCase()) {
      case 'embouteillage': cssClass = 'popup-embouteillage'; break;
      case 'accident': cssClass = 'popup-accident'; break;
      case 'inondation': cssClass = 'popup-inondation'; break;
      case 'voiture en feu': cssClass = 'popup-incendie'; break;
    }

    const customPopup = L.popup({ className: cssClass }).setContent(`${type} : ${description}`);
    L.marker(coords).addTo(markerLayerGroup).bindPopup(customPopup).openPopup();
    map.setView(coords, 15);
    document.getElementById("validateSound").play();
  }, function() {
    alert("Impossible d'obtenir votre position.");
  });
}


  </script>
</body>
</html>
