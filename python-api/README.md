# Python API — Referral Classifier & Recommendation Service

Thin Flask wrapper around the tested pipeline from `Patient_Referral_AI.ipynb`
(Phases 3–5). Laravel calls these endpoints over HTTP.

## Setup

```bash
cd python-api
python3.12 -m venv venv
source venv/bin/activate
pip install -r requirements.txt
python app.py
```

Runs on `http://127.0.0.1:5001`. Keep this terminal open while demoing —
Laravel needs it running. Each time you open a new terminal, re-run
`source venv/bin/activate` before `python app.py`.

## Endpoints

### `GET /health`
Quick check that the API and model loaded correctly.

### `GET /mdc-categories`
Returns the 26 diagnosis category options (for a dropdown on the patient form).

### `GET /hospitals`
Returns all 24 hospitals with tier, specialties, and bed data.

### `POST /predict-existing`
Full pipeline for a patient already in a major hospital.

**Request body:**
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

- `age_group`: 0=0-17, 1=18-29, 2=30-49, 3=50-69, 4=70+
- `gender`: "F", "M", or "U"
- `admission_type`: "Elective", "Emergency", "Newborn", "Not Available", "Trauma", "Urgent"
- `mdc_code`: 0-25 (see `/mdc-categories`)
- `severity_code`: 1=Minor, 2=Moderate, 3=Major, 4=Extreme
- `risk_of_mortality_code`: 0=Minor, 1=Moderate, 2=Major, 3=Extreme
- `medical_surgical`: "Medical" or "Surgical"

**Response:** decision, confidence %, and (if suitable) a ranked hospital list.

### `POST /predict-new`
For new/ambulance patients — skips the classifier, goes straight to recommendation.

**Request body:**
```json
{ "required_specialty": "Accident & Emergency" }
```

## Note on default values

`CCSR Diagnosis Code`, `CCSR Procedure Code`, and `APR DRG Code` are label-encoded
from the original training data and can't be meaningfully reconstructed from raw
clinical input without the original encoders. They're set to fixed median values
(documented in `app.py`) since they contribute only ~5-8% feature importance each —
`APR Severity of Illness` and `APR Risk of Mortality` dominate the decision (~52%
combined), so this simplification doesn't materially affect predictions.
