"""
Smart Hospital Referral System -- Python API
Thin Flask wrapper around the tested classifier + recommendation pipeline
from Patient_Referral_AI.ipynb (Phases 3-5). Laravel calls these endpoints
over HTTP; all the actual AI/logic lives here, unchanged from the notebook.

Run: python app.py
Runs on http://127.0.0.1:5000
"""

from flask import Flask, request, jsonify
from flask_cors import CORS
import joblib
import pandas as pd

app = Flask(__name__)
CORS(app)  # allows Laravel (different port) to call this API from the browser if needed

# ---------- Load model + data once at startup ----------
model = joblib.load("models/referral_classifier_rf.joblib")
hospitals = pd.read_csv("data/hospitals_phase4.csv")

FEATURE_COLUMNS = [
    'Age Group', 'Length of Stay', 'CCSR Diagnosis Code', 'CCSR Procedure Code',
    'APR DRG Code', 'APR MDC Code', 'APR Severity of Illness Code', 'APR Risk of Mortality Code',
    'APR Medical Surgical Description', 'Emergency Department Indicator',
    'Gender_F', 'Gender_M', 'Gender_U',
    'Admission_Elective', 'Admission_Emergency', 'Admission_Newborn',
    'Admission_Not Available', 'Admission_Trauma', 'Admission_Urgent'
]

# Default/median values for fields that are label-encoded from training data and can't
# be meaningfully reconstructed from raw clinical input without the original encoders.
# These contribute only ~5-8% feature importance each (see Phase 3 audit), so medians
# are a safe placeholder for a demo -- documented here explicitly, not hidden.
DEFAULT_DIAGNOSIS_CODE = 187
DEFAULT_PROCEDURE_CODE = 224
DEFAULT_DRG_CODE = 383

# MDC code -> specialty (from Phase 5). Some codes intentionally share a specialty
# (e.g. MDC 19 "Mental Diseases" and MDC 20 "Alcohol/Drug Use" both -> Psychiatry,
# since that's the only matching specialty term in the hospital dataset).
MDC_TO_SPECIALTY = {
    0: "Medicine", 1: "Medicine", 2: "Ophthalmology", 3: "ENT", 4: "Medicine",
    5: "Cardiology", 6: "Gastroenterology", 7: "Gastroenterology", 8: "Orthopaedics",
    9: "Dermatology", 10: "Internal Medicine", 11: "Nephrology", 12: "Urology",
    13: "Gynaecology", 14: "Obstetrics & Gynaecology", 15: "Neonatology", 16: "Haematology",
    17: "Oncology", 18: "Medicine", 19: "Psychiatry", 20: "Psychiatry",
    21: "Emergency Medicine", 22: "Plastic Surgery", 23: "General Medicine",
    24: "Accident & Emergency", 25: "HIV/ART"
}

# Human-readable MDC categories, for populating a dropdown on the Laravel side
MDC_CATEGORIES = {
    0: "Pre-MDC / ungrouped", 1: "Nervous system", 2: "Eye", 3: "Ear, nose, mouth, throat",
    4: "Respiratory system", 5: "Circulatory system", 6: "Digestive system",
    7: "Hepatobiliary system and pancreas", 8: "Musculoskeletal system and connective tissue",
    9: "Skin, subcutaneous tissue, breast", 10: "Endocrine, nutritional, metabolic",
    11: "Kidney and urinary tract", 12: "Male reproductive system", 13: "Female reproductive system",
    14: "Pregnancy, childbirth, puerperium", 15: "Newborns and neonates",
    16: "Blood, blood-forming organs, immunological", 17: "Myeloproliferative diseases, neoplasms",
    18: "Infectious and parasitic diseases", 19: "Mental diseases and disorders",
    20: "Alcohol/drug use and induced disorders", 21: "Injuries, poisonings, toxic effects",
    22: "Burns", 23: "Factors influencing health status", 24: "Multiple significant trauma",
    25: "HIV infection"
}

GLOBAL_MAX_TIER = hospitals['hospital_tier'].max()


