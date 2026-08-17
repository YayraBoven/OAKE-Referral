OAKE-Referral

Smart Hospital Referral and Bed Availability System for Reducing No Bed Syndrome in Ghana

CS254_B — Introduction to Artificial Intelligence Cohort B 
Group Members
Joy Naa Ayi-Kooley Addy 
Franca Opokua Haligah 
Harriet Yayra Boven Fiahagbe 
Kwadwo Adjei Awuah Akotoh 

Lecturer:Dr. Daniel Addo

This is the single, complete README for the whole project. Component-level READMEs also exist inside python-api/ and oake-referral/ with additional detail on those pieces specifically, but everything needed to get the full system running from a fresh clone is here.

Overview

Ghana's major referral hospitals (Korle Bu, Ridge, 37 Military) are chronically overcrowded, a problem known as No Bed Syndrome, driven by two causes: no real-time visibility into which hospitals have available beds, and stabilised patients occupying beds longer than clinically necessary.

OAKE-Referral addresses both causes with two connected tools:

Referral classifier (supervised learning, Random Forest): predicts whether a hospitalised patient is stable enough to move to a lower-level hospital.
Hospital recommendation engine (rule-based): matches patients (both those cleared for transfer and new/ambulance arrivals) to an appropriate hospital among 24 real Greater Accra facilities, based on specialty, hospital tier, and live bed availability.

A Laravel web app provides the interface; a Python Flask API serves the trained model and recommendation logic. Full methodology, results, and ethics audit are documented in docs/OAKE_Referral_Final_Report.docx.

Project structure
OAKE-Referral-Project/
├── python-api/                        # Trained model + recommendation logic, served as a Flask API
│   ├── app.py
│   ├── models/referral_classifier_rf.joblib
│   ├── data/hospitals_phase4.csv
│   ├── requirements.txt
│   └── README.md                      # API endpoint documentation
├── oake-referral/                     # Laravel web frontend
│   ├── app/Http/Controllers/          # ReferralController, NewPatientController, HospitalController
│   ├── routes/web.php
│   ├── resources/views/               # Blade templates (home, existing, new, hospitals, insights)
│   └── composer.json
├── OAKE_Referral_AI.ipynb             # Full development notebook: data cleaning, model training,
                                      # evaluation, ethics audit (Phases 1-6)


Prerequisites
Python 3.12 (not 3.13+ — some dependencies lack pre-built packages for very new Python versions)
PHP 8.2.x and Composer
Git


DATASETS USED
Hospital length of stay dataset Microsoft. (n.d.). In www.kaggle.com. Retrieved August 16, 2026, from https://www.kaggle.com/datasets/aayushchou/hospital-length-of-stay-dataset-microsof
hasantugra. (2024, October 17). Hospital-inpatient-discharges-prediction. Kaggle. https://www.kaggle.com/code/hasantugra/hospital-inpatient-discharges-prediction



Running the system
The system has two parts that run as separate local processes simultaneously. Open two terminal windows.

Terminal 1 Python API:
bash
cd python-api
source venv/bin/activate
python app.py

Runs on http://127.0.0.1:5001. Confirm it's working by visiting http://127.0.0.1:5001/health — should show {"status": "ok", "hospitals_loaded": 24}.

Terminal 2 — Laravel app:
bash
cd oake-referral
php artisan serve

Runs on http://127.0.0.1:8000. Open this in a browser to use the system. Both terminals must stay running.
