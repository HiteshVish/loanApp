@extends('layouts.sneat')

@section('title', 'Security')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Account Settings /</span> Security</h4>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <h5 class="card-header">Change Password</h5>
            <div class="card-body">
                <form method="POST" action="{{ route('account.security') }}">
                    @csrf
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input class="form-control" type="password" id="current_password" name="current_password" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="password" class="form-label">New Password</label>
                            <input class="form-control" type="password" id="password" name="password" />
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                            <input class="form-control" type="password" id="password_confirmation" name="password_confirmation" />
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
