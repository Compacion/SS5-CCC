// Mobile menu toggle
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.querySelector('.menu-toggle');
    const navMenu = document.querySelector('.nav-menu');

    if (menuToggle && navMenu) {
        menuToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!menuToggle.contains(e.target) && !navMenu.contains(e.target)) {
                navMenu.classList.remove('active');
            }
        });
    }

    // Auto-refresh dashboard every 30 seconds
    if (window.location.pathname.includes('dashboard.php')) {
        setInterval(function() {
            location.reload();
        }, 30000);
    }

    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('error');
                } else {
                    field.classList.remove('error');
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    });

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, 5000);
    });
});

// Utility functions
function confirmAction(message) {
    return confirm(message);
}

function formatCurrency(amount) {
    return '$' + parseFloat(amount).toFixed(2);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString();
}

function formatTime(timeString) {
    const time = new Date('2000-01-01 ' + timeString);
    return time.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
}

// Status update functionality
function updateStatus(appointmentId, status) {
    if (confirmAction('Are you sure you want to update this appointment status?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.style.display = 'none';
        
        const idField = document.createElement('input');
        idField.name = 'appointment_id';
        idField.value = appointmentId;
        
        const statusField = document.createElement('input');
        statusField.name = 'status';
        statusField.value = status;
        
        const actionField = document.createElement('input');
        actionField.name = 'action';
        actionField.value = 'update_status';
        
        form.appendChild(idField);
        form.appendChild(statusField);
        form.appendChild(actionField);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Search functionality
function searchTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const table = document.getElementById(tableId);
    
    if (!input || !table) return;
    
    input.addEventListener('keyup', function() {
        const filter = input.value.toLowerCase();
        const rows = table.getElementsByTagName('tr');
        
        for (let i = 1; i < rows.length; i++) { // Skip header row
            const row = rows[i];
            const cells = row.getElementsByTagName('td');
            let found = false;
            
            for (let j = 0; j < cells.length; j++) {
                if (cells[j].textContent.toLowerCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
            
            row.style.display = found ? '' : 'none';
        }
    });
}

// Initialize search for tables
document.addEventListener('DOMContentLoaded', function() {
    // Add search boxes to tables if they exist
    const tables = document.querySelectorAll('table');
    tables.forEach((table, index) => {
        if (table.rows.length > 1) { // Only add search if table has data
            const searchBox = document.createElement('input');
            searchBox.type = 'text';
            searchBox.placeholder = 'Search...';
            searchBox.className = 'form-control';
            searchBox.style.marginBottom = '1rem';
            searchBox.id = 'search_' + index;
            
            table.parentNode.insertBefore(searchBox, table);
            
            // Add search functionality
            searchBox.addEventListener('keyup', function() {
                const filter = searchBox.value.toLowerCase();
                const rows = table.getElementsByTagName('tr');
                
                for (let i = 1; i < rows.length; i++) {
                    const row = rows[i];
                    const cells = row.getElementsByTagName('td');
                    let found = false;
                    
                    for (let j = 0; j < cells.length; j++) {
                        if (cells[j].textContent.toLowerCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                    
                    row.style.display = found ? '' : 'none';
                }
            });
        }
    });
});
