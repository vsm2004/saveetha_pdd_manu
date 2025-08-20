import numpy as np
import pandas as pd
p = pd.read_csv(r"C:\Users\DELL'\Desktop\pdd 2025 sse\Integration process\stay_data_4300_entries.csv")
import sklearn
from sklearn.preprocessing import LabelEncoder
le=LabelEncoder()
p['Stay_Type_Label'] = le.fit_transform(p['Stay Type'])
from sklearn.preprocessing import MultiLabelBinarizer
p['Amenities_list'] = p['Amenities'].apply(lambda x: [i.strip() for i in str(x).split(',')])
mlb = MultiLabelBinarizer()
amenities_p = pd.DataFrame(mlb.fit_transform(p['Amenities_list']), columns=mlb.classes_)
p = pd.concat([p.drop(columns=['Amenities', 'Amenities_list']), amenities_p], axis=1)
p['Country'] = le.fit_transform(p['Country'])
p['City'] = le.fit_transform(p['City'])
import re
def extract_rent(value):
    # Remove currency symbols, commas, etc.
    if pd.isnull(value):
        return None
    value = re.sub(r'[^\d.]', '', str(value))
    try:
        return float(value)
    except:
        return None
p['Price per Night (USD)']=p['Price per Night (USD)'].apply(extract_rent)
p = p.drop(columns=['Stay Name', 'Stay Type'])
y=p['Stay_Type_Label']
X=p.drop(columns=['Stay_Type_Label'])
from sklearn.model_selection import train_test_split
X_train, X_test, y_train, y_test = train_test_split(
    X, y, test_size=0.2, random_state=42, stratify=y
)
from xgboost import XGBClassifier
model = XGBClassifier(use_label_encoder=False, eval_metric='mlogloss')
model.fit(X_train, y_train)
y_pred = model.predict(X_test)
from sklearn.metrics import accuracy_score
accuracy = accuracy_score(y_test, y_pred)
print("Accuracy:", accuracy)
from sklearn.ensemble import RandomForestClassifier
from sklearn.linear_model import LogisticRegression
from sklearn.metrics import accuracy_score, classification_report, confusion_matrix
import seaborn as sns
import matplotlib.pyplot as plt
from sklearn.utils import shuffle
y_shuffled = shuffle(y, random_state=42)
model.fit(X_train, y_shuffled[:len(y_train)])
print("Accuracy with shuffled labels:", model.score(X_test, y_test))
rf_model = RandomForestClassifier(random_state=42)
rf_model.fit(X_train, y_train)
rf_preds = rf_model.predict(X_test)
from sklearn.utils import shuffle
from sklearn.ensemble import RandomForestClassifier
from sklearn.linear_model import LogisticRegression
y_shuffled = shuffle(y, random_state=42)
rf_model = RandomForestClassifier(random_state=42)
rf_model.fit(X_train, y_shuffled[:len(y_train)])
lr_model = LogisticRegression(max_iter=1000)
lr_model.fit(X_train, y_train)
lr_preds = lr_model.predict(X_test)
y_shuffled = shuffle(y, random_state=42)
lr_model = LogisticRegression(max_iter=1000, random_state=42)
lr_model.fit(X_train, y_shuffled[:len(y_train)])
print("Random Forest Accuracy:", accuracy_score(y_test, rf_preds))
print("Random Forest Accuracy with shuffled labels:", rf_model.score(X_test, y_test))
print("Logistic Regression Accuracy:", accuracy_score(y_test, lr_preds))
print("Logistic Regression Accuracy with shuffled labels:", lr_model.score(X_test, y_test))
import joblib
joblib.dump(model, 'stay_type_classifier.pkl')
