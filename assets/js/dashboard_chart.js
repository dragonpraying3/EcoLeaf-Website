// Student Chart
if (typeof weeklyData !== 'undefined') {
    console.log('Creating chart for student dashboard');
    const studentChart = document.getElementById('myChart');
    if (studentChart) {
        new Chart(studentChart.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Fuel', 'Transport', 'Cycling/Walking', 'Recycling', 'Waste', 'Electric'],
                datasets: [{
                    label: 'CO₂ Contribution (kg)',
                    data: [
                        weeklyData.fuel,
                        weeklyData.transport,
                        weeklyData.cycling_walking,
                        weeklyData.recycling,
                        weeklyData.waste,
                        weeklyData.electric
                    ],
                    backgroundColor: [
                        'rgba(252, 24, 24, 0.7)',
                        'rgba(255, 159, 64, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(201, 203, 207, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(255, 205, 86, 0.7)'
                    ],
                    borderColor: 'white',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    title: { display: true, text: 'Weekly Calculator Result' }
                }
            }
        });
    }
}

// ORGANIZER CHART
if (typeof weeklyData !== 'undefined') {
    console.log('Creating chart for dashboard (student or organizer)');
    const dashboardChart = document.getElementById('myChart');
    
    if (dashboardChart) {
        console.log('Dashboard chart data:', weeklyData);
        
        const isOrganizer = document.querySelector('.dashboard-container h1')?.textContent?.includes('Organizer') || false;
        
        new Chart(dashboardChart.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Fuel', 'Transport', 'Cycling/Walking', 'Recycling', 'Waste', 'Electric'],
                datasets: [{
                    label: isOrganizer ? 'Total CO₂ Saved (kg)' : 'CO₂ Contribution (kg)',
                    data: [
                        weeklyData.fuel,
                        weeklyData.transport,
                        weeklyData.cycling_walking,
                        weeklyData.recycling,
                        weeklyData.waste,
                        weeklyData.electric
                    ],
                    backgroundColor: [
                        'rgba(252, 24, 24, 0.7)',
                        'rgba(255, 159, 64, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(201, 203, 207, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(255, 205, 86, 0.7)'
                    ],
                    borderColor: 'white',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    title: { 
                        display: true, 
                        text: isOrganizer ? 'Total CO₂ Saved by Participants' : 'Weekly Calculator Result'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'CO₂ Saved (kg)'
                        }
                    }
                }
            }
        });
    }
}

// Total CO2 Saved Chart
if (typeof totalCarbon !== 'undefined') {
    console.log('Creating total CO2 chart for admin dashboard');
    const totalCtx = document.getElementById('totalCO2Chart');
    if (totalCtx) {
        new Chart(totalCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Fuel','Transport','Cycling/Walking','Recycling','Waste','Electric'],
                datasets: [{
                    label: 'CO₂ Contribution (kg)',
                    data: [
                        totalCarbon.fuel,
                        totalCarbon.transport,
                        totalCarbon.cycling_walking,
                        totalCarbon.recycling,
                        totalCarbon.waste,
                        totalCarbon.electric
                    ],
                    backgroundColor: [
                        'rgba(252, 24, 24, 0.7)',
                        'rgba(255, 159, 64, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(201, 203, 207, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(255, 205, 86, 0.7)'
                    ],
                    borderColor: 'white',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    title: { display: true, text: 'Total CO₂ Saved (All Users)' }
                }
            }
        });
    }
}

// Admin-only Chart
if (typeof adminCarbon !== 'undefined') {
    console.log('Creating admin CO2 chart for admin dashboard');
    const adminCtx = document.getElementById('adminCO2Chart');
    if (adminCtx) {
        new Chart(adminCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Fuel','Transport','Cycling/Walking','Recycling','Waste','Electric'],
                datasets: [{
                    label: 'CO₂ Contribution (kg)',
                    data: [
                        adminCarbon.fuel,
                        adminCarbon.transport,
                        adminCarbon.cycling_walking,
                        adminCarbon.recycling,
                        adminCarbon.waste,
                        adminCarbon.electric
                    ],
                    backgroundColor: [
                        'rgba(252, 24, 24, 0.7)',
                        'rgba(255, 159, 64, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(201, 203, 207, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(255, 205, 86, 0.7)'
                    ],
                    borderColor: 'white',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    title: { display: true, text: 'Your CO₂ Saved' }
                }
            }
        });
    }
}