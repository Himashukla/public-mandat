@extends('layouts.frontend')

@section('title', 'All Polls')

@section('content')

{{-- Hero --}}
<div class="hero-section">
  <div class="container">
    <h1>🗳️ Explore Polls</h1>
    <p>Browse polls from our community and cast your vote today.</p>
  </div>
</div>

{{-- Polls --}}
<div class="container" style="margin-top: 60px;">

  <div class="d-flex align-items-center mb-4">
    <div>
      <h5 class="mb-0" style="font-weight: 700; color: #333;">All Polls</h5>
      <small class="text-muted">{{ $polls->total() }} polls available</small>
    </div>
  </div>

  @if($polls->count() > 0)
  <div class="row">
    @foreach($polls as $poll)
    <div class="col-lg-6 mb-4">
      <div class="poll-card">

        {{-- Card Header --}}
        <div class="poll-card-header">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <span class="status-pill {{ $poll->is_active ? 'active' : 'closed' }}">
              {{ $poll->is_active ? '● Active' : '● Closed' }}
            </span>
            <span class="poll-votes-badge">
              <i class="fa fa-users"></i>
              <span class="vote-count-{{ $poll->id }}">{{ $poll->votes_count }}</span> votes
            </span>
          </div>
          <h5>{{ $poll->question }}</h5>
          @if($poll->description)
          <p style="font-size: 13px; opacity: 0.85; margin: 8px 0 0;">
            {{ $poll->description }}
          </p>
          @endif
        </div>

        {{-- Card Body --}}
        <div class="poll-card-body">

          <div class="admin-by">
            <i class="fa fa-user-circle"></i>
            By <strong>{{ $poll->user->name }}</strong>
            @if($poll->ends_at)
            &nbsp;·&nbsp;
            <i class="fa fa-hourglass-end"></i>
            Ends {{ $poll->ends_at->diffForHumans() }}
            @endif
          </div>

          {{-- Options --}}
          <div id="options-{{ $poll->id }}">
            @foreach($poll->options as $option)
            <div class="vote-option-item mb-2" data-poll-id="{{ $poll->id }}" data-option-id="{{ $option->id }}"
              data-voted="{{ $poll->userHasVoted || !$poll->is_active ? 'true' : 'false' }}">

              <div class="option-wrapper" style="border: 2px solid {{ $poll->userHasVoted || !$poll->is_active ? '#6259ca' : '#eee' }};
                                       border-radius: 10px;
                                       overflow: hidden;
                                       position: relative;
                                       cursor: {{ $poll->userHasVoted || !$poll->is_active ? 'default' : 'pointer' }};
                                       transition: all 0.2s;">

                {{-- background fill bar --}}
                <div class="opt-fill-{{ $option->id }}" style="position: absolute;
                                           top: 0; left: 0; height: 100%;
                                           width: {{ $option->percentage() }}%;
                                           background: linear-gradient(135deg, rgba(98,89,202,0.12), rgba(155,143,247,0.12));
                                           border-radius: 8px;
                                           transition: width 0.5s ease;">
                </div>

                <div class="d-flex align-items-center justify-content-between p-2"
                  style="position: relative; z-index: 1;">
                  <div class="d-flex align-items-center">
                    {{-- radio button: only show if not voted --}}
                    @if(!$poll->userHasVoted && $poll->is_active)
                    <div class="option-radio mr-2" id="radio-{{ $option->id }}" style="width: 16px; height: 16px; border-radius: 50%;
                                                   border: 2px solid #6259ca; flex-shrink: 0;">
                    </div>
                    @endif
                    <span style="font-size: 14px; color: #444; font-weight: 500;">
                      {{ $option->label }}
                    </span>
                  </div>
                  <span
                    style="font-size: 12px; color: #6259ca; font-weight: 700; white-space: nowrap; margin-left: 8px;">
                    <span class="opt-percent-{{ $option->id }}">{{ $option->percentage() }}</span>%
                    <small class="text-muted">
                      (<span class="opt-votes-{{ $option->id }}">{{ $option->totalVotes() }}</span>)
                    </small>
                  </span>
                </div>

              </div>
            </div>
            @endforeach
          </div>

          {{-- Submit button: only if not voted and active --}}
          @if(!$poll->userHasVoted && $poll->is_active)
          <button class="btn btn-vote mt-3 submit-vote" id="submit-btn-{{ $poll->id }}" data-poll-id="{{ $poll->id }}"
            data-url="{{ route('frontend.polls.vote', $poll) }}">
            <i class="fa fa-vote-yea"></i> Submit Vote
          </button>
          <div class="vote-error text-danger mt-2" id="error-{{ $poll->id }}" style="display:none; font-size: 13px;">
          </div>
          @endif

          {{-- Already voted message --}}
          @if($poll->userHasVoted && $poll->is_active)
          <div class="text-center mt-3" id="voted-msg-{{ $poll->id }}">
            <small class="text-success">
              <i class="fa fa-check-circle"></i> You have already voted
            </small>
          </div>
          @endif

          {{-- Closed message --}}
          @if(!$poll->is_active)
          <div class="text-center mt-3">
            <small class="text-danger">
              <i class="fa fa-lock"></i> This poll is closed
            </small>
          </div>
          @endif

        </div>
      </div>
    </div>
    @endforeach
  </div>

  {{-- Pagination --}}
  <div class="d-flex justify-content-center mt-3">
    {{ $polls->appends(request()->query())->links() }}
  </div>

  @else
  <div class="empty-state">
    <i class="fa fa-poll"></i>
    <h5>No Polls Found</h5>
    <p>There are no polls at the moment. Check back later!</p>
  </div>
  @endif

