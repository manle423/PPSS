document.getElementById('show-address-form').addEventListener('click', function() {
    var formContainer = document.getElementById('address-form-container');
    formContainer.style.display = 'block';
    document.getElementById('address-form').reset();
    document.getElementById('address-form-method').value = 'POST';
    document.getElementById('address-form').action = addAddressRoute;
});

document.getElementById('address-form-container').addEventListener('click', function(event) {
    if (event.target.id === 'cancel-address-form') {
        var formContainer = document.getElementById('address-form-container');
        formContainer.style.display = 'none';
    }
});

document.querySelectorAll('.btn-edit-address').forEach(function(button) {
    button.addEventListener('click', function() {
        var addressId = this.getAttribute('data-id');
        var card = document.getElementById('address-card-' + addressId);
        card.querySelector('.address-view').style.display = 'none';
        card.querySelector('.address-edit').style.display = 'block';
    });
});

document.querySelectorAll('.btn-cancel-edit').forEach(function(button) {
    button.addEventListener('click', function() {
        var addressId = this.getAttribute('data-id');
        var card = document.getElementById('address-card-' + addressId);
        card.querySelector('.address-view').style.display = 'block';
        card.querySelector('.address-edit').style.display = 'none';
    });
});

document.querySelector('.btn-edit-user-info').addEventListener('click', function() {
    document.querySelector('.user-info-view').style.display = 'none';
    document.querySelector('.user-info-edit').style.display = 'block';
});

document.querySelector('.btn-cancel-edit-user-info').addEventListener('click', function() {
    document.querySelector('.user-info-view').style.display = 'block';
    document.querySelector('.user-info-edit').style.display = 'none';
});