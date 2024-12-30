import cv2
import numpy as np
import os
import sys
import json
import logging

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

def main():
    try:
        # Validasi argumen
        if len(sys.argv) != 4:
            result = {"error": f"Invalid number of arguments. Expected 4, got {len(sys.argv)}"}
            print(json.dumps(result))
            return

        # Parse arguments
        image_path = sys.argv[1]
        training_dir = sys.argv[2]
        confidence_threshold = float(sys.argv[3])

        # Validasi file dan direktori
        if not os.path.exists(image_path):
            result = {"error": "Image file not found"}
            print(json.dumps(result))
            return

        if not os.path.exists(training_dir):
            result = {"error": "Training directory not found"}
            print(json.dumps(result))
            return

        # Load image
        image = cv2.imread(image_path, cv2.IMREAD_GRAYSCALE)
        if image is None:
            result = {"error": "Failed to load image"}
            print(json.dumps(result))
            return

        # Load face cascade
        face_cascade = cv2.CascadeClassifier(cv2.data.haarcascades + 'haarcascade_frontalface_default.xml')
        faces = face_cascade.detectMultiScale(image, 1.1, 4)

        if len(faces) == 0:
            result = {"match": False, "confidence": 0, "error": "No face detected"}
            print(json.dumps(result))
            return

        # Get the largest face
        (x, y, w, h) = max(faces, key=lambda f: f[2] * f[3])
        face = image[y:y+h, x:x+w]
        
        # Resize for consistency
        face = cv2.resize(face, (200, 200))

        # Load training images
        training_images = []
        for filename in os.listdir(training_dir):
            if filename.endswith((".jpg", ".jpeg", ".png")):
                img_path = os.path.join(training_dir, filename)
                img = cv2.imread(img_path, cv2.IMREAD_GRAYSCALE)
                if img is not None:
                    training_images.append(cv2.resize(img, (200, 200)))

        if not training_images:
            result = {"error": "No training images found"}
            print(json.dumps(result))
            return

        # Create and train LBPH recognizer
        recognizer = cv2.face.LBPHFaceRecognizer_create()
        recognizer.train(training_images, np.array([0] * len(training_images)))

        # Predict
        label, confidence = recognizer.predict(face)
        
        # Convert confidence to percentage (0-100)
        confidence = 100 - min(100, confidence)
        
        result = {
            "match": confidence >= confidence_threshold,
            "confidence": float(confidence)
        }
        
        print(json.dumps(result))

    except Exception as e:
        result = {"error": str(e)}
        print(json.dumps(result))

if __name__ == "__main__":
    main()