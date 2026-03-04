@extends('layouts.admin')

@section('title', 'My Polls')

@section('content')
<div class="container-fluid">

  <div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
      <div class="welcome-text">
        <h4>My Polls</h4>
        <p class="mb-0">Manage your polls</p>
      </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active">Polls</li>
      </ol>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">All Polls</h4>
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
                  <th>Options</th>
                  <th>Total Votes</th>
                  <th>Status</th>
                  <th>Created</th>
                  <th style="min-width: 180px;">Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($polls as $poll)
                <tr id="poll-row-{{ $poll->id }}">
                  <td>{{ $loop->iteration }}</td>
                  <td>{{ Str::limit($poll->question, 50) }}</td>
                  <td>{{ $poll->options_count ?? $poll->options()->count() }}</td>
                  <td>{{ $poll->votes_count }}</td>
                  <td>
                    {{-- Status Toggle --}}
                    <div class="custom-control custom-switch">
                      <input type="checkbox" class="custom-control-input status-toggle" id="toggle-{{ $poll->id }}"
                        data-id="{{ $poll->id }}" {{ $poll->is_active ? 'checked' : '' }}>
                      <label class="custom-control-label" for="toggle-{{ $poll->id }}">
                        <span class="badge {{ $poll->is_active ? 'badge-success' : 'badge-danger' }}"
                          id="badge-{{ $poll->id }}">
                          {{ $poll->is_active ? 'Active' : 'Closed' }}
                        </span>
                      </label>
                    </div>
                  </td>
                  <td>{{ $poll->created_at->format('d M Y') }}</td>
                  <td>
                    <div class="d-flex align-items-center" style="gap: 4px;">

                      {{-- Share --}}
                      <button class="btn btn-xs btn-secondary copy-link"
                        data-link="{{ route('frontend.polls.index', $poll) }}" title="Copy shareable link">
                        <i class="fa fa-share-alt"></i>
                      </button>

                      {{-- Results --}}
                      <a href="{{ route('admin.polls.show', $poll) }}" class="btn btn-xs btn-info" title="View Results">
                        <i class="fa fa-bar-chart"></i>
                      </a>

                      {{-- Edit --}}
                      <a href="{{ route('admin.polls.edit', $poll) }}" class="btn btn-xs btn-warning" title="Edit Poll">
                        <i class="fa fa-pencil"></i>
                      </a>

                      {{-- Delete --}}
                      <form action="{{ route('admin.polls.destroy', $poll) }}" method="POST"
                        class="d-inline delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-xs btn-danger" title="Delete Poll">
                          <i class="fa fa-trash"></i>
                        </button>
                      </form>

                    </div>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="7" class="text-center text-muted py-4">
                    You have no polls yet.
                    <a href="{{ route('admin.polls.create') }}">Create your first poll!</a>
                  </td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <div class="mt-3">
            {{ $polls->links() }}
          </div>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
  // status toggle
    $(document).on('change', '.status-toggle', function() {
        var pollId = $(this).data('id');
        var toggle = $(this);
        var isChecked = $(this).prop('checked');

        Swal.fire({
            title: isChecked ? 'Activate Poll?' : 'Close Poll?',
            text: isChecked ? 'This poll will be visible to users.' : 'Users will no longer be able to vote.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: isChecked ? '#2BC155' : '#FF2E2E',
            cancelButtonColor: '#6c757d',
            confirmButtonText: isChecked ? 'Yes, activate it!' : 'Yes, close it!',
            cancelButtonText: 'Cancel'
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/polls/' + pollId + '/toggle-status',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        var badge = $('#badge-' + pollId);
                        if (response.is_active) {
                            badge.removeClass('badge-danger').addClass('badge-success').text('Active');
                        } else {
                            badge.removeClass('badge-success').addClass('badge-danger').text('Closed');
                        }

                        Swal.fire({
                            title: response.is_active ? 'Activated!' : 'Closed!',
                            text: response.is_active ? 'Poll is now active.' : 'Poll is now closed.',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    },
                    error: function() {
                        toggle.prop('checked', !toggle.prop('checked'));
                        Swal.fire({
                            title: 'Error!',
                            text: 'Something went wrong. Please try again.',
                            icon: 'error',
                            confirmButtonColor: '#FF2E2E'
                        });
                    }
                });
            } else {
                // revert toggle if user cancelled
                toggle.prop('checked', !toggle.prop('checked'));
            }
        });
    });

    // copy shareable link
    $(document).on('click', '.copy-link', function() {
        var link = $(this).data('link');
        navigator.clipboard.writeText(link).then(function() {
            Swal.fire({
                title: 'Copied!',
                text: 'Shareable link copied to clipboard.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
        });
    });

    // confirm before delete
    $(document).on('submit', '.delete-form', function(e) {
        e.preventDefault();
        var form = $(this);

        Swal.fire({
            title: 'Delete Poll?',
            text: 'This action cannot be undone. All votes will be lost.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#FF2E2E',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then(function(result) {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
</script>
@endpush