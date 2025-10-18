@extends('layouts.dashboard')

@section('page-title', 'Add Order')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom border-dashed d-flex align-items-center">
                <h4 class="card-title">Order Information</h4>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger alert-theme">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('orders.store') }}" method="POST">
                    @csrf

                    <!-- Row 1: Customer Name, Phone, WhatsApp -->
                    <div class="row">
                        <div class="col-lg-4 col-md-6">
                            <div class="mb-3">
                                <label for="customer_name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="customer_name" name="customer_name"
                                       value="{{ old('customer_name') }}" required>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="mb-3">
                                <label for="customer_phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="customer_phone" name="customer_phone"
                                       value="{{ old('customer_phone') }}" required>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="mb-3">
                                <label for="customer_whatsapp" class="form-label">WhatsApp Number</label>
                                <input type="tel" class="form-control" id="customer_whatsapp" name="customer_whatsapp"
                                       value="{{ old('customer_whatsapp') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: State, Product, Quantity -->
                    <div class="row">
                        <div class="col-lg-4 col-md-6">
                            <div class="mb-3">
                                <label for="customer_state" class="form-label">State <span class="text-danger">*</span></label>
                                <select class="form-select" id="customer_state" name="customer_state" required>
                                    <option value="">Select State</option>
                                    <option value="Abia" {{ old('customer_state') == 'Abia' ? 'selected' : '' }}>Abia</option>
                                    <option value="Adamawa" {{ old('customer_state') == 'Adamawa' ? 'selected' : '' }}>Adamawa</option>
                                    <option value="Akwa Ibom" {{ old('customer_state') == 'Akwa Ibom' ? 'selected' : '' }}>Akwa Ibom</option>
                                    <option value="Anambra" {{ old('customer_state') == 'Anambra' ? 'selected' : '' }}>Anambra</option>
                                    <option value="Bauchi" {{ old('customer_state') == 'Bauchi' ? 'selected' : '' }}>Bauchi</option>
                                    <option value="Bayelsa" {{ old('customer_state') == 'Bayelsa' ? 'selected' : '' }}>Bayelsa</option>
                                    <option value="Benue" {{ old('customer_state') == 'Benue' ? 'selected' : '' }}>Benue</option>
                                    <option value="Borno" {{ old('customer_state') == 'Borno' ? 'selected' : '' }}>Borno</option>
                                    <option value="Cross River" {{ old('customer_state') == 'Cross River' ? 'selected' : '' }}>Cross River</option>
                                    <option value="Delta" {{ old('customer_state') == 'Delta' ? 'selected' : '' }}>Delta</option>
                                    <option value="Ebonyi" {{ old('customer_state') == 'Ebonyi' ? 'selected' : '' }}>Ebonyi</option>
                                    <option value="Edo" {{ old('customer_state') == 'Edo' ? 'selected' : '' }}>Edo</option>
                                    <option value="Ekiti" {{ old('customer_state') == 'Ekiti' ? 'selected' : '' }}>Ekiti</option>
                                    <option value="Enugu" {{ old('customer_state') == 'Enugu' ? 'selected' : '' }}>Enugu</option>
                                    <option value="FCT" {{ old('customer_state') == 'FCT' ? 'selected' : '' }}>FCT</option>
                                    <option value="Gombe" {{ old('customer_state') == 'Gombe' ? 'selected' : '' }}>Gombe</option>
                                    <option value="Imo" {{ old('customer_state') == 'Imo' ? 'selected' : '' }}>Imo</option>
                                    <option value="Jigawa" {{ old('customer_state') == 'Jigawa' ? 'selected' : '' }}>Jigawa</option>
                                    <option value="Kaduna" {{ old('customer_state') == 'Kaduna' ? 'selected' : '' }}>Kaduna</option>
                                    <option value="Kano" {{ old('customer_state') == 'Kano' ? 'selected' : '' }}>Kano</option>
                                    <option value="Katsina" {{ old('customer_state') == 'Katsina' ? 'selected' : '' }}>Katsina</option>
                                    <option value="Kebbi" {{ old('customer_state') == 'Kebbi' ? 'selected' : '' }}>Kebbi</option>
                                    <option value="Kogi" {{ old('customer_state') == 'Kogi' ? 'selected' : '' }}>Kogi</option>
                                    <option value="Kwara" {{ old('customer_state') == 'Kwara' ? 'selected' : '' }}>Kwara</option>
                                    <option value="Lagos" {{ old('customer_state') == 'Lagos' ? 'selected' : '' }}>Lagos</option>
                                    <option value="Nasarawa" {{ old('customer_state') == 'Nasarawa' ? 'selected' : '' }}>Nasarawa</option>
                                    <option value="Niger" {{ old('customer_state') == 'Niger' ? 'selected' : '' }}>Niger</option>
                                    <option value="Ogun" {{ old('customer_state') == 'Ogun' ? 'selected' : '' }}>Ogun</option>
                                    <option value="Ondo" {{ old('customer_state') == 'Ondo' ? 'selected' : '' }}>Ondo</option>
                                    <option value="Osun" {{ old('customer_state') == 'Osun' ? 'selected' : '' }}>Osun</option>
                                    <option value="Oyo" {{ old('customer_state') == 'Oyo' ? 'selected' : '' }}>Oyo</option>
                                    <option value="Plateau" {{ old('customer_state') == 'Plateau' ? 'selected' : '' }}>Plateau</option>
                                    <option value="Rivers" {{ old('customer_state') == 'Rivers' ? 'selected' : '' }}>Rivers</option>
                                    <option value="Sokoto" {{ old('customer_state') == 'Sokoto' ? 'selected' : '' }}>Sokoto</option>
                                    <option value="Taraba" {{ old('customer_state') == 'Taraba' ? 'selected' : '' }}>Taraba</option>
                                    <option value="Yobe" {{ old('customer_state') == 'Yobe' ? 'selected' : '' }}>Yobe</option>
                                    <option value="Zamfara" {{ old('customer_state') == 'Zamfara' ? 'selected' : '' }}>Zamfara</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="mb-3">
                                <label for="product_id" class="form-label">Product <span class="text-danger">*</span></label>
                                <select class="form-select" id="product_id" name="product_id" required>
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="mb-3">
                                <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="quantity" name="quantity"
                                       value="{{ old('quantity') }}" min="1" required>
                            </div>
                        </div>
                    </div>

                    <!-- Row 3: Price, Status, Agent -->
                    <div class="row">
                        <div class="col-lg-4 col-md-6">
                            <div class="mb-3">
                                <label for="unit_price" class="form-label">Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">₦</span>
                                    <input type="number" class="form-control" id="unit_price" name="unit_price"
                                           value="{{ old('unit_price') }}" step="0.01" min="0" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="mb-3">
                                <label for="order_status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="order_status" name="status" required>
                                    <option value="">Select Status</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status }}" {{ old('status') == $status ? 'selected' : '' }}>
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="mb-3">
                                <label for="agent_id" class="form-label">Agent</label>
                                <select class="form-select" id="agent_id" name="agent_id">
                                    <option value="">Select Agent</option>
                                    @foreach($agents as $agent)
                                        <option value="{{ $agent->id }}" {{ old('agent_id') == $agent->id ? 'selected' : '' }}>
                                            {{ $agent->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Row 4: Address and Notes (two textareas side by side) -->
                    <div class="row">
                        <div class="col-lg-6 col-md-6">
                            <div class="mb-3">
                                <label for="customer_address" class="form-label">Address <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="customer_address" name="customer_address" rows="3" required>{{ old('customer_address') }}</textarea>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('orders.index') }}" class="btn btn-light">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-device-floppy me-1"></i>Save Order
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-calculate total price when quantity or unit price changes
document.addEventListener('DOMContentLoaded', function() {
    const quantityInput = document.getElementById('quantity');
    const unitPriceInput = document.getElementById('unit_price');

    function calculateTotal() {
        const quantity = parseFloat(quantityInput.value) || 0;
        const unitPrice = parseFloat(unitPriceInput.value) || 0;
        const total = quantity * unitPrice;

        // You can add a total display field if needed
        console.log('Total Price: ₦' + total.toFixed(2));
    }

    quantityInput.addEventListener('input', calculateTotal);
    unitPriceInput.addEventListener('input', calculateTotal);
});
</script>
@endsection
