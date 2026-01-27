{{-- resources/views/reception/admin/staff/edit.blade.php --}}
@extends('layouts.reception')

@section('title', 'Admin — Edit Staff')

@section('content')
<section class="container rx-container py-4 py-lg-5" style="max-width: 860px;">

  <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-2 mb-3">
    <div>
      <div class="text-muted small">Admin</div>
      <h2 class="mb-1">Edit Staff</h2>
      <div class="text-muted">Update staff details and access role.</div>
    </div>

    <div class="d-flex gap-2">
      <a href="{{ route('reception.admin.staff.index') }}" class="btn btn-outline-dark">
        <i class="bi bi-arrow-left me-1"></i> Back
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger">
      <div class="fw-semibold mb-1">Please fix the following:</div>
      <ul class="mb-0">
        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  <div class="card rx-filter-card">
    <div class="card-body p-4">

      <div class="d-flex align-items-center gap-3 mb-4">
        <div class="icon-pill" style="width:46px;height:46px;">
          <i class="bi bi-person-gear"></i>
        </div>
        <div>
          <div class="fw-bold">{{ $staff->name }}</div>
          <div class="text-muted small">{{ $staff->email }}</div>
        </div>
        <div class="ms-auto">
          <span class="badge text-bg-dark text-uppercase">{{ $staff->role }}</span>
        </div>
      </div>

      <form method="POST" action="{{ route('reception.admin.staff.update', $staff) }}" class="row g-3">
        @csrf
        @method('PUT')

        <div class="col-md-6">
          <label class="form-label">Full name</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input
              name="name"
              class="form-control"
              value="{{ old('name', $staff->name) }}"
              required
              autocomplete="name"
            >
          </div>
        </div>

        <div class="col-md-6">
          <label class="form-label">Email</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input
              type="email"
              name="email"
              class="form-control"
              value="{{ old('email', $staff->email) }}"
              required
              autocomplete="email"
            >
          </div>
          <div class="form-text">Used for staff login.</div>
        </div>

        <div class="col-md-6">
          <label class="form-label">Role</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
            <select name="role" class="form-select" required>
              <option value="reception" @selected(old('role', $staff->role) === 'reception')>
                Reception (Bookings + Rooms board)
              </option>
              <option value="admin" @selected(old('role', $staff->role) === 'admin')>
                Admin (All access)
              </option>
            </select>
          </div>
          <div class="form-text">
            Admin can manage staff, rooms, and analytics. Reception has operational access.
          </div>
        </div>

        <div class="col-12">
          <hr class="my-2">
        </div>

        <div class="col-12 d-flex flex-wrap gap-2 justify-content-end">
          <a href="{{ route('reception.admin.staff.index') }}" class="btn btn-outline-secondary">
            Cancel
          </a>
          <button class="btn btn-dark">
            <i class="bi bi-check2-circle me-1"></i> Save Changes
          </button>
        </div>
      </form>

    </div>
  </div>

</section>
@endsection
