@extends('layouts.app')

@section('content')
  <div id="new" class="page active">
    <h1>New / Ambulance patient</h1>
    <p class="subtitle">For patients not yet placed at a hospital. Select the specialty needed and get a ranked list of hospitals with available beds.</p>

    <div class="card">
      <div class="field">
        <label>Required specialty</label>
        <select><option>Accident &amp; Emergency</option><option>Obstetrics &amp; Gynaecology</option><option>Cardiology</option><option>Paediatrics</option></select>
      </div>
      <a class="btn btn-primary submit-btn" style="margin-top:14px; display:inline-block; text-align:center;" href="{{ route('hospitals') }}">Find hospital</a>
    </div>

    <div style="margin-top:28px; max-width:560px;">
      <h2>Ranked results</h2>
      <table>
        <tr><th>Hospital</th><th>Tier</th><th>Beds available</th></tr>
        <tr><td class="hosp-name">St Martin's Memorial Hospital</td><td><span class="badge badge-3">Tier 3</span></td><td>18 / 150</td></tr>
        <tr><td class="hosp-name">Achimota Hospital</td><td><span class="badge badge-2">Tier 2</span></td><td>22 / 220</td></tr>
        <tr><td class="hosp-name">Korle Bu Teaching Hospital</td><td><span class="badge badge-1">Tier 1</span></td><td>77 / 2000</td></tr>
      </table>
    </div>
  </div>
@endsection
