@extends('layouts.sneat')

@section('title', 'Edit User')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold py-3 mb-0">
        <span class="text-muted fw-light">Admin / Users /</span> Edit User
    </h4>
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
        <i class="bx bx-arrow-back"></i> Back to Users
    </a>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <h5 class="card-header">Edit User Profile</h5>
            <div class="card-body">
                <div class="d-flex align-items-start align-items-sm-center gap-4 mb-4">
                    @if($user->avatar)
                        <img src="{{ $user->avatar }}" alt="user-avatar" class="d-block rounded" height="100" width="100" />
                    @else
                        <div class="avatar avatar-xl">
                            <span class="avatar-initial rounded bg-label-primary" style="font-size: 48px;">{{ substr($user->name, 0, 1) }}</span>
                        </div>
                    @endif
                    <div>
                        <h6 class="mb-1">{{ $user->name }}</h6>
                        <small class="text-muted">{{ $user->email }}</small>
                        <div class="mt-2">
                            @if($user->isAdmin())
                                <span class="badge bg-label-danger">Admin</span>
                            @else
                                <span class="badge bg-label-primary">User</span>
                            @endif
                            @if($user->id === auth()->id())
                                <span class="badge bg-label-info">This is you</span>
                            @endif
                        </div>
                    </div>
                </div>

                <form action="{{ route('admin.users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="name" class="form-label">Name</label>
                            <input class="form-control @error('name') is-invalid @enderror" type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required />
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="email" class="form-label">E-mail</label>
                            <input class="form-control @error('email') is-invalid @enderror" type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required />
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>User</option>
                                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Select user role (Admin or User)</div>
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="password" class="form-label">New Password (Optional)</label>
                            <input class="form-control @error('password') is-invalid @enderror" type="password" id="password" name="password" placeholder="Leave blank to keep current password" />
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Min. 8 characters. Leave blank to keep current password.</div>
                        </div>

                        <div class="mb-3 col-md-12">
                            <label class="form-label">Account Information</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <p class="mb-1"><strong>User ID:</strong> #{{ $user->id }}</p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-1"><strong>Member Since:</strong> {{ $user->created_at->format('F d, Y') }}</p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-1">
                                        <strong>Email Status:</strong> 
                                        @if($user->email_verified_at)
                                            <span class="badge bg-label-success">Verified</span>
                                        @else
                                            <span class="badge bg-label-warning">Unverified</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bx bx-save"></i> Save Changes
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

        @if($user->id !== auth()->id())
        <div class="card border-danger">
            <h5 class="card-header text-danger">Danger Zone</h5>
            <div class="card-body">
                <p class="mb-3">Permanently delete this user account. This action cannot be undone.</p>
                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone!');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bx bx-trash"></i> Delete User Account
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