</div>

@endsection

@push('scripts')
<script>
  $(document).ready(function() {

    var selectedOptions = {};

    // select option — only if not already voted
    $(document).on('click', '.vote-option-item', function() {
        var pollId   = $(this).data('poll-id');
        var optionId = $(this).data('option-id');
        var voted    = $(this).data('voted');

        if (voted === 'true') return;

        // reset all options in this poll
        $('#options-' + pollId + ' .vote-option-item .option-wrapper').css({
            'border-color': '#eee'
        });
        $('#options-' + pollId + ' .vote-option-item .option-radio').css({
            'background': 'white'
        });

        // highlight selected
        $(this).find('.option-wrapper').css({
            'border-color': '#6259ca'
        });
        $(this).find('.option-radio').css({
            'background': '#6259ca'
        });

        selectedOptions[pollId] = optionId;
        $('#error-' + pollId).hide();
    });

    // submit vote
    $(document).on('click', '.submit-vote', function() {
        var pollId = $(this).data('poll-id');
        var url    = $(this).data('url');
        var btn    = $(this);

        if (!selectedOptions[pollId]) {
            $('#error-' + pollId).text('Please select an option first.').show();
            return;
        }

        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Submitting...');

        $.ajax({
            url:  url,
            type: 'POST',
            data: {
                _token:    '{{ csrf_token() }}',
                option_id: selectedOptions[pollId]
            },
            success: function(response) {

                // update vote count badge
                $('.vote-count-' + pollId).text(response.total_votes);

                // update each option bar and count
                $.each(response.results, function(i, opt) {
                    $('.opt-percent-' + opt.id).text(opt.percentage);
                    $('.opt-votes-'   + opt.id).text(opt.votes);
                    $('.opt-fill-'    + opt.id).css('width', opt.percentage + '%');
                });

                // hide submit button
                btn.hide();

                // mark all options as voted so they are not clickable
                $('#options-' + pollId + ' .vote-option-item').data('voted', 'true');

                // remove radio buttons
                $('#options-' + pollId + ' .option-radio').remove();

                // show voted message
                $('#error-' + pollId).hide();
                $('#options-' + pollId).after(
                    '<div class="text-center mt-3" id="voted-msg-' + pollId + '">' +
                    '<small class="text-success">' +
                    '<i class="fa fa-check-circle"></i> You have already voted' +
                    '</small></div>'
                );

                Swal.fire({
                    title: 'Vote Recorded!',
                    text:  response.message,
                    icon:  'success',
                    timer: 1500,
                    showConfirmButton: false
                });
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="fa fa-vote-yea"></i> Submit Vote');
                var message = xhr.responseJSON ? xhr.responseJSON.message : 'Something went wrong.';
                $('#error-' + pollId).text(message).show();

                Swal.fire({
                    title: 'Oops!',
                    text:   message,
                    icon:   'error',
                    confirmButtonColor: '#6259ca'
                });
            }
        });
    });

    // copy link
    $(document).on('click', '.copy-link', function() {
        var link = $(this).data('link');
        navigator.clipboard.writeText(link).then(function() {
            Swal.fire({
                title: 'Copied!',
                text:  'Shareable link copied to clipboard.',
                icon:  'success',
                timer: 1500,
                showConfirmButton: false
            });
        });
    });

    // Echo listener — real time updates for all browsers
    if (window.Echo) {
        window.Echo.channel('polls')
            .listen('.vote.recorded', function(data) {
                var pollId = data.poll.id;

                // update total vote count badge
                $('.vote-count-' + pollId).text(data.totalVotes);

                // update each option bar and count
                $.each(data.results, function(i, opt) {
                    $('.opt-percent-' + opt.id).text(opt.percentage);
                    $('.opt-votes-'   + opt.id).text(opt.votes);
                    $('.opt-fill-'    + opt.id).css('width', opt.percentage + '%');
                });
            });

        console.log('Subscribed to polls channel');
    }

});
</script>
@endpush