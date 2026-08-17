@extends('layouts.app')

@section('content')
  <div id="new" class="page active">
    <h1>New / Ambulance Patient</h1>
    <p class="subtitle">For patients not yet placed at a hospital. Select the specialty needed and get a ranked list of hospitals with available beds.</p>

    @if(isset($error))
      <div class="result not-suitable">
        <div class="result-title">{{ $error }}</div>
      </div>
    @endif

    <form method="POST" action="{{ route('new.submit') }}">
      @csrf
      <div class="card">
        <div class="field">
          <label>Required specialty</label>
          <select name="required_specialty">
            @foreach($specialties as $specialty)
              <option value="{{ $specialty }}">{{ $specialty }}</option>
            @endforeach
          </select>
        </div>
        <button type="submit" class="btn btn-primary submit-btn" style="margin-top:14px;">Find hospital</button>
      </div>
    </form>

    @if(isset($result))
      @if($result['recommendation']['status'] === 'ok')
        <div style="margin-top:28px; max-width:560px;">
          <h2>Ranked results</h2>
          <table>
            <tr><th>Hospital</th><th>Tier</th><th>Beds available</th></tr>
            @foreach($result['recommendation']['results'] as $hospital)
              <tr>
                <td class="hosp-name">{{ $hospital['hospital_name'] }}</td>
                <td><span class="badge badge-{{ $hospital['hospital_tier'] }}">Tier {{ $hospital['hospital_tier'] }}</span></td>
                <td>{{ $hospital['beds_available'] }} / {{ $hospital['number_of_beds'] }}</td>
              </tr>
            @endforeach
          </table>
        </div>
      @else
        <div class="result not-suitable" style="margin-top:20px; max-width:560px;">
          <div class="result-title">No match found</div>
          <div class="result-detail">{{ $result['recommendation']['message'] }}</div>
        </div>
      @endif
    @endif
  </div>
@endsection