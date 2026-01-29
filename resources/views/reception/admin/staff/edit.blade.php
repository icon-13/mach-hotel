{{-- resources/views/reception/admin/staff/edit.blade.php --}}
@extends('layouts.reception')

@section('title', 'Admin — Edit Staff')

@section('content')
@php
  $rxUser = auth('reception')->user();
  $isSelf = $rxUser && $staff && ((int)$rxUser->id === (int)$staff->id);
@endphp

<section class="container rx-container py-4 py-lg-5" style="max-width: 980px;">

  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-2 mb-3">
    <div>
      <div class="text-muted small">Admin • Staff Accounts</div>
      <h2 class="mb-1">Edit Staff</h2>
      <div class="text-muted">
        Update staff details • reset password when necessary
      </div>
    </div>

    <div class="d-flex gap-2 flex-wrap">
      <a class="btn btn-outline-dark" href="{{ route('reception.admin.staff.index') }}">
        <i class="bi bi-arrow-left me-1"></i> Back
      </a>

      <a class="btn btn-outline-danger" href="{{ route('reception.logout.get') }}">
        <i class="bi bi-box-arrow-right me-1"></i> Logout
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success d-flex align-items-start gap-2">
      <i class="bi bi-check-circle mt-1"></i>
      <div>{{ session('success') }}</div>
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger d-flex align-items-start gap-2">
      <i class="bi bi-exclamation-triangle mt-1"></i>
      <div>{{ session('error') }}</div>
    </div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger">
      <div class="fw-semibold mb-1">Please fix the following:</div>
      <ul class="mb-0">
        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  <div class="row g-3">

    {{-- LEFT: Profile --}}
    <div class="col-lg-7">
      <div class="card shadow-sm">
        <div class="card-header d-flex align-items-center justify-content-between">
          <div class="fw-semibold">
            <i class="bi bi-person-badge me-2"></i> Staff Profile
          </div>

          <span class="badge rounded-pill {{ $staff->role === 'admin' ? 'text-bg-dark' : 'text-bg-secondary' }}">
            {{ strtoupper($staff->role) }}
          </span>
        </div>

        <div class="card-body">
          <form method="POST" action="{{ route('reception.admin.staff.update', $staff) }}">
            @csrf
            @method('PUT')

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Full name</label>
                <input
                  name="name"
                  class="form-control"
                  value="{{ old('name', $staff->name) }}"
                  required
                  maxlength="255"
                  autocomplete="name"
                >
              </div>

              <div class="col-md-6">
                <label class="form-label">Email</label>
                <input
                  name="email"
                  type="email"
                  class="form-control"
                  value="{{ old('email', $staff->email) }}"
                  required
                  maxlength="255"
                  autocomplete="email"
                >
              </div>

              <div class="col-md-6">
                <label class="form-label">Role</label>
                <select name="role" class="form-select" required>
                  <option value="admin" @selected(old('role', $staff->role) === 'admin')>Admin</option>
                  <option value="reception" @selected(old('role', $staff->role) === 'reception')>Reception</option>
                </select>

                @if($isSelf)
                  <div class="form-text text-warning">
                    <i class="bi bi-shield-exclamation me-1"></i>
                    You are editing your own account — you can’t remove your admin role.
                  </div>
                @else
                  <div class="form-text">
                    Reception can operate bookings/rooms. Admin has management access.
                  </div>
                @endif
              </div>

              <div class="col-md-6">
                <label class="form-label">Status</label>
                <div class="d-flex align-items-center justify-content-between gap-2 p-2 rounded"
                     style="background: rgba(255,255,255,.06); border:1px solid var(--border);">
                  <div>
                    <div class="fw-semibold">
                      {{ $staff->is_active ? 'Active' : 'Inactive' }}
                    </div>
                    <div class="text-muted small">
                      Inactive staff can’t log in.
                    </div>
                  </div>

                  <form method="POST" action="{{ route('reception.admin.staff.toggle', $staff) }}">
                    @csrf
                    <button
                      type="submit"
                      class="btn {{ $staff->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                      @if($isSelf) disabled @endif
                    >
                      <i class="bi {{ $staff->is_active ? 'bi-pause-circle' : 'bi-play-circle' }} me-1"></i>
                      {{ $staff->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                  </form>
                </div>

                @if($isSelf)
                  <div class="form-text text-muted">
                    You can’t deactivate your own account.
                  </div>
                @endif
              </div>
            </div>

            <hr class="my-4">

            <div class="d-flex gap-2 flex-wrap">
              <button class="btn btn-dark">
                <i class="bi bi-check2-circle me-1"></i> Save Changes
              </button>
              <a class="btn btn-outline-secondary" href="{{ route('reception.admin.staff.index') }}">
                Cancel
              </a>
            </div>

          </form>
        </div>
      </div>
    </div>

    {{-- RIGHT: Password Reset --}}
    <div class="col-lg-5">

      <div class="card shadow-sm">
        <div class="card-header">
          <div class="fw-semibold">
            <i class="bi bi-shield-lock me-2"></i> Reset Password
          </div>
        </div>

        <div class="card-body">

          @if($isSelf)
            <div class="alert alert-info mb-0">
              <div class="fw-semibold mb-1">You can’t reset your own password here.</div>
              <div class="small text-muted">
                Use <span class="fw-semibold">Account → Change Password</span> for your own password.
              </div>
            </div>
          @else
            <div class="text-muted small mb-3">
              Use this only if staff forgot their password or got locked out.
              
            </div>

            <form method="POST" action="{{ route('reception.admin.staff.resetPassword', $staff) }}">
              @csrf

              <div class="mb-3">
                <label class="form-label">New password</label>
                <input
                  type="password"
                  name="password"
                  class="form-control"
                  minlength="8"
                  required
                  autocomplete="new-password"
                  placeholder="Enter a temporary password"
                >
                <div class="form-text">
                  Minimum 8 characters. Share it securely (WhatsApp/Call), then tell them to change it after login.
                </div>
              </div>

              <button
                type="submit"
                class="btn btn-outline-danger w-100"
                onclick="return confirm('Reset password for {{ $staff->name }}? They will need the new password to log in.')"
              >
                <i class="bi bi-arrow-repeat me-1"></i> Reset Password
              </button>
            </form>

            <hr class="my-4">

            <div class="p-3 rounded"
                 style="background: rgba(255,255,255,.06); border:1px dashed var(--border);">
              <div class="fw-semibold mb-1">
                <i class="bi bi-lightbulb me-1"></i> Recommended flow
              </div>
              <ol class="small text-muted mb-0 ps-3">
                <li>Admin sets a temporary password here</li>
                <li>Staff logs in</li>
                <li>Staff goes to “Change Password” and sets their own</li>
              </ol>
            </div>
          @endif

        </div>
      </div>

    </div>
  </div>
</section>
@endsection
