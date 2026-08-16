@extends('layouts.app')

@section('content')
  <div id="existing" class="page active">
    <h1>Existing patient referral</h1>
    <p class="subtitle">Enter the patient's clinical details to check if they're suitable for downward referral.</p>

    <div class="card">
      <div class="field-row">
        <div class="field"><label>Age group</label><select><option>18–29</option><option>0–17</option><option>30–49</option><option>50–69</option><option>70+</option></select></div>
        <div class="field"><label>Gender</label><select><option>Female</option><option>Male</option></select></div>
      </div>
      <div class="field-row">
        <div class="field"><label>Severity of illness</label><select><option>Minor</option><option>Moderate</option><option>Major</option><option>Extreme</option></select></div>
        <div class="field"><label>Risk of mortality</label><select><option>Minor</option><option>Moderate</option><option>Major</option><option>Extreme</option></select></div>
      </div>
      <div class="field-row">
        <div class="field"><label>Admission type</label><select><option>Elective</option><option>Emergency</option><option>Urgent</option></select></div>
        <div class="field"><label>Length of stay (days)</label><input type="number" value="2"></div>
      </div>
      <div class="field-row">
        <div class="field"><label>Medical / surgical</label><select><option>Surgical</option><option>Medical</option></select></div>
        <div class="field"><label>Current hospital</label><select><option>Korle Bu Teaching Hospital</option><option>Ridge Hospital</option><option>37 Military Hospital</option></select></div>
      </div>
      <button class="btn btn-primary submit-btn">Check referral eligibility</button>

      <div class="result suitable">
        <div class="result-title">Suitable for downward referral — 98.6% confidence</div>
        <div class="result-detail">Required specialty: Obstetrics &amp; Gynaecology. Recommended hospitals below.</div>
      </div>
    </div>

    <div style="margin-top:28px; max-width:560px;">
      <h2>Recommended hospitals</h2>
      <table>
        <tr><th>Hospital</th><th>Tier</th><th>Beds available</th></tr>
        <tr><td class="hosp-name">Inkoom Hospital</td><td><span class="badge badge-3">Tier 3</span></td><td>15 / 100</td></tr>
        <tr><td class="hosp-name">Manna Mission Hospital</td><td><span class="badge badge-3">Tier 3</span></td><td>16 / 110</td></tr>
        <tr><td class="hosp-name">North Legon Hospital</td><td><span class="badge badge-3">Tier 3</span></td><td>21 / 170</td></tr>
      </table>
    </div>
  </div>
@endsection
