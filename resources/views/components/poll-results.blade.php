<div>
  {{-- Auto refresh every 3 seconds --}}
  <div wire:poll.3000ms>

    {{-- Total votes --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
      <h6 style="font-weight: 700; color: #333; margin: 0;">
        Live Results
      </h6>
      <span class="badge badge-primary" style="background: #6259ca; font-size: 13px; padding: 6px 12px;">
        {{ $totalVotes }} total votes
      </span>
    </div>

    {{-- Options --}}
    @foreach($options as $option)
    <div class="mb-4">
      <div class="d-flex justify-content-between align-items-center mb-1">
        <span style="font-size: 14px; font-weight: 500; color: #444;">
          {{ $option->label }}
        </span>
        <span style="font-size: 14px; font-weight: 700; color: #6259ca;">
          {{ $option->percentage() }}%
          <small class="text-muted ml-1">
            ({{ $option->totalVotes() }} votes)
          </small>
        </span>
      </div>
      <div class="progress" style="height: 14px; border-radius: 10px;">
        <div class="progress-bar" role="progressbar" style="width: {{ $option->percentage() }}%;
                           background: linear-gradient(135deg, #6259ca, #9b8ff7);
                           border-radius: 10px;
                           transition: width 0.5s ease;">
        </div>
      </div>
    </div>
    @endforeach

    {{-- Last updated --}}
    <div class="text-right mt-2">
      <small class="text-muted">
        <i class="fa fa-sync-alt"></i>
        Last updated: {{ now()->format('H:i:s') }}
      </small>
    </div>

  </div>
</div>