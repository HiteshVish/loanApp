@extends('layouts.sneat')

@section('title', 'Profile')

@section('content')
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Account Settings /</span> Profile</h4>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <h5 class="card-header">Profile Details</h5>
            <div class="card-body">
                <div class="d-flex align-items-start align-items-sm-center gap-4 mb-4">
                    @if(Auth::user()->avatar)
                        <img src="{{ Auth::user()->avatar }}" alt="user-avatar" class="d-block rounded" height="100" width="100" id="uploadedAvatar" />
                    @else
                        <div class="avatar avatar-xl">
                            <span class="avatar-initial rounded bg-label-primary" style="font-size: 48px;">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                    @endif
                </div>
                
                <form method="POST" action="{{ route('account.profile') }}">
                    @csrf
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="name" class="form-label">Name</label>
                            <input class="form-control" type="text" id="name" name="name" value="{{ old('name', Auth::user()->name) }}" autofocus />
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="email" class="form-label">E-mail</label>
                            <input class="form-control" type="email" id="email" name="email" value="{{ old('email', Auth::user()->email) }}" />
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="created_at" class="form-label">Member Since</label>
                        <input class="form-control" type="text" id="created_at" value="{{ Auth::user()->created_at->format('F d, Y') }}" disabled />
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
