@extends('layouts.admin')

@section('page-title', 'Update User')

@section('content')

<!-- Change User Form -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                    @csrf
                    @method('PUT')

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Row 1: Username and Email -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror"
                                   id="username" name="username" value="{{ old('username', $user->name) }}" required>
                            <div class="form-text">Required. 150 characters or fewer. Letters, digits and @/./+/-/_ only.</div>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email', $user->email) }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Row 2: Password and Password Confirmation -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   id="password" name="password">
                            <div class="form-text">
                                <ul class="mb-0">
                                    <li>Your password must contain at least 8 characters.</li>
                                    <li>Your password can't be entirely numeric.</li>
                                    <li><strong>Leave blank to keep the current password.</strong></li>
                                </ul>
                            </div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">Password Confirmation</label>
                            <input type="password" class="form-control" id="password_confirmation"
                                   name="password_confirmation">
                            <div class="form-text">Enter the same password as before, for verification.</div>
                        </div>
                    </div>

                    <!-- Row 3: Phone and Role -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                   id="phone" name="phone" value="{{ old('phone', $user->phone ?? '') }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="role_id" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select @error('role_id') is-invalid @enderror" id="role_id" name="role_id" required>
                                <option value="">Select Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Row 4: Max Orders and Status -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="max_orders_per_day" class="form-label">Max Orders Per Day <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('max_orders_per_day') is-invalid @enderror"
                                   id="max_orders_per_day" name="max_orders_per_day"
                                   value="{{ old('max_orders_per_day', $user->max_orders_per_day ?? 30) }}"
                                   min="1" max="1000" required>
                            <div class="form-text">Maximum orders that can be assigned per day.</div>
                            @error('max_orders_per_day')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                       value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Is Active
                                </label>
                            </div>
                            <div class="form-text">Designates whether this user should be treated as active.</div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-light">Cancel</a>
                        <button type="submit" name="action" value="save_and_add_another" class="btn btn-light">
                            Save and add another
                        </button>
                        <button type="submit" name="action" value="save_and_continue" class="btn btn-light">
                            Save and continue editing
                        </button>
                        <button type="submit" name="action" value="save" class="btn btn-primary">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Make password fields optional for edit
document.addEventListener('DOMContentLoaded', function() {
    const passwordField = document.getElementById('password');
    const passwordConfirmationField = document.getElementById('password_confirmation');

    passwordField.required = false;
    passwordConfirmationField.required = false;
});
</script>
@endsection
