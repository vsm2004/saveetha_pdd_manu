import nltk
import textblob
import sklearn
import emoji
import nltk
nltk.download('punkt')
nltk.download('stopwords')
nltk.download('averaged_perceptron_tagger')
nltk.download('vader_lexicon')
from textblob import TextBlob
from sklearn.feature_extraction.text import CountVectorizer
import re
from nltk.corpus import stopwords
from nltk.sentiment import SentimentIntensityAnalyzer
import math
emoji_dict = {
    "🤩": "very satisfied",
    "😊": "satisfied",
    "🙂": "okayish",
    "😐": "not so good",
    "😡": "very poor"
}
EMOJI_MAP = {
    "🤩": 1.0,
    "😊": 0.6,
    "🙂": 0.2,
    "😐": -0.2,
    "😡": -1.0
}
def star_to_score(star):
    return (star - 3) / 2.0
def compute_overall_score(text_score, emoji_score, star_score,
                          eps=1e-2, label_thresh=0.05):
    a = max(-1.0, min(1.0, float(text_score)))
    b = max(-1.0, min(1.0, float(emoji_score)))
    c = max(-1.0, min(1.0, float(star_score)))
    scores = [a, b, c]

    # Count signs
    neg_count = sum(1 for s in scores if s < 0)
    pos_count = sum(1 for s in scores if s > 0)

    # Force sign logic
    if neg_count >= 2:
        forced_sign = -1.0
    else:
        if pos_count > neg_count:
            forced_sign = 1.0
        elif neg_count > pos_count:
            forced_sign = -1.0
        else:
            forced_sign = 1.0 if sum(scores) >= 0 else -1.0

    # Magnitude with eps to avoid zero collapse
    abs_vals = [max(abs(s), eps) for s in scores]
    magnitude = (abs_vals[0] * abs_vals[1] * abs_vals[2]) ** (1/3)

    overall = forced_sign * magnitude
    overall = max(-1.0, min(1.0, overall))

    # Label
    if overall >= label_thresh:
        label = "Positive"
    elif overall <= -label_thresh:
        label = "Negative"
    else:
        label = "Neutral"

    return overall, label
import re

def extract_suggestions(text):
    negative_keywords = [
        "not", "no", "never", "poor", "bad", "dirty", "horrible", "unpleasant", "unfriendly",
        "broken", "smelly", "rude", "slow", "loud", "tasteless", "worst", "hard", "uncomfortable",
        "not working", "not functioning", "faulty"
    ]

    # Split at commas, semicolons, "but", "though", "however"
    parts = re.split(r'\b(?:but|though|however)\b|[,;]', text, flags=re.IGNORECASE)
    suggestions = []

    # Only consider segments after these words for complaints
    # Find positions of "but"/"though"/"however" and take subsequent segments
    trigger_positions = [m.end() for m in re.finditer(r'\b(?:but|though|however)\b', text, flags=re.IGNORECASE)]

    if trigger_positions:
        after_parts = []
        for pos in trigger_positions:
            after_parts.append(text[pos:].strip())

        for segment in after_parts:
            seg_lower = segment.lower()
            if any(neg in seg_lower for neg in negative_keywords):
                cleaned = segment.strip().capitalize()
                if cleaned:
                    suggestions.append(cleaned)
    else:
        # If no "but" found, still check for negatives in all parts
        for part in parts:
            part_lower = part.lower()
            if any(neg in part_lower for neg in negative_keywords):
                cleaned = part.strip().capitalize()
                if cleaned:
                    suggestions.append(cleaned)

    return suggestions
def analyze_feedback(emoji_input, star_rating, text_feedback):
    print("\n--- FEEDBACK ANALYSIS ---")
    emoji_meaning = emoji_dict.get(emoji_input, "Unknown")
    print(f"Emoji selected: {emoji_input} ({emoji_meaning})")
    print(f"Star rating given: {star_rating}/5")

    # Text sentiment
    sia = SentimentIntensityAnalyzer()
    sentiment_scores = sia.polarity_scores(text_feedback)
    compound = sentiment_scores['compound']
    print(text_feedback)

    if compound >= 0.4:
        sentiment = "Positive"
    elif compound <= -0.4:
        sentiment = "Negative"
    else:
        sentiment = "Neutral"
    print(f"Overall text sentiment: {sentiment}")

    # NEW: Cumulative score calculation
    emoji_score = EMOJI_MAP.get(emoji_input, 0.0)
    star_score = star_to_score(star_rating)
    cumulative_score, cumulative_label = compute_overall_score(compound, emoji_score, star_score)
    print(f"Cumulative score: {cumulative_score:.3f} → {cumulative_label}")

    # Suggestions if negative or neutral
    if cumulative_label in ["Negative", "Neutral"]:
        suggestions = extract_suggestions(text_feedback)
        if suggestions:
            print("\n🔧 Suggested areas for improvement:")
            for s in suggestions:
                print(f"- {s}")
        else:
            print("⚠️ Feedback is negative but no clear suggestions found.")
    else:
        print("✅ Positive feedback. No improvements suggested.")

    print("\n[✅ Feedback analyzed independently from all inputs]")
user_emoji = input("Enter emoji: ")
star_rating = int(input("Enter star rating (1-5): "))
text_feedback = input("Enter your feedback: ")
analyze_feedback(user_emoji, star_rating, text_feedback)
