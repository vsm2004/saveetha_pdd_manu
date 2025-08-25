import vosk
import sounddevice as sd
import json
import queue

# Your rooms data
from rooms_data import rooms

q = queue.Queue()

def callback(indata, frames, time, status):
    if status:
        print(status)
    q.put(bytes(indata))

def extract_keywords(text):
    from nltk.corpus import stopwords
    from nltk.tokenize import word_tokenize
    from fuzzywuzzy import fuzz
    
    stop_words = set(stopwords.words('english'))
    tokens = word_tokenize(text.lower())
    return [word for word in tokens if word.isalpha() and word not in stop_words]

def match_rooms(keywords, threshold=70):
    from fuzzywuzzy import fuzz
    
    matched = []
    for room in rooms:
        score = sum(
            1 for feature in room['features']
            for keyword in keywords
            if fuzz.partial_ratio(feature.lower(), keyword.lower()) > threshold
        )
        if score > 0:
            matched.append((room, score))
    matched.sort(key=lambda x: x[1], reverse=True)
    return [room[0] for room in matched]

def ai_genie_voicebox():
    model = vosk.Model(r"C:\vosk-model-small-en-us-0.15\vosk-model-small-en-us-0.15")
    rec = vosk.KaldiRecognizer(model, 16000)
    
    with sd.RawInputStream(samplerate=16000, blocksize=8000, dtype='int16',
                           channels=1, callback=callback):
        print("🧞‍♂️ Speak your room and feature requirements...")
        while True:
            data = q.get()
            if rec.AcceptWaveform(data):
                result = json.loads(rec.Result())
                user_input = result.get("text", "")
                if user_input:
                    print(f"🎙️ You said: {user_input}")
                    keywords = extract_keywords(user_input)
                    print(f"🔍 Extracted keywords: {keywords}")
                    matched_rooms = match_rooms(keywords)
                    if matched_rooms:
                        print("\n🏨 Matching Rooms Found:")
                        for room in matched_rooms:
                            print(f"- {room['name']} | ₹{room['price']} | Features: {', '.join(room['features'])}")
                    else:
                        print("❌ No matching rooms found.")
                break

if __name__ == "__main__":
    ai_genie_voicebox()
