# OAKE-Referral — Backend API Brief

This document tells the frontend exactly how to connect to the Python backend.
The backend is already built, tested, and running — it does not need to be
created or modified. Laravel controllers should call these endpoints and
display the real responses, replacing the static sample data shown in the
mockup file.

**Base URL (local development):** `http://127.0.0.1:5001`

Before building forms, confirm the API is running by visiting
`http://127.0.0.1:5001/health` in a browser — it should return
`{"status": "ok", "hospitals_loaded": 24}`.

---

## 1. Existing patient page

**Endpoint:** `POST /predict-existing`
**Used on:** the "Existing patient" page, when the form is submitted.

### Request body (JSON)

| Field | Type | Values | Notes |
|---|---|---|---|
| `age_group` | integer | 0–4 | 0=0-17, 1=18-29, 2=30-49, 3=50-69, 4=70+ |
| `gender` | string | `"F"`, `"M"`, `"U"` | |
| `admission_type` | string | `"Elective"`, `"Emergency"`, `"Newborn"`, `"Not Available"`, `"Trauma"`, `"Urgent"` | |
| `length_of_stay` | number | e.g. `2` | days |
| `mdc_code` | integer | 0–25 | diagnosis category — get the list from `GET /mdc-categories` |
| `severity_code` | integer | 1–4 | 1=Minor, 2=Moderate, 3=Major, 4=Extreme |
| `risk_of_mortality_code` | integer | 0–3 | 0=Minor, 1=Moderate, 2=Major, 3=Extreme |
| `medical_surgical` | string | `"Medical"`, `"Surgical"` | |
| `emergency_department` | boolean | `true`/`false` | |
| `current_hospital_name` | string | e.g. `"Korle Bu Teaching Hospital"` | used only for the "continue care at X" message |

### Example request
```json
{
  "age_group": 1,
  "gender": "F",
  "admission_type": "Elective",
  "length_of_stay": 2,
  "mdc_code": 14,
  "severity_code": 1,
  "risk_of_mortality_code": 0,
  "medical_surgical": "Surgical",
  "emergency_department": false,
  "current_hospital_name": "Korle Bu Teaching Hospital"
}
```

### Response — two possible shapes

**If suitable for referral:**
```json
{
  "decision": "SUITABLE for downward referral",
  "confidence": 98.6,
  "required_specialty": "Obstetrics & Gynaecology",
  "recommendation": {
    "status": "ok",
    "results": [
      {"hospital_name": "Inkoom Hospital", "hospital_tier": 3, "beds_available": 15, "number_of_beds": 100, "score": 1.0}
    ]
  }
}
```
Display: the decision + confidence as a green success message, then render `recommendation.results` as a table (same layout as the mockup's "Recommended hospitals" table).

**If not suitable:**
```json
{
  "decision": "NOT suitable for downward referral",
  "confidence": 86.6,
  "action": "Continue care at Ridge Hospital (Greater Accra Regional Hospital)",
  "recommendation": null
}
```
Display: the decision + confidence as a red/neutral message, show the `action` text, do not render a hospital table.

**Possible error (missing/invalid field):**
```json
{ "status": "error", "message": "Invalid or missing field: 'age_group'" }
```
HTTP status will be 400 — show a form validation error.

---

## 2. New / ambulance patient page

**Endpoint:** `POST /predict-new`
**Used on:** the "New patient" page, when the form is submitted.

### Request body
```json
{ "required_specialty": "Accident & Emergency" }
```
The dropdown of valid specialty values should come from the `specialities` field in `GET /hospitals` (see below) — don't hardcode a list, since it should reflect what's actually offered.

### Response
```json
{
  "decision": "New patient -- routing to best available hospital",
  "recommendation": {
    "status": "ok",
    "results": [
      {"hospital_name": "St Martin's Memorial Hospital", "hospital_tier": 3, "beds_available": 18, "number_of_beds": 150, "score": 1.0}
    ]
  }
}
```
Same table rendering as above. Note: unlike the existing-patient flow, major (Tier 1) hospitals can appear here — that's correct, not a bug.

If no hospital matches: `recommendation.status` will be `"no_match"` with a `message` field — display that message instead of an empty table.

---

## 3. Hospital directory page

**Endpoint:** `GET /hospitals`
**Used on:** the "Hospitals" page, on page load (no form needed).

Returns an array of all 24 hospitals:
```json
[
  {
    "hospital_id": 1,
    "hospital_name": "Korle Bu Teaching Hospital",
    "hospital_level": "Teaching Hospital",
    "hospital_tier": 1,
    "region": "Greater Accra",
    "location": "Guggisberg Avenue, Ablekuma South District, Accra",
    "specialities": "Medicine, Child Health, Obstetrics & Gynaecology, ...",
    "number_of_beds": 2000,
    "beds_occupied": 1923,
    "beds_available": 77
  }
]
```
Render as the table shown in the mockup. The search box and tier filter (shown in the mockup) can be implemented as client-side JavaScript filtering on this already-loaded array — no need to re-call the API per keystroke.

---

## 4. Model insights & ethics page

No live endpoint — this page is static content. Use the real figures below (from the project's Phase 3 and Phase 6 evaluation) rather than the mockup's placeholder numbers, which already match:

- Feature importance: Risk of Mortality 30%, Severity of Illness 22%, Diagnosis category ~8%, Length of stay ~6%
- Known limitation text: trained on U.S. clinical data (no public Ghanaian dataset exists), not yet validated on Ghanaian patients; recall is lower for patients aged 18–49 than for patients 70+; system is decision support only, not a replacement for clinical judgment.

---

## General notes for the IDE

- All requests should set header `Content-Type: application/json`.
- The API has CORS enabled, so browser-side JavaScript (fetch/axios) can call it directly if preferred — but calling it from the Laravel backend (via Laravel's `Http::post(...)` client) is equally valid and keeps the API URL out of client-side code.
- The Python API must be running (`python app.py`) alongside Laravel during local development — this is a two-process local setup, not a single app.
- Every JSON example in this document is a **real response**, tested against the actual trained model and hospital data — not illustrative/fake.
