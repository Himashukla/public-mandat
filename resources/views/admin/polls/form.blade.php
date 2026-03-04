{{-- resources/views/admin/polls/form.blade.php --}}
@extends('layouts.admin')

@section('title', isset($poll) ? 'Edit Poll' : 'Create Poll')

@section('content')
<div class="container-fluid">

  <div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
      <div class="welcome-text">
        <h4>{{ isset($poll) ? 'Edit Poll' : 'Create Poll' }}</h4>
        <p class="mb-0">{{ isset($poll) ? 'Update your poll details' : 'Add a new poll with options' }}</p>
      </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.polls.index') }}">Polls</a></li>
        <li class="breadcrumb-item active">{{ isset($poll) ? 'Edit' : 'Create' }}</li>
      </ol>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Poll Details</h4>
        </div>
        <div class="card-body">

          <form action="{{ isset($poll) ? route('admin.polls.update', $poll) : route('admin.polls.store') }}"
            method="POST">
            @csrf
            @if(isset($poll))
            @method('PUT')
            @endif

            <div class="form-group">
              <label><strong>Question</strong></label>
              <input type="text" name="question" class="form-control @error('question') is-invalid @enderror"
                placeholder="Enter your poll question" value="{{ old('question', $poll->question ?? '') }}">
              @error('question')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-group">
              <label><strong>Description</strong> <small class="text-muted">(optional)</small></label>
              <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror"
                placeholder="Add some context about this poll">{{ old('description', $poll->description ?? '') }}</textarea>
              @error('description')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-group">
              <label><strong>Options</strong></label>
              <div id="options-wrapper">
                @if(isset($poll))
                @foreach($poll->options as $index => $option)
                <div class="input-group mb-2 option-row">
                  <input type="text" name="options[]" class="form-control" placeholder="Option {{ $index + 1 }}"
                    value="{{ old('options.' . $index, $option->label) }}">
                  <div class="input-group-append">
                    <button type="button" class="btn btn-outline-danger remove-option">
                      <i class="fa fa-times"></i>
                    </button>
                  </div>
                </div>
                @endforeach
                @else
                <div class="input-group mb-2 option-row">
                  <input type="text" name="options[]" class="form-control @error('options.0') is-invalid @enderror"
                    placeholder="Option 1" value="{{ old('options.0') }}">
                  <div class="input-group-append">
                    <button type="button" class="btn btn-outline-danger remove-option">
                      <i class="fa fa-times"></i>
                    </button>
                  </div>
                </div>
                <div class="input-group mb-2 option-row">
                  <input type="text" name="options[]" class="form-control @error('options.1') is-invalid @enderror"
                    placeholder="Option 2" value="{{ old('options.1') }}">
                  <div class="input-group-append">
                    <button type="button" class="btn btn-outline-danger remove-option">
                      <i class="fa fa-times"></i>
                    </button>
                  </div>
                </div>
                @endif
              </div>
              @error('options')
              <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror
              <button type="button" id="add-option" class="btn btn-outline-primary btn-sm mt-2">
                <i class="fa fa-plus"></i> Add Option
              </button>

              @if(isset($poll))
              <div class="alert alert-warning mt-3 p-2">
                <small>
                  <i class="fa fa-exclamation-triangle"></i>
                  Editing options will reset all existing votes for this poll.
                </small>
              </div>
              @endif
            </div>

            <hr>

            {{-- Starts At, Ends At, Status in one row --}}
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label><strong>Starts At</strong> <small class="text-muted">(optional)</small></label>
                  <input type="datetime-local" name="starts_at"
                    class="form-control @error('starts_at') is-invalid @enderror"
                    value="{{ old('starts_at', isset($poll) && $poll->starts_at ? $poll->starts_at->format('Y-m-d\TH:i') : '') }}">
                  @error('starts_at')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label><strong>Ends At</strong> <small class="text-muted">(optional)</small></label>
                  <input type="datetime-local" name="ends_at"
                    class="form-control @error('ends_at') is-invalid @enderror"
                    value="{{ old('ends_at', isset($poll) && $poll->ends_at ? $poll->ends_at->format('Y-m-d\TH:i') : '') }}">
                  @error('ends_at')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label><strong>Status</strong></label>
                  <select name="is_active" class="form-control">
                    <option value="1" {{ old('is_active', $poll->is_active ?? '1') == '1' ? 'selected' : '' }}>Active
                    </option>
                    <option value="0" {{ old('is_active', $poll->is_active ?? '1') == '0' ? 'selected' : '' }}>Closed
                    </option>
                  </select>
                </div>
              </div>
            </div>

            <div class="mt-3">
              <button type="submit" class="btn {{ isset($poll) ? 'btn-warning' : 'btn-primary' }}">
                {{ isset($poll) ? 'Update Poll' : 'Create Poll' }}
              </button>
              <a href="{{ route('admin.polls.index') }}" class="btn btn-light ml-2">Cancel</a>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
  var optionCount = document.querySelectorAll('.option-row').length;

    document.getElementById('add-option').addEventListener('click', function() {
        optionCount++;
        var wrapper = document.getElementById('options-wrapper');
        var div = document.createElement('div');
        div.className = 'input-group mb-2 option-row';
        div.innerHTML = `
            <input type="text" name="options[]" class="form-control" placeholder="Option ${optionCount}">
            <div class="input-group-append">
                <button type="button" class="btn btn-outline-danger remove-option">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        `;
        wrapper.appendChild(div);
        attachRemove(div.querySelector('.remove-option'));
    });

    function attachRemove(btn) {
        btn.addEventListener('click', function() {
            if (document.querySelectorAll('.option-row').length <= 2) {
                alert('A poll must have at least 2 options.');
                return;
            }
            this.closest('.option-row').remove();
        });
    }

    document.querySelectorAll('.remove-option').forEach(function(btn) {
        attachRemove(btn);
    });
</script>
@endpush