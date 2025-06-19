<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Trafic.CM - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <style>
    body { 
      font-family: 'Segoe UI', sans-serif; 
      transition: background-color 0.3s, color 0.3s;
     }
    .dark-mode {
       background-color: #025d8f; 
       color: #000000; 
      }
    .sidebar {
      height: 100vh; 
      background-color: 
      #108362;
       color: white;
      position: sticky; top: 0;
    }
    .sidebar a {
       color: white; display: block; padding: 1rem; text-decoration: none; 
      }
    .sidebar a:hover { 
      background-color: rgba(255,255,255,0.1); 
    }
    .stat-card, .signalement-card {
      border-radius: 10px;
      box-shadow: 0 3px 10px rgba(0,0,0,0.1);
      transition: transform 0.2s;
    }
    .stat-card:hover { 
      transform: scale(1.02);  
    }
    .stat-card { 
      background-color: white;
     }
    #map {
       height: 600px; border-radius: 5%;
       }
        .btn-fixed {
      position: fixed;
      top: 10px;
      width: 45px;
      height: 45px;
      padding: 0;
      border-radius: 50%;
      z-index: 999;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
  #resetBtn { right: 110px; }
    #downloadBtn { right: 60px; }
    #themeToggle { right: 10px; }
  </style>
</head>
<body>
  <button id="themeToggle" class="btn btn-primary btn-fixed">🌙</button>
  <button id="downloadBtn" class="btn btn-info btn-fixed">⬇️</button>
  <button id="resetBtn" class="btn btn-danger btn-fixed">🗑️</button>

  <div class="container-fluid">
    <div class="row">
      <div class="col-md-2 sidebar p-4">
        <h3 class="text-center">🚦 Trafic.CM</h3>
        <a href="interface-admi1.php">Accueil</a>
        <a href="#">Carte</a>
        <a href="#mb-3">Signalements</a>
        <a href="#mb-2">Statistiques</a>
        <a href="#">Paramètres</a>
      </div>

      <div class="col-md-10 py-4">
        <h1 class="mb-4">Tableau de bord Administratif</h1>

        <div class="bg-light p-3 mb-4 signalement-card">
          <h5>🚨 Nouveau signalement</h5>
          <form id="addForm" class="row g-2">
            <div class="col-md-3"><input required placeholder="Type" id="type" class="form-control" /></div>
            <div class="col-md-4"><input required placeholder="Lieu (ville)" id="lieu" class="form-control" /></div>
            <div class="col-md-3">
              <select id="statut" class="form-select">
                <option value="">Statut</option>
                <option>En cours</option>
                <option>Résolu</option>
                <option>Critique</option>
              </select>
            </div>
            <div class="col-md-2"><button class="btn btn-success w-100">Ajouter</button></div>
          </form>
        </div>

        <div class="row g-3 mb-4">
          <div class="col-md-3"><div class="bg-white p-3 stat-card"><h6>Total</h6><h4 id="total">0</h4></div></div>
          <div class="col-md-3"><div class="bg-white p-3 stat-card"><h6>En cours</h6><h4 id="encours">0</h4></div></div>
          <div class="col-md-3"><div class="bg-white p-3 stat-card"><h6>Résolus</h6><h4 id="resolu">0</h4></div></div>
          <div class="col-md-3"><div class="bg-white p-3 stat-card"><h6>Critiques</h6><h4 id="critique">0</h4></div></div>
        </div>

        <div class="row g-4 mb-4">
          <div class="col-md-6">
            <div class="bg-white p-3 stat-card">
              <h6 class="mb-2">📍 Carte des signalements</h6>
              <div id="map"></div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="bg-white p-3 stat-card">
              <h6 id="mb-2">📊 Statistiques</h6>
              <canvas id="chartStats"></canvas>
            </div>
          </div>
        </div>

        <div class="bg-white p-4 stat-card">
          <h5 id="mb-3"> Derniers signalements</h5>
          <table class="table table-hover">
            <thead><tr><th>ID</th><th>Type</th><th>Lieu</th><th>Date</th><th>Statut</th></tr></thead>
            <tbody id="signalementTable"></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    const map = L.map('map').setView([4.05, 11.5], 6);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap'
    }).addTo(map);

    const chartCtx = document.getElementById('chartStats');
    let chart;
    let signalements = [];

    async function geocodeAdresse(adresse) {
      const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(adresse)}`;
      const res = await fetch(url);
      const data = await res.json();
      return data[0] ? [parseFloat(data[0].lat), parseFloat(data[0].lon)] : null;
    }

    function updateStats() {
      document.getElementById('total').textContent = signalements.length;
      document.getElementById('encours').textContent = signalements.filter(s => s.statut === 'En cours').length;
      document.getElementById('resolu').textContent = signalements.filter(s => s.statut === 'Résolu').length;
      document.getElementById('critique').textContent = signalements.filter(s => s.statut === 'Critique').length;
    }

    function refreshTable() {
      const tbody = document.getElementById('signalementTable');
      tbody.innerHTML = '';
      signalements.forEach(sig => {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>#${sig.id}</td><td>${sig.type}</td><td>${sig.lieu}</td><td>${sig.date}</td><td>${sig.statut}</td>`;
        tbody.appendChild(tr);
      });
      updateStats();
      updateMap();
      updateChart();
    }

    function updateMap() {
      map.eachLayer(layer => {
        if (layer instanceof L.Marker) map.removeLayer(layer);
      });
      signalements.forEach(sig => {
        if (sig.coords) {
          L.marker(sig.coords).addTo(map).bindPopup(`<strong>${sig.type}</strong><br>${sig.lieu}`);
        }
      });
    }

    function updateChart() {
      const stats = { 'En cours': 0, 'Résolu': 0, 'Critique': 0 };
      signalements.forEach(s => stats[s.statut]++);
      if (chart) chart.destroy();
      chart = new Chart(chartCtx, {
        type: 'doughnut',
        data: {
          labels: Object.keys(stats),
          datasets: [{
            data: Object.values(stats),
            backgroundColor: ['#ffc107', '#198754', '#dc3545']
          }]
        },
        options: { plugins: { legend: { position: 'bottom' } } }
      });
    }

    document.getElementById('addForm').addEventListener('submit', async function (e) {
      e.preventDefault();
      const type = document.getElementById('type').value;
      const lieu = document.getElementById('lieu').value;
      const statut = document.getElementById('statut').value;
      const coords = await geocodeAdresse(lieu);
      if (!coords) return alert('Lieu non trouvé');

      const now = new Date().toISOString().slice(0, 16).replace('T', ' ');
      signalements.push({ id: Date.now(), type, lieu, date: now, statut, coords });
      this.reset();
      refreshTable();
      map.setView(coords, 12);
    });

    document.getElementById('themeToggle').addEventListener('click', () => {
      document.body.classList.toggle('dark-mode');
    });

    document.getElementById('downloadBtn').addEventListener('click', () => {
      const blob = new Blob([JSON.stringify(signalements, null, 2)], { type: 'application/json' });
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = 'signalements.json';
      a.click();
    });

    document.getElementById('resetBtn').addEventListener('click', () => {
      if (confirm('Voulez-vous vraiment réinitialiser tous les signalements ?')) {
        signalements = [];
        refreshTable();
      }
    });

    refreshTable();
  </script>
</body>
</html>
