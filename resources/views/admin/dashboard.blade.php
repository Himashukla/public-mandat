{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">

  {{-- Page Title --}}
  <div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
      <div class="welcome-text">
        <h4>Hi, {{ auth()->user()->name }}! 👋</h4>
        <p class="mb-0">Poll Management Dashboard</p>
      </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active">Dashboard</li>
      </ol>
    </div>
  </div>

  {{-- Stat Cards --}}
  <div class="row">
    <div class="col-lg-3 col-sm-6">
      <div class="card">
        <div class="stat-widget-two card-body">
          <div class="stat-content">
            <div class="stat-text">Total Polls</div>
            <div class="stat-digit">
              <i class="fa fa-bar-chart"></i> {{ $totalPolls }}
            </div>
          </div>
          <div class="progress">
            <div class="progress-bar progress-bar-primary" style="width: 80%" role="progressbar"></div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-sm-6">
      <div class="card">
        <div class="stat-widget-two card-body">
          <div class="stat-content">
            <div class="stat-text">Active Polls</div>
            <div class="stat-digit">
              <i class="fa fa-check-circle"></i> {{ $activePolls }}
            </div>
          </div>
          <div class="progress">
            <div class="progress-bar progress-bar-success"
              style="width: {{ $totalPolls > 0 ? ($activePolls / $totalPolls) * 100 : 0 }}%" role="progressbar"></div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-sm-6">
      <div class="card">
        <div class="stat-widget-two card-body">
          <div class="stat-content">
            <div class="stat-text">Total Votes</div>
            <div class="stat-digit">
              <i class="fa fa-users"></i> {{ $totalVotes }}
            </div>
          </div>
          <div class="progress">
            <div class="progress-bar progress-bar-warning" style="width: 75%" role="progressbar"></div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-sm-6">
      <div class="card">
        <div class="stat-widget-two card-body">
          <div class="stat-content">
            <div class="stat-text">Closed Polls</div>
            <div class="stat-digit">
              <i class="fa fa-lock"></i> {{ $closedPolls }}
            </div>
          </div>
          <div class="progress">
            <div class="progress-bar progress-bar-danger"
              style="width: {{ $totalPolls > 0 ? ($closedPolls / $totalPolls) * 100 : 0 }}%" role="progressbar"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    {{-- Recent Polls Table --}}
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Recent Polls</h4>
          <div class="card-action">
            <a href="{{ route('admin.polls.create') }}" class="btn btn-primary btn-sm">
              <i class="fa fa-plus"></i> Create Poll
            </a>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table mb-0">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Question</th>
                  <th>Votes</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($recentPolls as $poll)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td>{{ Str::limit($poll->question, 40) }}</td>
                  <td>{{ $poll->votes_count }}</td>
                  <td>
                    @if($poll->is_active)
                    <span class="badge badge-success">Active</span>
                    @else
                    <span class="badge badge-danger">Closed</span>
                    @endif
                  </td>
                  <td>
                    <a href="{{ route('admin.polls.show', $poll) }}" class="btn btn-xs btn-info">Results</a>
                    <a href="{{ route('admin.polls.edit', $poll) }}" class="btn btn-xs btn-warning">Edit</a>
                    <button class="btn btn-xs btn-secondary copy-link" data-link="{{ route('frontend.polls.index', $poll) }}">
                      <i class="fa fa-share-alt"></i>
                    </button>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="5" class="text-center text-muted py-3">
                    No polls yet. <a href="{{ route('admin.polls.create') }}">Create one!</a>
                  </td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    {{-- Right Column --}}
    <div class="col-lg-4">

      {{-- Poll Status Chart --}}
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Poll Status</h4>
        </div>
        <div class="card-body text-center">
          <canvas id="poll-status-chart" height="180"></canvas>
          <ul class="widget-line-list mt-3">
            <li class="border-right">
              {{ $activePolls }}<br>
              <span class="text-success"><i class="ti-hand-point-up"></i> Active</span>
            </li>
            <li>
              {{ $closedPolls }}<br>
              <span class="text-danger"><i class="ti-hand-point-down"></i> Closed</span>
            </li>
          </ul>
        </div>
      </div>

      {{-- Quick Actions --}}
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Quick Actions</h4>
        </div>
        <div class="card-body">
          <a href="{{ route('admin.polls.create') }}" class="btn btn-primary btn-block mb-2">
            <i class="fa fa-plus"></i> Create New Poll
          </a>
          <a href="{{ route('admin.polls.index') }}" class="btn btn-info btn-block mb-2">
            <i class="fa fa-list"></i> View All Polls
          </a>
          <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-danger btn-block">
              <i class="fa fa-sign-out"></i> Logout
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

  {{-- Bottom Row --}}
  <div class="row">

    {{-- Top Voted Polls --}}
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Top Voted Polls</h4>
        </div>
        <div class="card-body">
          @forelse($topPolls as $poll)
          <div class="progress-content py-2">
            <div class="row align-items-center">
              <div class="col-5">
                <div class="progress-text" title="{{ $poll->question }}">
                  {{ Str::limit($poll->question, 25) }}
                </div>
              </div>
              <div class="col-7">
                <div class="progress">
                  <div class="progress-bar progress-bar-primary" role="progressbar"
                    style="width: {{ $totalVotes > 0 ? ($poll->votes_count / $totalVotes) * 100 : 0 }}%">
                    {{ $poll->votes_count }} votes
                  </div>
                </div>
              </div>
            </div>
          </div>
          @empty
          <p class="text-center text-muted">No votes recorded yet.</p>
          @endforelse
        </div>
      </div>
    </div>

    {{-- Recent Vote Activity --}}
    <div class="col-lg-6">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Recent Vote Activity</h4>
        </div>
        <div class="card-body">
          <div class="widget-timeline">
            <ul class="timeline">
              @forelse($recentVotes as $vote)
              <li>
                <div class="timeline-badge primary"></div>
                <a class="timeline-panel text-muted" href="#">
                  <span>{{ $vote->created_at->diffForHumans() }}</span>
                  <h6 class="m-t-5">
                    <strong>
                      {{ $vote->user ? $vote->user->name : 'Guest (' . $vote->ip_address . ')' }}
                    </strong>
                    voted on:
                    {{ Str::limit($vote->pollOption->poll->question, 35) }}
                  </h6>
                </a>
              </li>
              @empty
              <li>
                <div class="timeline-badge info"></div>
                <a class="timeline-panel text-muted" href="#">
                  <h6 class="m-t-5">No vote activity yet.</h6>
                </a>
              </li>
              @endforelse
            </ul>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection

@push('scripts')
<script>
  // Doughnut Chart
    var ctx = document.getElementById('poll-status-chart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Active', 'Closed'],
            datasets: [{
                data: [{{ $activePolls }}, {{ $closedPolls }}],
                backgroundColor: ['#2BC155', '#FF2E2E'],
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            legend: { position: 'bottom' },
            cutoutPercentage: 70,
        }
    });

    // Copy shareable link
    document.querySelectorAll('.copy-link').forEach(function(btn) {
        btn.addEventListener('click', function() {
            navigator.clipboard.writeText(this.getAttribute('data-link')).then(function() {
                alert('Shareable link copied!');
            });
        });
    });
</script>
@endpush