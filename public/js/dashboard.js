/* ============================================
   MOVEUP - DASHBOARD INTERACTIONS
   ============================================ */

// Mobile Sidebar Toggle
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('mobileMenuToggle');
    const sidebar = document.getElementById('dashboardSidebar');
    const sidebarClose = document.getElementById('sidebarClose');
    
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('open');
        });
    }
    
    if (sidebarClose) {
        sidebarClose.addEventListener('click', function() {
            sidebar.classList.remove('open');
        });
    }
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 1024 && sidebar && sidebar.classList.contains('open')) {
            if (!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
                sidebar.classList.remove('open');
            }
        }
    });
    
    // Sidebar navigation
    document.querySelectorAll('.sidebar-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const section = this.dataset.section;
            if (section) {
                showSection(section);
            }
        });
    });
    
    // Add Meal Form
    const addMealForm = document.getElementById('addMealForm');
    if (addMealForm) {
        addMealForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'add_meal');
            
            try {
                const response = await fetch('analyze_nutrition.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                if (data.success) {
                    // Reload page to show new meal
                    location.reload();
                } else {
                    alert(data.error || 'Erreur lors de l\'ajout');
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });
    }
    
    // Full Add Meal Form
    const fullAddMealForm = document.getElementById('fullAddMealForm');
    if (fullAddMealForm) {
        fullAddMealForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'add_meal');
            
            try {
                const response = await fetch('analyze_nutrition.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error || 'Erreur lors de l\'ajout');
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });
    }
    
    // Update Weight Form
    const updateWeightForm = document.getElementById('updateWeightForm');
    if (updateWeightForm) {
        updateWeightForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'update_weight');
            
            try {
                const response = await fetch('update_weight.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error || 'Erreur lors de la mise à jour');
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });
    }
    
    // Analyze Nutrition Button
    const analyzeBtn = document.getElementById('analyzeNutritionBtn');
    if (analyzeBtn) {
        analyzeBtn.addEventListener('click', async function() {
            const nutritionAnalysis = document.getElementById('nutritionAnalysis');
            nutritionAnalysis.innerHTML = '<div class="loading">Analyse en cours...</div>';
            
            try {
                const response = await fetch('analyze_nutrition.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=analyze'
                });
                const data = await response.json();
                
                if (data.success) {
                    nutritionAnalysis.innerHTML = `
                        <div class="recommendations-box">
                            <strong><i class="fas fa-lightbulb"></i> Recommandations:</strong><br>
                            ${data.recommendations}
                        </div>
                    `;
                } else {
                    nutritionAnalysis.innerHTML = `
                        <div class="recommendations-box" style="border-left-color: var(--red);">
                            ${data.error || 'Ajoute des repas pour recevoir des recommandations'}
                        </div>
                    `;
                }
            } catch (error) {
                nutritionAnalysis.innerHTML = '<div class="recommendations-box" style="border-left-color: var(--red);">Erreur lors de l\'analyse</div>';
            }
        });
    }
    
    // Load nutrition history
    loadNutritionHistory();
    
    // Edit Profile Button
    const editProfileBtn = document.getElementById('editProfileBtn');
    if (editProfileBtn) {
        editProfileBtn.addEventListener('click', function() {
            window.location.href = 'edit_profile.php';
        });
    }
});

function showSection(sectionId) {
    // Hide all sections
    document.querySelectorAll('.dashboard-section').forEach(section => {
        section.classList.remove('active');
    });
    
    // Remove active class from all sidebar links
    document.querySelectorAll('.sidebar-link').forEach(link => {
        link.classList.remove('active');
    });
    
    // Show selected section
    const targetSection = document.getElementById(`section-${sectionId}`);
    if (targetSection) {
        targetSection.classList.add('active');
    }
    
    // Activate corresponding sidebar link
    const activeLink = document.querySelector(`.sidebar-link[data-section="${sectionId}"]`);
    if (activeLink) {
        activeLink.classList.add('active');
    }
    
    // Close sidebar on mobile
    if (window.innerWidth <= 1024) {
        const sidebar = document.getElementById('dashboardSidebar');
        if (sidebar) {
            sidebar.classList.remove('open');
        }
    }
    
    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

async function loadNutritionHistory() {
    const historyContainer = document.getElementById('nutritionHistory');
    if (!historyContainer) return;
    
    try {
        const response = await fetch('analyze_nutrition.php?action=get_history');
        const data = await response.json();
        
        if (data.success && data.meals.length > 0) {
            let html = '';
            let currentDate = '';
            
            data.meals.forEach(meal => {
                const mealDate = new Date(meal.date_log).toLocaleDateString('fr-FR');
                if (mealDate !== currentDate) {
                    if (currentDate) html += '</div>';
                    currentDate = mealDate;
                    html += `<div style="margin-bottom: 1rem;">
                                <div style="font-size: 0.7rem; color: var(--gold); margin-bottom: 0.5rem;">${mealDate}</div>`;
                }
                html += `<div class="meal-item">
                            <div class="meal-name">${escapeHtml(meal.meal_name)}</div>
                            <div class="meal-calories">${meal.calories || '?'} kcal</div>
                         </div>`;
            });
            html += '</div>';
            historyContainer.innerHTML = html;
        } else {
            historyContainer.innerHTML = '<div class="empty-meals"><i class="fas fa-utensils"></i><p>Aucun repas enregistré</p></div>';
        }
    } catch (error) {
        historyContainer.innerHTML = '<div class="empty-meals">Erreur de chargement</div>';
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function startWorkout(name, duration) {
    // In a real app, this would start a workout session
    // For now, show completion modal after a delay
    const modal = document.getElementById('sessionModal');
    if (modal) {
        modal.classList.add('open');
        
        // Log workout completion
        fetch('update_weight.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=log_workout&session_name=${encodeURIComponent(name)}&duration=${duration}`
        }).catch(console.error);
    }
}

function closeSessionModal() {
    const modal = document.getElementById('sessionModal');
    if (modal) {
        modal.classList.remove('open');
        location.reload();
    }
}

function closeWeightModal() {
    const modal = document.getElementById('weightModal');
    if (modal) {
        modal.classList.remove('open');
    }
}