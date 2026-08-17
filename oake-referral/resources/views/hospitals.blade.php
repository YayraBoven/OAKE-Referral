@extends('layouts.app')

@section('content')
  <div id="hospitals" class="page active">
    <h1>Hospital Directory</h1>
    <p class="subtitle">Live view of all {{ count($hospitals) }} hospitals in the network.</p>

    @if($apiError)
      <div class="result not-suitable">
        <div class="result-title">Unable to reach the referral system. Please make sure the API is running.</div>
      </div>
    @endif

    <div class="search-row">
      <input type="text" id="hospitalSearch" placeholder="Search Hospitals...">
      <select id="tierFilter">
        <option value="all">All tiers</option>
        <option value="1">Tier 1</option>
        <option value="2">Tier 2</option>
        <option value="3">Tier 3</option>
      </select>
    </div>

    <table>
      <tr><th>Hospital</th><th>Tier</th><th>Beds</th></tr>
      @foreach($hospitals as $hospital)
        @php
          $fillPercent = $hospital['number_of_beds'] > 0
            ? round(($hospital['beds_available'] / $hospital['number_of_beds']) * 100)
            : 0;
        @endphp
        <tr class="hospital-row" data-name="{{ strtolower($hospital['hospital_name']) }}" data-tier="{{ $hospital['hospital_tier'] }}">
          <td>
            <div class="hosp-name">{{ $hospital['hospital_name'] }}</div>
            <div class="hosp-loc">{{ $hospital['location'] }}</div>
          </td>
          <td><span class="badge badge-{{ $hospital['hospital_tier'] }}">Tier {{ $hospital['hospital_tier'] }}</span></td>
          <td>
            <div class="bed-cell">
              <div class="bed-bar"><div class="bed-fill" style="width:{{ $fillPercent }}%"></div></div>
              <span class="bed-text">{{ $hospital['beds_available'] }} / {{ $hospital['number_of_beds'] }}</span>
            </div>
          </td>
        </tr>
      @endforeach
    </table>
  </div>

  <script>
    const searchInput = document.getElementById('hospitalSearch');
    const tierSelect = document.getElementById('tierFilter');
    const rows = document.querySelectorAll('.hospital-row');

    function applyFilters() {
      const query = searchInput.value.toLowerCase();
      const tier = tierSelect.value;

      rows.forEach(row => {
        const matchesName = row.dataset.name.includes(query);
        const matchesTier = tier === 'all' || row.dataset.tier === tier;
        row.style.display = (matchesName && matchesTier) ? '' : 'none';
      });
    }

    searchInput.addEventListener('input', applyFilters);
    tierSelect.addEventListener('change', applyFilters);
  </script>
@endsection