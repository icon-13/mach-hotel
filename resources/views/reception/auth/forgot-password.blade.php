@extends('layouts.reception')
@section('title','Reset Password — Mach Hotel')

@section('content')
<section class="container rx-container py-4 py-lg-5" style="max-width:720px;">

  <div class="card shadow-sm">
    <div class="card-body p-4">

      <div class="d-flex align-items-center gap-2 mb-2">
        <span class="icon-pill" style="width:42px;height:42px;">
          <i class="bi bi-shield-lock"></i>
        </span>
        <div>
          <h4 class="mb-0">Reset your password</h4>
          <div class="text-muted small">Staff security policy</div>
        </div>
      </div>

      <div class="text-muted mt-3">
        For staff accounts, password resets are handled by the <span class="fw-semibold">Admin</span>.
        Please contact your administrator to reset your password.
      </div>

      <div class="alert alert-secondary mt-3 mb-0">
        Tip: If you’re on a shared device, always logout after your shift.
      </div>

      <div class="d-flex gap-2 flex-wrap mt-4">
        <a class="btn btn-dark" href="{{ route('reception.login') }}">
          <i class="bi bi-arrow-left me-1"></i> Back to Login
        </a>

        <a class="btn btn-outline-dark" href="{{ route('home') }}">
          <i class="bi bi-globe2 me-1"></i> Public Site
        </a>
      </div>

    </div>
  </div>

</section>
@endsection
