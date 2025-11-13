@extends('layouts.sneat')

@section('title', 'Security')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Account Settings /</span> Security</h4>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <h5 class="card-header">Change Password</h5>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('account.security') }}">
                    @csrf
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="current_password" class="form-label">Current Password <span class="text-danger">*</span></label>
                            <input class="form-control @error('current_password') is-invalid @enderror" type="password" id="current_password" name="current_password" required />
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="password" class="form-label">New Password <span class="text-danger">*</span></label>
                            <input class="form-control @error('password') is-invalid @enderror" type="password" id="password" name="password" required minlength="8" />
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Minimum 8 characters</div>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="password_confirmation" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                            <input class="form-control @error('password_confirmation') is-invalid @enderror" type="password" id="password_confirmation" name="password_confirmation" required minlength="8" />
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mt-2">
                        <button type="submit" class="btn btn-primary me-2">Save changes</button>
                        <button type="reset" class="btn btn-outline-secondary">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
