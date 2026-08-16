@extends('layouts.app')

@section('content')
  <div id="hospitals" class="page active">
    <h1>Hospital Directory</h1>
    <p class="subtitle">Live view of all 24 hospitals in the network.</p>

    <div class="search-row">
      <input type="text" placeholder="Search Hospitals...">
      <select><option>All tiers</option><option>Tier 1</option><option>Tier 2</option><option>Tier 3</option></select>
    </div>

    <table>
      <tr><th>Hospital</th><th>Tier</th><th>Beds</th></tr>
      <tr>
        <td><div class="hosp-name">Korle Bu Teaching Hospital</div><div class="hosp-loc">Ablekuma South, Accra</div></td>
        <td><span class="badge badge-1">Tier 1</span></td>
        <td><div class="bed-cell"><div class="bed-bar"><div class="bed-fill" style="width:4%"></div></div><span class="bed-text">77 / 2000</span></div></td>
      </tr>
      <tr>
        <td><div class="hosp-name">Maamobi General Hospital</div><div class="hosp-loc">Maamobi, Accra</div></td>
        <td><span class="badge badge-2">Tier 2</span></td>
        <td><div class="bed-cell"><div class="bed-bar"><div class="bed-fill" style="width:11%"></div></div><span class="bed-text">19 / 170</span></div></td>
      </tr>
      <tr>
        <td><div class="hosp-name">Inkoom Hospital</div><div class="hosp-loc">Baatsona, Spintex Road</div></td>
        <td><span class="badge badge-3">Tier 3</span></td>
        <td><div class="bed-cell"><div class="bed-bar"><div class="bed-fill" style="width:15%"></div></div><span class="bed-text">15 / 100</span></div></td>
      </tr>
    </table>
  </div>
@endsection
