document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('claimForm');
    const emotionIndicator = document.getElementById('emotionIndicator');
    const textarea = form.querySelector('textarea');

    // Analyse d'émotion en temps réel
    textarea.addEventListener('input', async () => {
        const response = await fetch('/api/analyze-emotion', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ text: textarea.value })
        });
        
        const { emotion } = await response.json();
        emotionIndicator.textContent = {
            positive: '😊 Positif',
            negative: '😠 Négatif',
            neutral: '😐 Neutre'
        }[emotion];
    });

    // Soumission du formulaire
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        try {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData
            });

            if (response.ok) {
                window.location.href = '/claim-success';
            }
        } catch (error) {
            console.error('Erreur:', error);
        }
    });

    // Charger les récompenses
    loadRewards();
});

async function loadRewards() {
    const response = await fetch('/api/rewards/user');
    const { badges, credits } = await response.json();
    
    document.getElementById('creditCounter').textContent = credits;
    const container = document.getElementById('badgesContainer');
    
    badges.forEach(badge => {
        const badgeEl = document.createElement('div');
        badgeEl.className = 'badge';
        badgeEl.innerHTML = `
            <img src="${badge.image}" alt="${badge.name}">
            <span>${badge.name}</span>
        `;
        container.appendChild(badgeEl);
    });
}