<form action="{{ route('user.add-address') }}" method="POST" id="address-form">
    @csrf
    <input type="hidden" name="_method" value="POST" id="address-form-method">
    <input type="hidden" name="address_id" id="address-id">
    <div class="form-group">
        <label for="full_name">Full Name</label>
        <input type="text" class="form-control" id="full_name" name="full_name" required>
    </div>
    <div class="form-group">
        <label for="phone_number">Phone Number</label>
        <input type="text" class="form-control" id="phone_number" name="phone_number" required>
    </div>
    <div class="form-group">
        <label for="address_line_1">Address Line 1</label>
        <input type="text" class="form-control" id="address_line_1" name="address_line_1" required>
    </div>
    <div class="form-group">
        <label for="address_line_2">Address Line 2</label>
        <input type="text" class="form-control" id="address_line_2" name="address_line_2">
    </div>
    <div class="form-group">
        <label for="ward">Ward</label>
        <input type="text" class="form-control" id="ward" name="ward">
    </div>
    <div class="form-group">
        <label for="district">District</label>
        <input type="text" class="form-control" id="district" name="district">
    </div>
    <div class="form-group">
        <label for="province">Province</label>
        <input type="text" class="form-control" id="province" name="province">
    </div>
    <div class="form-group">
        <label for="is_default">Is Default</label>
        <input type="checkbox" id="is_default" name="is_default">
    </div>
    <button type="submit" class="btn btn-primary">Save Address</button>
    <button type="button" id="cancel-address-form" class="btn btn-secondary">Cancel</button>
</form>