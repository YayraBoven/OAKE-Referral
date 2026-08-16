@extends('layouts.app')

@section('content')
  <div id="insights" class="page active">
    <h1>Model insights &amp; ethics</h1>
    <p class="subtitle">Transparency on how the classifier makes decisions and its known limitations.</p>

    <h2>What drives the model's decisions</h2>
    <div class="bar-row"><div class="bar-label">Risk of mortality</div><div class="bar-track"><div class="bar-fill" style="width:100%"></div></div><div class="bar-val">30%</div></div>
    <div class="bar-row"><div class="bar-label">Severity of illness</div><div class="bar-track"><div class="bar-fill" style="width:73%"></div></div><div class="bar-val">22%</div></div>
    <div class="bar-row"><div class="bar-label">Diagnosis category</div><div class="bar-track"><div class="bar-fill" style="width:27%"></div></div><div class="bar-val">8%</div></div>
    <div class="bar-row"><div class="bar-label">Length of stay</div><div class="bar-track"><div class="bar-fill" style="width:21%"></div></div><div class="bar-val">6%</div></div>

    <div class="note-box">
      <strong>Known limitation:</strong> the classifier is trained on U.S. clinical data, since no public Ghanaian dataset exists. It has not been validated on Ghanaian patients. Recall is also lower for patients aged 18–49 than for patients 70+. This system is decision support only — not a replacement for clinical judgment.
    </div>
  </div>
@endsection
