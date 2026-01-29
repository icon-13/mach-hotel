{{-- resources/views/reception/account/password.blade.php --}}
@extends('layouts.reception')
@section('title','Reception — Change Password')

@section('content')
@php
  $rxUser  = auth('reception')->user();
  $role    = $rxUser->role ?? 'staff';
  $isAdmin = ($role === 'admin');
@endphp

<section class="container rx-container py-4 py-lg-5">

  {{-- Header --}}
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-2 mb-3">
    <div>
      <div class="text-muted small">Account</div>
      <h2 class="mb-1">Change Password</h2>
      <div class="text-muted">Keep your staff account secure — use a strong password.</div>
    </div>

    <div class="d-flex gap-2 flex-wrap">
      {{-- ✅ Back: go to bookings if exists, otherwise safe fallback to /reception --}}
      <a class="btn btn-outline-dark"
         href="{{ $isAdmin ? route('reception.admin.rooms.index') : url('/reception') }}">
        <i class="bi bi-arrow-left me-1"></i> Back
      </a>

      <a class="btn btn-outline-danger"
         href="{{ route('reception.logout.get') }}">
        <i class="bi bi-box-arrow-right me-1"></i> Logout
      </a>
    </div>
  </div>

  {{-- Alerts --}}
  @if(session('success'))
    <div class="alert alert-success d-flex align-items-start gap-2">
      <i class="bi bi-check2-circle fs-5"></i>
      <div>{{ session('success') }}</div>
    </div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger">
      <div class="fw-semibold mb-1">Please fix the following:</div>
      <ul class="mb-0">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="row g-3">
    {{-- Form card --}}
    <div class="col-lg-7">
      <div class="card shadow-sm" style="background: rgba(255,255,255,.06); border: 1px solid var(--border);">
        <div class="card-body p-3 p-md-4">

          <div class="d-flex align-items-center gap-2 mb-3">
            <span class="icon-pill" style="width:40px;height:40px;">
              <i class="bi bi-shield-lock"></i>
            </span>
            <div>
              <div class="fw-semibold">Update your password</div>
              <div class="text-muted small">You’ll stay logged in on this device.</div>
            </div>
          </div>

          <form method="POST" action="{{ route('reception.account.password.update') }}" id="pwForm">
            @csrf

            {{-- Current password --}}
            <div class="mb-3">
              <label class="form-label">Current password</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-key"></i></span>
                <input
                  type="password"
                  name="current_password"
                  class="form-control"
                  required
                  autocomplete="current-password"
                  placeholder="Enter current password"
                >
                <button class="btn btn-outline-secondary" type="button" data-toggle="pw" aria-label="Show password">
                  <i class="bi bi-eye"></i>
                </button>
              </div>
              @error('current_password')
                <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror
            </div>

            {{-- New password --}}
            <div class="mb-3">
              <label class="form-label">New password</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input
                  type="password"
                  name="password"
                  class="form-control"
                  required
                  minlength="8"
                  autocomplete="new-password"
                  placeholder="Minimum 8 characters"
                >
                <button class="btn btn-outline-secondary" type="button" data-toggle="pw" aria-label="Show password">
                  <i class="bi bi-eye"></i>
                </button>
              </div>
              @error('password')
                <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror

              <div class="form-text">
                Tip: mix letters, numbers, and symbols. Avoid common passwords.
              </div>
            </div>

            {{-- Confirm --}}
            <div class="mb-3">
              <label class="form-label">Confirm new password</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-check2-circle"></i></span>
                <input
                  type="password"
                  name="password_confirmation"
                  class="form-control"
                  required
                  minlength="8"
                  autocomplete="new-password"
                  placeholder="Re-type new password"
                >
                <button class="btn btn-outline-secondary" type="button" data-toggle="pw" aria-label="Show password">
                  <i class="bi bi-eye"></i>
                </button>
              </div>
            </div>

            {{-- Submit --}}
            <div class="d-flex gap-2 flex-wrap mt-4">
              <button class="btn btn-dark px-4" type="submit">
                <i class="bi bi-check2-circle me-1"></i> Save password
              </button>
              <a class="btn btn-outline-secondary" href="{{ $isAdmin ? route('reception.admin.rooms.index') : url('/reception') }}">
                Cancel
              </a>
            </div>

          </form>
        </div>
      </div>
    </div>

    {{-- Side info --}}
    <div class="col-lg-5">
      <div class="card shadow-sm" style="background: rgba(255,255,255,.06); border: 1px solid var(--border);">
        <div class="card-body p-3 p-md-4">

          <div class="d-flex align-items-center gap-2 mb-2">
            <span class="icon-pill" style="width:40px;height:40px;">
              <i class="bi bi-person-badge"></i>
            </span>
            <div>
              <div class="fw-semibold">{{ $rxUser->name ?? 'Staff' }}</div>
              <div class="text-muted small">{{ $rxUser->email ?? '' }}</div>
            </div>
          </div>

          <hr>

          <div class="text-muted small">
            <div class="d-flex align-items-center gap-2 mb-2">
              <i class="bi bi-shield-check"></i>
              <span>Role: <span class="fw-semibold">{{ strtoupper($role) }}</span></span>
            </div>

            <div class="d-flex align-items-center gap-2 mb-2">
              <i class="bi bi-info-circle"></i>
              <span>Change your password incase of any breach.</span>
            </div>

            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-exclamation-triangle"></i>
              <span>If you forget it, admin can reset it from Staff Accounts.</span>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

</section>

{{-- Simple JS: toggle show/hide password --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-toggle="pw"]').forEach(btn => {
    btn.addEventListener('click', () => {
      const group = btn.closest('.input-group');
      const input = group?.querySelector('input');
      const icon = btn.querySelector('i');
      if (!input) return;

      const isPw = input.type === 'password';
      input.type = isPw ? 'text' : 'password';
      if (icon) icon.className = isPw ? 'bi bi-eye-slash' : 'bi bi-eye';
    });
  });
});
</script>
@endsection
