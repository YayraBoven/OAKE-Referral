@extends('layouts.app')

@section('content')
  <div id="home" class="page active">
    <div class="hero">
      <div class="eyebrow">Live across 24 Greater Accra facilities</div>
      <h1>OAKE-Referral: routing patients to available capacity, not just the nearest bed.</h1>
      <p class="subtitle">An AI referral classifier and live hospital matching system built to ease No Bed Syndrome across Greater Accra's referral network.</p>
      <div class="hero-cta">
        <a class="btn btn-primary" href="{{ route('existing') }}">Check a patient</a>
        <a class="btn btn-secondary" href="{{ route('hospitals') }}">View hospital capacity</a>
      </div>
    </div>

    <div class="stat-row">
      <div class="stat-box"><div class="num">24</div><div class="lbl">Hospitals tracked</div></div>
      <div class="stat-box"><div class="num">82%</div><div class="lbl">Classifier accuracy</div></div>
      <div class="stat-box"><div class="num">3</div><div class="lbl">Hospital tiers</div></div>
    </div>

    <h2>How it works</h2>
    <div class="steps">
      <div class="step"><div class="n">STEP 1</div><h3>Assess</h3><p>Patient clinical data is checked by the referral classifier.</p></div>
      <div class="step"><div class="n">STEP 2</div><h3>Match</h3><p>If suitable, hospitals are filtered by specialty, tier, and beds.</p></div>
      <div class="step"><div class="n">STEP 3</div><h3>Recommend</h3><p>A ranked list of hospitals is returned.</p></div>
    </div>
  </div>
@endsection
