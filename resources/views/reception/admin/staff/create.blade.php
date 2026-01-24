@extends('layouts.reception')

@section('title','Admin — Add Staff')

@section('content')
<section class="container rx-container py-4 py-lg-5">

  {{-- Header --}}
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-2 mb-3">
    <div>
      <div class="text-muted small">Admin Panel</div>
      <h2 class="mb-1">Add Staff</h2>
      <div class="text-muted">Create an Admin or Reception account</div>
    </div>

    <a href="{{ route('reception.admin.staff.index') }}" class="btn btn-outline-dark">
      <i class="bi bi-arrow-left me-1"></i> Back
    </a>
  </div>

  @if($errors->any())
    <div class="alert alert-danger">
      <div class="fw-semibold mb-1">Fix these:</div>
      <ul class="mb-0">
        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  <div class="row g-3">
    {{-- Form --}}
    <div class="col-lg-7">
      <div class="card shadow-sm">
        <div class="card-body">

          <form method="POST" action="{{ route('reception.admin.staff.store') }}" autocomplete="off">
            @csrf

            <div class="row g-3">
              <div class="col-12">
                <label class="form-label">Full name</label>
                <input
                  name="name"
                  value="{{ old('name') }}"
                  class="form-control"
                  required
                  placeholder="e.g. Asha Kimaro"
                >
              </div>

              <div class="col-12">
                <label class="form-label">Email</label>
                <input
                  type="email"
                  name="email"
                  value="{{ old('email') }}"
                  class="form-control"
                  required
                  placeholder="e.g. asha@machhotel.com"
                >
              </div>

              <div class="col-md-6">
                <label class="form-label">Role</label>
                <select name="role" class="form-select" required>
                  <option value="">Select…</option>
                  <option value="reception" @selected(old('role')==='reception')>Reception</option>
                  <option value="admin" @selected(old('role')==='admin')>Admin</option>
                </select>
                <div class="form-text">
                  Reception can manage bookings & rooms board. Admin can also manage staff & room assignments.
                </div>
              </div>

              <div class="col-md-6">
                <label class="form-label">Temporary password</label>
                <div class="input-group">
                  <input
                    type="password"
                    name="password"
                    id="pw"
                    class="form-control"
                    required
                    minlength="8"
                    placeholder="Min 8 characters"
                  >
                  <button class="btn btn-outline-secondary" type="button" id="pwToggle" aria-label="Toggle password">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
                <div class="form-text">They can change it later (optional feature).</div>
              </div>
            </div>

            <hr class="my-4">

            <div class="d-flex gap-2 flex-wrap">
              <button class="btn btn-dark" type="submit">
                <i class="bi bi-check2-circle me-1"></i> Create Staff
              </button>

              <a href="{{ route('reception.admin.staff.index') }}" class="btn btn-outline-secondary">
                Cancel
              </a>
            </div>

          </form>

        </div>
      </div>
    </div>

    {{-- Side card (premium + minimal) --}}
    <div class="col-lg-5">
      <div class="card shadow-sm">
        <div class="card-body">
          <div class="d-flex align-items-center gap-2 mb-2">
            <span class="icon-pill" style="width:38px;height:38px;">
              <i class="bi bi-shield-check"></i>
            </span>
            <h5 class="mb-0">Access control</h5>
          </div>

          <p class="text-muted mb-0">
            Keep staff accounts minimal. Only give <b>Admin</b> role to trusted managers.
            Reception staff should only handle daily operations.
          </p>

          <hr>

          <div class="text-muted small">
            Tip: If you later add “force password change on first login”, this page won’t need changes.
          </div>
        </div>
      </div>
    </div>

  </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const pw = document.getElementById('pw');
  const btn = document.getElementById('pwToggle');
  if (!pw || !btn) return;

  btn.addEventListener('click', function () {
    const isPass = pw.getAttribute('type') === 'password';
    pw.setAttribute('type', isPass ? 'text' : 'password');
    btn.innerHTML = isPass ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
  });
});
</script>
@endpush
@endsection
