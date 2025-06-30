// Fonctions pour la gestion de la sécurité admin

/**
 * Exporte les données de sécurité en CSV
 */
function exportSecurityData() {
    const table = document.querySelector('.security-table');
    if (!table) return;
    
    let csv = [];
    const rows = table.querySelectorAll('tr');
    
    // En-têtes
    const headers = [];
    table.querySelectorAll('th').forEach(th => {
        headers.push(th.textContent.trim());
    });
    csv.push(headers.join(','));
    
    // Données
    table.querySelectorAll('tbody tr').forEach(row => {
        const cols = [];
        row.querySelectorAll('td').forEach((td, index) => {
            if (index < headers.length - 1) { // Exclure la colonne Actions
                cols.push('"' + td.textContent.trim().replace(/"/g, '""') + '"');
            }
        });
        csv.push(cols.join(','));
    });
    
    // Téléchargement
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    
    if (link.download !== undefined) {
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', `security-stats-${new Date().toISOString().split('T')[0]}.csv`);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
}

/**
 * Débloque une adresse IP
 */
function unblockIP(ipAddress) {
    if (!confirm(`Êtes-vous sûr de vouloir débloquer l'adresse IP ${ipAddress} ?`)) {
        return;
    }
    
    fetch(`${window.racinesite}admin/security/unblock`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `ip=${encodeURIComponent(ipAddress)}&csrf_token=${document.querySelector('meta[name="csrf-token"]').content}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Adresse IP débloquée avec succès');
            location.reload();
        } else {
            alert('Erreur lors du déblocage : ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur de connexion');
    });
}

/**
 * Affiche les détails d'une IP
 */
function showIPDetails(ipAddress) {
    fetch(`${window.racinesite}admin/security/ip-details?ip=${encodeURIComponent(ipAddress)}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showIPModal(data.details);
        } else {
            alert('Erreur lors de la récupération des détails');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur de connexion');
    });
}

/**
 * Affiche une modal avec les détails d'une IP
 */
function showIPModal(details) {
    const modal = document.createElement('div');
    modal.className = 'ip-details-modal';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fi fi-sr-globe"></i> Détails de l'IP ${details.ip_address}</h3>
                <button class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="detail-grid">
                    <div class="detail-item">
                        <strong>Tentatives totales:</strong>
                        <span>${details.total_attempts}</span>
                    </div>
                    <div class="detail-item">
                        <strong>Échecs:</strong>
                        <span class="failed-count">${details.failed_attempts}</span>
                    </div>
                    <div class="detail-item">
                        <strong>Réussites:</strong>
                        <span class="success-count">${details.successful_attempts}</span>
                    </div>
                    <div class="detail-item">
                        <strong>Dernière tentative:</strong>
                        <span>${details.last_attempt}</span>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Fermer en cliquant à l'extérieur
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal();
        }
    });
}

/**
 * Ferme la modal
 */
function closeModal() {
    const modal = document.querySelector('.ip-details-modal');
    if (modal) {
        modal.remove();
    }
}

// Actualisation automatique toutes les 30 secondes
setInterval(() => {
    const timestamp = document.querySelector('.stats-overview');
    if (timestamp && document.visibilityState === 'visible') {
        location.reload();
    }
}, 30000);