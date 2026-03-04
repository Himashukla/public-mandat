@extends('layouts.frontend')

@section('title', $poll->question)

@section('content')

{{-- Hero --}}
<div class="hero-section">
  <div class="container">
    <h1 style="font-size: 28px;">🗳️ {{ $poll->question }}</h1>
    @if($poll->description)
    <p>{{ $poll->description }}</p>
    @endif
  </div>
</div>

<div class="container" style="margin-top: 50px;">

  <div class="row justify-content-center">
    <div class="col-lg-8">

      {{-- Back --}}
      <div class="mb-4">
        <a href="{{ route('frontend.polls.index') }}" style="color: #6259ca; font-size: 14px; font-weight: 500;">
          <i class="fa fa-arrow-left"></i> Back to Polls
        </a>
      </div>

      {{-- Main Poll Card --}}
      <div class="poll-card mb-4">
        <div class="poll-card-header">
          <div class="d-flex justify-content-between align-items-start mb-3">
            <span class="status-pill {{ $poll->is_active ? 'active' : 'closed' }}">
              {{ $poll->is_active ? '● Active' : '● Closed' }}
            </span>
            <span class="poll-votes-badge">
              <i class="fa fa-users"></i>
              <span id="total-votes">{{ $poll->totalVotes() }}</span> votes
            </span>
          </div>
          <h4 style="font-size: 20px; font-weight: 700; margin-bottom: 8px;">
            {{ $poll->question }}
          </h4>
          @if($poll->description)
          <p style="font-size: 14px; opacity: 0.85; margin: 0;">
            {{ $poll->description }}
          </p>
          @endif
        </div>

        <div class="poll-card-body">

          {{-- Poll Meta --}}
          <div class="d-flex flex-wrap mb-4" style="gap: 16px;">
            <div class="admin-by mb-0">
              <i class="fa fa-user-circle"></i>
              By <strong>{{ $poll->user->name }}</strong>
            </div>
            <div class="admin-by mb-0">
              <i class="fa fa-clock" style="color: #9b8ff7;"></i>
              {{ $poll->created_at->format('d M Y') }}
            </div>
            @if($poll->ends_at)
            <div class="admin-by mb-0">
              <i class="fa fa-hourglass-end" style="color: #9b8ff7;"></i>
              Ends {{ $poll->ends_at->diffForHumans() }}
            </div>
            @endif
          </div>

          {{-- Voted or Closed: show results --}}
          @if($hasVoted || !$poll->is_active)

          <div id="results-section">
            <h6 style="font-weight: 700; color: #333; margin-bottom: 16px;">
              Results
            </h6>
            @foreach($poll->options as $option)
            <div class="mb-4" id="result-row-{{ $option->id }}">
              <div class="d-flex justify-content-between mb-1">
                <span style="font-size: 14px; font-weight: 500; color: #444;">
                  {{ $option->label }}
                </span>
                <span style="font-size: 14px; color: #6259ca; font-weight: 700;">
                  <span class="opt-percent-{{ $option->id }}">{{ $option->percentage() }}</span>%
                  <small class="text-muted ml-1">
                    (<span class="opt-votes-{{ $option->id }}">{{ $option->totalVotes() }}</span> votes)
                  </small>
                </span>
              </div>
              <div class="progress" style="height: 14px; border-radius: 10px;">
                <div class="progress-bar opt-bar-{{ $option->id }}" role="progressbar" style="width: {{ $option->percentage() }}%;
                                               background: linear-gradient(135deg, #6259ca, #9b8ff7);
                                               border-radius: 10px;
                                               transition: width 0.6s ease;">
                </div>
              </div>
            </div>
            @endforeach

            @if($hasVoted && $poll->is_active)
            <div class="text-center py-3" style="background: #f0fdf4; border-radius: 12px; border: 1px solid #bbf7d0;">
              <i class="fa fa-check-circle" style="color: #2BC155; font-size: 20px;"></i>
              <p class="mb-0 mt-1" style="color: #2BC155; font-weight: 600; font-size: 14px;">
                You have already voted on this poll.
              </p>
            </div>
            @endif

            @if(!$poll->is_active)
            <div class="text-center py-3" style="background: #fff5f5; border-radius: 12px; border: 1px solid #fecaca;">
              <i class="fa fa-lock" style="color: #FF2E2E; font-size: 20px;"></i>
              <p class="mb-0 mt-1" style="color: #FF2E2E; font-weight: 600; font-size: 14px;">
                This poll is closed.
              </p>
            </div>
            @endif
          </div>

          {{-- Not voted and active: show options --}}
          @else

          <div id="vote-section">
            <h6 style="font-weight: 700; color: #333; margin-bottom: 16px;">
              Choose your answer
            </h6>

            @foreach($poll->options as $option)
            <div class="vote-option-item mb-3" data-option-id="{{ $option->id }}">
              <div class="d-flex align-items-center p-3" style="border: 2px solid #eee;
                                           border-radius: 12px;
                                           cursor: pointer;
                                           transition: all 0.2s;">
                <div class="option-radio mr-3" style="width: 20px;
                                               height: 20px;
                                               border-radius: 50%;
                                               border: 2px solid #6259ca;
                                               flex-shrink: 0;
                                               transition: all 0.2s;">
                </div>
                <span style="font-size: 15px; color: #444;">{{ $option->label }}</span>
              </div>
            </div>
            @endforeach

            <div id="vote-error" class="text-danger mb-3" style="display: none; font-size: 13px;"></div>

            <button id="submit-vote-btn" class="btn btn-vote" data-url="{{ route('frontend.polls.vote', $poll) }}">
              <i class="fa fa-vote-yea"></i> Submit Vote
            </button>
          </div>

          {{-- hidden results shown after voting --}}
          <div id="results-section" style="display: none;">
            <h6 style="font-weight: 700; color: #333; margin-bottom: 16px;">
              Results
            </h6>
            @foreach($poll->options as $option)
            <div class="mb-4">
              <div class="d-flex justify-content-between mb-1">
                <span style="font-size: 14px; font-weight: 500; color: #444;">
                  {{ $option->label }}
                </span>
                <span style="font-size: 14px; color: #6259ca; font-weight: 700;">
                  <span class="opt-percent-{{ $option->id }}">0</span>%
                  <small class="text-muted ml-1">
                    (<span class="opt-votes-{{ $option->id }}">0</span> votes)
                  </small>
                </span>
              </div>
              <div class="progress" style="height: 14px; border-radius: 10px;">
                <div class="progress-bar opt-bar-{{ $option->id }}" role="progressbar" style="width: 0%;
                                               background: linear-gradient(135deg, #6259ca, #9b8ff7);
                                               border-radius: 10px;
                                               transition: width 0.6s ease;">
                </div>
              </div>
            </div>
            @endforeach
            <div class="text-center py-3" style="background: #f0fdf4; border-radius: 12px; border: 1px solid #bbf7d0;">
              <i class="fa fa-check-circle" style="color: #2BC155; font-size: 20px;"></i>
              <p class="mb-0 mt-1" style="color: #2BC155; font-weight: 600; font-size: 14px;">
                Your vote has been recorded!
              </p>
            </div>
          </div>

          @endif

        </div>
      </div>

      {{-- Share Card --}}
      <div class="poll-card mb-4">
        <div class="poll-card-body">
          <h6 style="font-weight: 700; color: #333; margin-bottom: 12px;">
            <i class="fa fa-share-alt" style="color: #6259ca;"></i> Share this Poll
          </h6>
          <div class="input-group">
            <input type="text" class="form-control" id="shareable-link"
              value="{{ route('frontend.polls.index', $poll) }}" readonly
              style="border-radius: 10px 0 0 10px; font-size: 13px;">
            <div class="input-group-append">
              <button class="btn btn-vote" id="copy-btn" style="border-radius: 0 10px 10px 0; padding: 0 16px;">
                <i class="fa fa-copy"></i> Copy
              </button>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
  var selectedOptionId = null;

    // select option
    $(document).on('click', '.vote-option-item', function() {
        $('.vote-option-item .d-flex').css({
            'border-color': '#eee',
            'background': 'white'
        });
        $('.vote-option-item .option-radio').css({
            'background': 'white'
        });

        $(this).find('.d-flex').css({
            'border-color': '#6259ca',
            'background': '#f5f4ff'
        });
        $(this).find('.option-radio').css({
            'background': '#6259ca'
        });

        selectedOptionId = $(this).data('option-id');
        $('#vote-error').hide();
    });

    // submit vote
    $('#submit-vote-btn').on('click', function() {
        if (!selectedOptionId) {
            $('#vote-error').text('Please select an option first.').show();
            return;
        }

        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Submitting...');

        $.ajax({
            url: btn.data('url'),
            type: 'POST',
            data: {
                _token:    '{{ csrf_token() }}',
                option_id: selectedOptionId
            },
            success: function(response) {
                // update total votes
                $('#total-votes').text(response.total_votes);

                // update result bars
                $.each(response.results, function(i, opt) {
                    $('.opt-percent-' + opt.id).text(opt.percentage);
                    $('.opt-votes-'   + opt.id).text(opt.votes);
                    $('.opt-bar-'     + opt.id).css('width', opt.percentage + '%');
                });

                // hide vote section, show results
                $('#vote-section').hide();
                $('#results-section').show();

                Swal.fire({
                    title: 'Vote Recorded!',
                    text: response.message,
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="fa fa-vote-yea"></i> Submit Vote');
                var message = xhr.responseJSON ? xhr.responseJSON.message : 'Something went wrong.';

                $('#vote-error').text(message).show();

                Swal.fire({
                    title: 'Oops!',
                    text: message,
                    icon: 'error',
                    confirmButtonColor: '#6259ca'
                });
            }
        });
    });

    // copy link
    $('#copy-btn').on('click', function() {
        var input = document.getElementById('shareable-link');
        input.select();
        navigator.clipboard.writeText(input.value).then(function() {
            Swal.fire({
                title: 'Copied!',
                text: 'Shareable link copied to clipboard.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
        });
    });
</script>
@endpush