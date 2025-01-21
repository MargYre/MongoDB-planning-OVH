class PlanningManager {
    constructor() {
        this.baseUrl = 'index.php';
        this.requestTimeout = 5000; // 5 secondes timeout
    }

    async updatePlanning(week, year, userId) {
        let select = null;
        try {
            if (!this.validateInputs(week, year, userId)) {
                throw new Error('Données invalides pour la mise à jour du planning');
            }

            select = document.querySelector(`select[data-week="${week}"]`);
            if (!select) {
                throw new Error('Élément select non trouvé dans le DOM');
            }

            const selectedOption = select.options[select.selectedIndex];
            const userName = selectedOption.textContent.trim();

            if (!confirm(`Voulez-vous vraiment assigner la semaine ${week} à ${userName} ?`)) {
                select.value = select.getAttribute('data-previous-value') || select.value;
                return;
            }
            console.log('Envoi de la requête:', {week, year, userId});
            const response = await this.sendUpdateRequest(week, year, userId);
            console.log('Réponse brute:', responseBody);
            
            await this.handleResponse(response, select);
        } catch (error) {
            console.error('Détails de l\'erreur:', {
                message: error.message,
                stack: error.stack,
                response: error.response
            });
            throw error;
        }
    }

    validateInputs(week, year, userId) {
        week = parseInt(week, 10);
        year = parseInt(year, 10);
        
        if (isNaN(week) || week < 1 || week > 53) {
            console.error('Numéro de semaine invalide:', week);
            return false;
        }
        
        if (isNaN(year) || year < 2000 || year > 2100) {
            console.error('Année invalide:', year);
            return false;
        }
        
        if (!userId || typeof userId !== 'string' || userId.length !== 24) {
            console.error('ID utilisateur invalide:', userId);
            return false;
        }
        
        return true;
    }

    async sendUpdateRequest(week, year, userId) {
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), this.requestTimeout);

        try {
            const formData = new URLSearchParams();
            formData.append('week', week);
            formData.append('year', year);
            formData.append('user_id', userId);

            const response = await fetch(`${this.baseUrl}?action=update_planning`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData,
                signal: controller.signal
            });

            clearTimeout(timeoutId);

            const responseBody = await response.text();
            let data;
            try {
                data = JSON.parse(responseBody);
            } catch (e) {
                console.error('Réponse non JSON:', responseBody);
                throw new Error('Réponse invalide du serveur');
            }

            if (!response.ok) {
                throw new Error(`Erreur HTTP ${response.status}: ${data.error || 'Erreur inconnue'}`);
            }

            return data;
        } catch (error) {
            if (error.name === 'AbortError') {
                throw new Error('La requête a pris trop de temps');
            }
            throw error;
        }
    }

    async handleResponse(data, select) {
        if (!data) {
            throw new Error('Réponse vide du serveur');
        }

        if (data.error) {
            throw new Error(data.error);
        }
        
        if (!data.success) {
            throw new Error(data.message || 'Échec de la mise à jour');
        }
        
        alert(data.message || 'Modification effectuée avec succès !');
        if (select) {
            select.setAttribute('data-previous-value', select.value);
        }
        
        // Recharger la page pour mettre à jour les statistiques
        window.location.reload();
    }

    handleError(error, select) {
        let message = 'Une erreur est survenue lors de la modification';
        
        if (error.message.includes('HTTP 400')) {
            message = 'Données invalides. Veuillez réessayer.';
        } else if (error.message.includes('HTTP 401')) {
            message = 'Session expirée. Veuillez vous reconnecter.';
            window.location.href = `${this.baseUrl}?action=login`;
            return;
        } else if (error.message.includes('HTTP 404')) {
            message = 'Semaine non trouvée dans le planning.';
        } else if (error.message.includes('trop de temps')) {
            message = 'La requête a pris trop de temps. Veuillez réessayer.';
        }
        
        alert(message);
        
        if (select) {
            select.value = select.getAttribute('data-previous-value') || select.value;
        }
    }
}

const planningManager = new PlanningManager();