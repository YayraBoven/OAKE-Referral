@extends('layouts.app')

@section('content')
  <div id="existing" class="page active">
    <h1>Existing Patient Referral</h1>
    <p class="subtitle">Enter the patient's clinical details to check if they are suitable for downward referral.</p>

    @if(isset($error))
      <div class="result not-suitable">
        <div class="result-title">{{ $error }}</div>
      </div>
    @endif

    <form method="POST" action="{{ route('existing.submit') }}">
      @csrf
      <div class="card">
        <div class="field-row">
          <div class="field"><label>Age group</label>
            <select name="age_group">
              <option value="18-29">18–29</option>
              <option value="0-17">0–17</option>
              <option value="30-49">30–49</option>
              <option value="50-69">50–69</option>
              <option value="70+">70+</option>
            </select>
          </div>
          <div class="field"><label>Gender</label>
            <select name="gender">
              <option value="F">Female</option>
              <option value="M">Male</option>
            </select>
          </div>
        </div>
        <div class="field-row">
          <div class="field"><label>Severity of illness</label>
            <select name="severity">
              <option value="Minor">Minor</option>
              <option value="Moderate">Moderate</option>
              <option value="Major">Major</option>
              <option value="Extreme">Extreme</option>
            </select>
          </div>
          <div class="field"><label>Risk of mortality</label>
            <select name="risk_of_mortality">
              <option value="Minor">Minor</option>
              <option value="Moderate">Moderate</option>
              <option value="Major">Major</option>
              <option value="Extreme">Extreme</option>
            </select>
          </div>
        </div>
        <div class="field-row">
          <div class="field"><label>Admission type</label>
            <select name="admission_type">
              <option value="Elective">Elective</option>
              <option value="Emergency">Emergency</option>
              <option value="Urgent">Urgent</option>
            </select>
          </div>
          <div class="field"><label>Length of stay (days)</label>
            <input type="number" name="length_of_stay" value="2">
          </div>
        </div>
        <div class="field-row">
          <div class="field"><label>Medical / surgical</label>
            <select name="medical_surgical">
              <option value="Surgical">Surgical</option>
              <option value="Medical">Medical</option>
            </select>
          </div>
          <div class="field"><label>Diagnosis category</label>
            <select name="mdc_code">
              @foreach($mdcCategories as $code => $label)
                <option value="{{ $code }}">{{ $label }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="field-row">
          <div class="field"><label>Current hospital</label>
            <select name="current_hospital">
              <option value="Korle Bu Teaching Hospital">Korle Bu Teaching Hospital</option>
              <option value="Ridge Hospital (Greater Accra Regional Hospital)">Ridge Hospital</option>
              <option value="37 Military Hospital">37 Military Hospital</option>
            </select>
          </div>
        </div>
        <button type="submit" class="btn btn-primary submit-btn">Check referral eligibility</button>

        @if(isset($result))
          <div class="result {{ $result['decision'] === 'SUITABLE for downward referral' ? 'suitable' : 'not-suitable' }}">
            <div class="result-title">{{ $result['decision'] }} - {{ $result['confidence'] }}% confidence</div>
            @if(isset($result['required_specialty']))
              <div class="result-detail">Required specialty: {{ $result['required_specialty'] }}. Recommended hospitals below.</div>
            @elseif(isset($result['action']))
              <div class="result-detail">{{ $result['action'] }}</div>
            @endif
          </div>
        @endif
      </div>
    </form>

    @if(isset($result) && isset($result['recommendation']) && $result['recommendation']['status'] === 'ok')
      <div style="margin-top:28px; max-width:560px;">
        <h2>Recommended hospitals</h2>
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
    @endif
  </div>
@endsection