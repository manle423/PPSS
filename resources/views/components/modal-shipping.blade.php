<!-- Modal Shipping Option Start -->
<div class="modal fade" id="shippingModal" tabindex="-1" aria-labelledby="shippingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-0">
            <div class="modal-header">
                <h5 class="modal-title" id="shippingModalLabel">Shipping & Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Shipping Options Inside Modal -->
                <div class="form-group mb-3">
                    <label for="shipping_method">Shipping Method</label>
                    <select name="shipping_method" id="shipping_method" class="form-control">
                        <option value="standard">Standard Shipping (3-5 days)</option>
                        <option value="express">Express Shipping (1-2 days)</option>
                    </select>
                </div>
                <!-- Address Form Inside Modal -->
                @if (Auth::check())
                    @if ($addresses->isNotEmpty())
                        <div class="form-group mb-3">
                            <label for="address_id">Select Address</label>
                            <select name="address_id" id="address_id" class="form-control">
                                <option value="">Select an address</option>
                                @foreach ($addresses as $address)
                                    <option value="{{ $address->id }}"
                                        {{ old('address_id', request('address_id')) == $address->id ? 'selected' : '' }}>
                                        {{ $address->full_name }} - {{ $address->address_line_1 }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="selected_address_id" id="selected_address_id" value="">
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="new_address" name="new_address"
                                value="1" {{ old('new_address', request('new_address')) ? 'checked' : '' }}>
                            <label class="form-check-label" for="new_address">
                                Use a different address
                            </label>
                        </div>
                        <div id="new_address_form" style="display: none;">
                            <x-checkout.address-form :provinces="$provinces" />
                        </div>
                    @else
                        <div id="new_address_form">
                            <x-checkout.address-form :provinces="$provinces" />
                        </div>
                    @endif
                @else
                    <x-checkout.address-form :provinces="$provinces" />
                    <div class="form-group mb-3">
                        <label for="email">Email Address<sup>*</sup></label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                @endif
                <!-- Apply Address Button -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="applyAddressButton">Apply Address</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal Shipping Option End -->
