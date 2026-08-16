# OAKE-Referral - Smart Hospital Referral and Bed Availability System for Reducing No Bed Syndrome in Ghana

Course: CS254_B: Introduction to Artificial Intelligence, Cohort B

Team Members:
Kwadwo Adjei Awuah Akotoh - 63542028
Joy Naa Ayi-Kooley Addy - 89342028
Franca Opokua Haligah - 47562028
Harriet Yayra Boven Fiahagbe - 59722028

Project Overview

Ghana's major referral hospitals (Korle Bu, Ridge, 37 Military) are chronically overcrowded, a problem known as No Bed Syndrome which is driven by two causes: no real-time visibility into which hospitals have available beds, and stabilised patients occupying beds longer than clinically necessary. We then introduce OAKE Referral(Optimized AI for Knowledge-based Referral and Bed Management). OAKE Referral is an intelligent decision-support system designed to assist healthcare providers in identifying patients suitable for downward referral while recommending the most appropriate receiving hospital based on bed availability, specialty services and hospital level.

OAKE-Referral addresses both causes with two connected tools:
1. Referral classifier (supervised learning — Random Forest) — predicts whether a hospitalised patient is stable enough to move to a lower-level hospital.
2. Hospital recommendation engine(rule-based) — matches patients (both those cleared for transfer and new ambulance arrivals) to an appropriate hospital among 24 real Greater Accra facilities, based on specialty, hospital tier, and live bed availability.



Our Project structure

OAKE-Referral-Project/
├── python-api/              # Trained model + recommendation logic, exposed as a Flask API
│   ├── app.py
│   ├── models/
│   │   └── referral_classifier_rf.joblib
│   ├── data/
│   │   └── hospitals_phase4.csv
│   ├── requirements.txt
│   └── README.md            # Full API endpoint documentation
├── oake-referral/            # Laravel frontend
│   ├── app/
│   ├── routes/
│   ├── resources/views/
│   └── composer.json
                         

etup and installation

The system has two parts that run as separate local processes: the Python API (serves the trained model) and the Laravel app (the web frontend). Both must be running at the same time.

1. Python API

Requirements:Python 3.12 (not 3.13+ — some dependencies may lack pre-built packages for very new Python versions).

cd python-api
python3.12 -m venv venv
source venv/bin/activate        on Windows:venv\Scripts\activate
pip install -r requirements.txt


2. Laravel frontend

Requirements:PHP 8.2+, Composer.

cd oake-referral
composer install
cp .env.example .env            # if .env doesn't already exist
php artisan key:generate


Running the system

Open two terminal windows:

Terminal 1 — start the Python API:
cd python-api
source venv/bin/activate
python app.py

Runs on `http://127.0.0.1:5001`. Confirm it's working by visiting `http://127.0.0.1:5001/health` in a browser — should show `{"status": "ok", "hospitals_loaded": 24}`.

Terminal 2 — start the Laravel app:
cd oake-referral
php artisan serve
Runs on `http://127.0.0.1:8000`. Open this address in a browser to use the system.

Both must stay running for the site to work — closing either terminal stops that part of the system.

Usage example

Using the web interface:** open `http://127.0.0.1:8000`, go to "Existing patient," fill in a patient's clinical details (age group, severity, risk of mortality, admission type, length of stay), and submit. The system will return a referral decision and, if suitable, a ranked list of appropriate hospitals.

Calling the API directly (useful for testing without the web UI):
```bash
curl -X POST http://127.0.0.1:5001/predict-existing \
  -H "Content-Type: application/json" \
  -d '{
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
  }'
```

Expected response:
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

Reproducing the model
The trained classifier (`python-api/models/referral_classifier_rf.joblib`) was produced by running `notebooks/Patient_Referral_AI.ipynb` end to end against the SPARCS Hospital Inpatient Discharge dataset (~2.1 million records) and `LengthOfStay.csv`. These raw datasets are large and are not committed to this repository — see the notebook's data-loading cell for source download instructions. Running the notebook top to bottom regenerates the model file with a fixed random seed (`random_state=42`), so results should be reproducible.

Known limitations
- The classifier is trained on de-identified United States clinical data, since no public Ghanaian dataset exists, and has not been validated against real Ghanaian patients.
- Classifier recall is lower for patients aged 18–49 than for patients 70+ (see the Ethical Considerations section of the final report).
- This is a local development setup, not a production deployment — see `docs/OAKE_Referral_Final_Report.docx`, Section 7, for full limitations and future work.

References
Full references and the AI Use Declaration are in `docs/OAKE_Referral_Final_Report.docx`.
