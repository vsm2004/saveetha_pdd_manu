import spacy
from fuzzywuzzy import fuzz
import re
import nltk
from rooms_data import rooms
# Download once if not already present
nltk.download('punkt')
nltk.download('stopwords')
# Load spaCy model
nlp = spacy.load("en_core_web_sm")
def preprocess(text):
    """Lowercase and tokenize the input text"""
    doc = nlp(text.lower())
    return [token.lemma_ for token in doc if not token.is_stop and token.is_alpha]
def extract_keywords(user_input):
    """Use regex and spaCy to extract meaningful keywords"""
    keywords = preprocess(user_input)
    return list(set(keywords))
def match_rooms(keywords, threshold=70):
    """Fuzzy match input keywords with available room features"""
    matched_rooms = []
    for room in rooms:
        score = 0
        for feature in room["features"]:
            for keyword in keywords:
                similarity = fuzz.partial_ratio(feature.lower(), keyword.lower())
                if similarity > threshold:
                    score += 1
                    break  # Avoid double counting
        if score > 0:
            matched_rooms.append((room, score))

    # This line was wrongly indented — now it's fixed:
    matched_rooms.sort(key=lambda x: x[1], reverse=True)

    return [room for room, _ in matched_rooms]
def ai_genie_chatbot():
    print("🧞‍♂️ AI-Genie: Tell me the kind of room and features you need!")
    user_input = input("You: ")

    keywords = extract_keywords(user_input)
    print(f"\n🔍 Extracted keywords: {keywords}")

    matched = match_rooms(keywords)

    if matched:
        print("\n🏨 Matching Rooms Found:")
        for room in matched:
            print(f"- {room['name']} | ₹{room['price']} | Features: {', '.join(room['features'])}")
    else:
        print("❌ Sorry, I couldn't find any rooms matching your request.")

if __name__ == "__main__":
    ai_genie_chatbot()
