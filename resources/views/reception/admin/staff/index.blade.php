@extends('layouts.reception')

@section('title','Admin — Staff Accounts')

@section('content')
<section class="container rx-container py-4 py-lg-5">

  {{-- Header --}}
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-2 mb-3">
    <div>
      <div class="text-muted small">Admin Panel</div>
      <h2 class="mb-1">Staff Accounts</h2>
      <div class="text-muted">Manage reception & admin access</div>
    </div>

    <a href="{{ route('reception.admin.staff.create') }}" class="btn btn-dark">
      <i class="bi bi-person-plus me-1"></i> Add Staff
    </a>
  </div>

  {{-- Flash --}}
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  {{-- Table --}}
  <div class="card shadow-sm">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($staff as $s)
            <tr>
              <td class="fw-semibold">{{ $s->name }}</td>

              <td class="text-muted">{{ $s->email }}</td>

              <td>
                @if($s->role === 'admin')
                  <span class="badge bg-dark">ADMIN</span>
                @else
                  <span class="badge bg-info text-dark">RECEPTION</span>
                @endif
              </td>

              <td>
                @if($s->is_active)
                  <span class="badge bg-success">Active</span>
                @else
                  <span class="badge bg-secondary">Inactive</span>
                @endif
              </td>

              <td class="text-end">
                <div class="d-inline-flex gap-2">

                  <a href="{{ route('reception.admin.staff.edit', $s) }}"
                     class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-pencil me-1"></i> Edit
                  </a>

                  @if(auth('reception')->id() !== $s->id)
                    <form method="POST"
                          action="{{ route('reception.admin.staff.toggle', $s) }}"
                          class="d-inline">
                      @csrf
                      <button
                        class="btn btn-sm {{ $s->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}"
                        onclick="return confirm('Are you sure?');"
                      >
                        {{ $s->is_active ? 'Deactivate' : 'Activate' }}
                      </button>
                    </form>
                  @endif

                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center text-muted py-4">
                No staff accounts found.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</section>
@endsection
