import tensorflow as tf
from tensorflow.keras.models import load_model
import streamlit as st
import numpy as np

st.header('Image Classification Model (Fruits/Vegetables)')

model = load_model(r"C:\Users\elhad\Videos\py project\fruit_vegetable_model.keras")

data_cat = ['apple','banana','beetroot','bell pepper','cabbage','capsicum','carrot','cauliflower',
            'chilli pepper','corn','cucumber','eggplant','garlic','ginger','grapes','jalepeno',
            'kiwi','lemon','lettuce','mango','onion','orange','paprika','pear','peas','pineapple',
            'pomegranate','potato','raddish','soy beans','spinach','sweetcorn','sweetpotato',
            'tomato','turnip','watermelon']

img_height = 180
img_width = 180

uploaded_file = st.file_uploader("Upload an image...", type=["jpg", "jpeg", "png"])

if uploaded_file is not None:
    st.image(uploaded_file, width=200)

    image_load = tf.keras.utils.load_img(uploaded_file, target_size=(img_height, img_width))
    img_arr = tf.keras.utils.img_to_array(image_load)
    img_bat = tf.expand_dims(img_arr, 0)  


    predict = model.predict(img_bat)
    score = tf.nn.softmax(predict[0])  

    st.write('Veg/Fruit in image is:', f"**{data_cat[np.argmax(score)]}**")
    st.write(f'With accuracy of: **{np.max(score)*1000:.2f}%**')
