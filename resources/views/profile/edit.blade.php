@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2 class="mb-4 fw-bold text-success">Profile</h2>
    <div class="row g-4">
        <div class="col-12 col-md-6">
            @include('profile.partials.update-profile-information-form')
        </div>
        <div class="col-12 col-md-6">
            @include('profile.partials.update-password-form')
        </div>
    </div>
</div>
@endsection
