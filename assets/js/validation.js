// Form Validation Functions

function validateName(name) {
    return /^[a-zA-Z\s]{2,100}$/.test(name.trim());
}

function validateMobile(mobile) {
    return /^[6-9]\d{9}$/.test(mobile);
}

function validateEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function validatePrice(price) {
    return !isNaN(price) && parseFloat(price) > 0;
}

function validateDates(checkIn, checkOut) {
    return new Date(checkOut) > new Date(checkIn);
}

// Live Search/Filtering Table Helper
function filterTable(inputId, tableId, colIndex) {
    var input = document.getElementById(inputId);
    if(!input) return;
    
    input.addEventListener("keyup", function() {
        var filter = input.value.toUpperCase();
        var table = document.getElementById(tableId);
        var tr = table.getElementsByTagName("tr");

        for (var i = 1; i < tr.length; i++) {
            var td = tr[i].getElementsByTagName("td")[colIndex];
            if (td) {
                var txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }       
        }
    });
}

// Auto hide alerts
document.addEventListener("DOMContentLoaded", function() {
    var alerts = document.querySelectorAll('.alert-success');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.display = 'none';
        }, 3000);
    });
});