def build_feature_row(payload):
    """Convert a friendly JSON payload into the exact encoded feature row the model expects."""
    row = {
        'Age Group': int(payload['age_group']),                      # 0-4
        'Length of Stay': float(payload['length_of_stay']),
        'CCSR Diagnosis Code': DEFAULT_DIAGNOSIS_CODE,
        'CCSR Procedure Code': DEFAULT_PROCEDURE_CODE,
        'APR DRG Code': DEFAULT_DRG_CODE,
        'APR MDC Code': int(payload['mdc_code']),                    # 0-25
        'APR Severity of Illness Code': int(payload['severity_code']),      # 1-4
        'APR Risk of Mortality Code': int(payload['risk_of_mortality_code']),  # 0-3
        'APR Medical Surgical Description': 1 if payload['medical_surgical'] == 'Surgical' else 0,
        'Emergency Department Indicator': 1 if payload.get('emergency_department') else 0,
        'Gender_F': payload['gender'] == 'F',
        'Gender_M': payload['gender'] == 'M',
        'Gender_U': payload['gender'] == 'U',
        'Admission_Elective': payload['admission_type'] == 'Elective',
        'Admission_Emergency': payload['admission_type'] == 'Emergency',
        'Admission_Newborn': payload['admission_type'] == 'Newborn',
        'Admission_Not Available': payload['admission_type'] == 'Not Available',
        'Admission_Trauma': payload['admission_type'] == 'Trauma',
        'Admission_Urgent': payload['admission_type'] == 'Urgent',
    }
    return pd.DataFrame([row])[FEATURE_COLUMNS]


def recommend_hospital(required_specialty, exclude_major_referral=True, top_n=5):
    """Same logic as the notebook (Phase 4/5, post distance-removal fix)."""
    candidates = hospitals[hospitals['beds_available'] > 0].copy()
    candidates = candidates[candidates['specialities'].str.contains(required_specialty, case=False, na=False)]
    if exclude_major_referral:
        candidates = candidates[candidates['hospital_tier'] != 1]

    if candidates.empty:
        return {"status": "no_match",
                "message": f"No hospital with available beds and '{required_specialty}' found."}

    candidates['bed_ratio'] = candidates['beds_available'] / candidates['number_of_beds']
    candidates['bed_score'] = candidates['bed_ratio'] / candidates['bed_ratio'].max()
    candidates['tier_score'] = candidates['hospital_tier'] / GLOBAL_MAX_TIER
    candidates['score'] = 0.65 * candidates['bed_score'] + 0.35 * candidates['tier_score']

    result = candidates.sort_values('score', ascending=False).head(top_n)
    cols = ['hospital_name', 'hospital_tier', 'beds_available', 'number_of_beds', 'score']
    return {"status": "ok", "results": result[cols].round(3).to_dict(orient='records')}


# ---------- Routes ----------

@app.route('/health', methods=['GET'])
def health():
    return jsonify({"status": "ok", "hospitals_loaded": len(hospitals)})


@app.route('/mdc-categories', methods=['GET'])
def mdc_categories():
    """For populating the diagnosis-category dropdown on the Laravel form."""
    return jsonify(MDC_CATEGORIES)


@app.route('/predict-existing', methods=['POST'])
def predict_existing():
    """
    Full pipeline for an existing patient: classifier -> (if suitable) recommendation.
    Expected JSON body: age_group, gender, admission_type, length_of_stay, mdc_code,
    severity_code, risk_of_mortality_code, medical_surgical, emergency_department,
    current_hospital_name (for the 'continue care at X' message only).
    """
    payload = request.get_json()
    try:
        patient_row = build_feature_row(payload)
    except (KeyError, ValueError) as e:
        return jsonify({"status": "error", "message": f"Invalid or missing field: {e}"}), 400

    current_hospital = payload.get('current_hospital_name', 'the current hospital')
    mdc_code = int(payload['mdc_code'])

    pred = model.predict(patient_row)[0]
    proba = model.predict_proba(patient_row)[0]

    if pred == 0:
        return jsonify({
            "decision": "NOT suitable for downward referral",
            "confidence": round(float(proba[0]) * 100, 1),
            "action": f"Continue care at {current_hospital}",
            "recommendation": None
        })

    specialty = MDC_TO_SPECIALTY.get(mdc_code, "Medicine")
    rec = recommend_hospital(specialty, exclude_major_referral=True)
    return jsonify({
        "decision": "SUITABLE for downward referral",
        "confidence": round(float(proba[1]) * 100, 1),
        "required_specialty": specialty,
        "recommendation": rec
    })


@app.route('/predict-new', methods=['POST'])
def predict_new():
    """New/ambulance patient -- no classifier step, straight to recommendation, major hospitals included."""
    payload = request.get_json()
    if 'required_specialty' not in payload:
        return jsonify({"status": "error", "message": "Missing field: required_specialty"}), 400

    rec = recommend_hospital(payload['required_specialty'], exclude_major_referral=False)
    return jsonify({
        "decision": "New patient -- routing to best available hospital",
        "recommendation": rec
    })


@app.route('/hospitals', methods=['GET'])
def get_hospitals():
    """Full hospital directory, for the Hospital Directory page."""
    return jsonify(hospitals.to_dict(orient='records'))


if __name__ == '__main__':
    app.run(port=5001, debug=True)
