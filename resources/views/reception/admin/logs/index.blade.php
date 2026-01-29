@extends('layouts.reception')
@section('title','Admin • Audit Logs')

@section('content')
<section class="container rx-container py-4 py-lg-5">

  <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3 mb-3">
    <div>
      <div class="text-muted small">Admin</div>
      <h2 class="mb-1">Audit Logs</h2>
      <div class="text-muted">Track staff actions across bookings, rooms, and accounts.</div>
    </div>

    <div class="d-flex gap-2 flex-wrap">
      <a class="btn btn-outline-dark" href="{{ route('reception.admin.rooms.index') }}">
        <i class="bi bi-arrow-left me-1"></i> Back
      </a>
      <a class="btn btn-outline-secondary" href="{{ route('reception.admin.logs.index') }}">
        <i class="bi bi-arrow-clockwise me-1"></i> Refresh
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="card shadow-sm mb-3">
    <div class="card-body">
      <form class="row g-2 align-items-end" method="GET">
        <div class="col-lg-4">
          <label class="form-label small">Search</label>
          <input class="form-control" name="q" value="{{ $q }}" placeholder="Action, actor, entity id, IP...">
        </div>

        <div class="col-lg-3">
          <label class="form-label small">Action</label>
          <select class="form-select" name="action">
            <option value="">All</option>
            @foreach($actions as $a)
              <option value="{{ $a }}" @selected($action === $a)>{{ $a }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-lg-2 col-6">
          <label class="form-label small">Severity</label>
          <select class="form-select" name="severity">
            <option value="">All</option>
            <option value="info" @selected($severity==='info')>Info</option>
            <option value="warning" @selected($severity==='warning')>Warning</option>
            <option value="danger" @selected($severity==='danger')>Danger</option>
          </select>
        </div>

        <div class="col-lg-1 col-6">
          <label class="form-label small">From</label>
          <input class="form-control" type="date" name="from" value="{{ $from }}">
        </div>

        <div class="col-lg-1 col-6">
          <label class="form-label small">To</label>
          <input class="form-control" type="date" name="to" value="{{ $to }}">
        </div>

        <div class="col-lg-1 col-6 d-grid">
          <button class="btn btn-dark">
            <i class="bi bi-search"></i>
          </button>
        </div>
      </form>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th style="width:170px;">Time</th>
            <th style="width:220px;">Actor</th>
            <th style="width:170px;">Action</th>
            <th style="width:170px;">Entity</th>
            <th style="width:130px;">Severity</th>
            <th style="min-width:260px;">Meta</th>
          </tr>
        </thead>
        <tbody>
          @forelse($logs as $log)
            <tr>
              <td class="small text-muted">
                <div class="fw-semibold text-dark">{{ $log->created_at->format('Y-m-d') }}</div>
                <div>{{ $log->created_at->format('H:i:s') }}</div>
              </td>

              <td>
                <div class="fw-semibold">{{ $log->actor_name ?? 'System' }}</div>
                <div class="small text-muted">
                  {{ $log->actor_email ?? '' }}
                  @if($log->actor_role) • {{ strtoupper($log->actor_role) }} @endif
                </div>
                <div class="small text-muted">
                  {{ $log->ip ? "IP: {$log->ip}" : '' }}
                </div>
              </td>

              <td class="fw-semibold">{{ $log->action }}</td>

              <td class="small">
                @if($log->entity_type)
                  {{ $log->entity_type }} #{{ $log->entity_id }}
                @else
                  —
                @endif

                <div class="small text-muted">
                  {{ $log->method ? "{$log->method}" : '' }}
                </div>
              </td>

              <td>
                @php
                  $badge = match($log->severity){
                    'danger' => 'bg-danger',
                    'warning' => 'bg-warning text-dark',
                    default => 'bg-secondary',
                  };
                @endphp
                <span class="badge {{ $badge }}">{{ strtoupper($log->severity) }}</span>
              </td>

              <td class="small text-muted">
                @if($log->meta)
                  <details>
                    <summary class="text-decoration-underline">View</summary>
                    <pre class="mb-0 mt-2" style="white-space:pre-wrap;">{{ json_encode($log->meta, JSON_PRETTY_PRINT) }}</pre>
                  </details>
                @else
                  —
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center text-muted py-4">No logs found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="card-footer">
      {{ $logs->links() }}
    </div>
  </div>

</section>
@endsection
