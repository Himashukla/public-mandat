{{-- resources/views/admin/polls/show.blade.php --}}
@extends('layouts.admin')

@section('title', 'Poll Results')

@section('content')
<div class="container-fluid">

  <div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
      <div class="welcome-text">
        <h4>Poll Results</h4>
      </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.polls.index') }}">Polls</a></li>
        <li class="breadcrumb-item active">Results</li>
      </ol>
    </div>
  </div>

  <div class="row">

    {{-- Left: Live Results via Livewire --}}
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">{{ $poll->question }}</h4>
          <div class="card-action">
            @if($poll->is_active)
            <span class="badge badge-success">
              <i class="fa fa-circle"></i> Live
            </span>
            @else
            <span class="badge badge-danger">Closed</span>
            @endif
          </div>
        </div>
        <div class="card-body">
          @if($poll->description)
          <p class="text-muted mb-4">{{ $poll->description }}</p>
          @endif

          {{-- Livewire component --}}
          @livewire('poll-results', ['poll' => $poll])
        </div>
      </div>
    </div>

    {{-- Right: Poll Info --}}
    <div class="col-lg-4">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Poll Info</h4>
        </div>
        <div class="card-body">
          <table class="table table-sm table-borderless">
            <tr>
              <td class="text-muted">Status</td>
              <td>
                @if($poll->is_active)
                <span class="badge badge-success">Active</span>
                @else
                <span class="badge badge-danger">Closed</span>
                @endif
              </td>
            </tr>
            <tr>
              <td class="text-muted">Created</td>
              <td>{{ $poll->created_at->format('d M Y') }}</td>
            </tr>
            <tr>
              <td class="text-muted">Starts At</td>
              <td>{{ $poll->starts_at ? $poll->starts_at->format('d M Y H:i') : 'Immediately' }}</td>
            </tr>
            <tr>
              <td class="text-muted">Ends At</td>
              <td>{{ $poll->ends_at ? $poll->ends_at->format('d M Y H:i') : 'No expiry' }}</td>
            </tr>
            <tr>
              <td class="text-muted">Total Options</td>
              <td>{{ $poll->options->count() }}</td>
            </tr>
          </table>

          {{-- Shareable Link --}}
          <div class="mt-3">
            <label><strong>Shareable Link</strong></label>
            <div class="input-group">
              <input type="text" class="form-control" id="shareable-link" value="{{ route('frontend.polls.index') }}"
                readonly style="font-size: 12px;">
              <div class="input-group-append">
                <button class="btn btn-outline-secondary" id="copy-btn" type="button">
                  <i class="fa fa-copy"></i>
                </button>
              </div>
            </div>
          </div>

          <div class="mt-3">
            <a href="{{ route('admin.polls.edit', $poll) }}" class="btn btn-warning btn-block">
              <i class="fa fa-pencil"></i> Edit Poll
            </a>
            <form action="{{ route('admin.polls.destroy', $poll) }}" method="POST" class="mt-2 delete-form">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-danger btn-block">
                <i class="fa fa-trash"></i> Delete Poll
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection

@push('scripts')
<script>
  $('#copy-btn').on('click', function() {
        var input = document.getElementById('shareable-link');
        navigator.clipboard.writeText(input.value).then(function() {
            Swal.fire({
                title: 'Copied!',
                text: 'Link copied to clipboard.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
        });
    });

    $(document).on('submit', '.delete-form', function(e) {
        e.preventDefault();
        var form = $(this);
        Swal.fire({
            title: 'Delete Poll?',
            text: 'This cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#FF2E2E',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then(function(result) {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
</script>
@endpush