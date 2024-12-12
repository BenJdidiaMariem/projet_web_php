# -*- coding: utf-8 -*-
import tensorflow as tf
from tensorflow.keras.applications import ResNet50
from tensorflow.keras.models import Sequential
from tensorflow.keras.layers import Dense, Rescaling, GlobalAveragePooling2D
from tensorflow.keras.callbacks import ModelCheckpoint, EarlyStopping, ReduceLROnPlateau
from tensorflow.keras.optimizers import Adam, SGD, RMSprop

def train_model(learning_rate, epochs, patience, optimizer_name, activation_function, validation_split, directory_path):
    try:
        img_size = 224
        batch_size = 32

        # Charger les jeux de données
        train = tf.keras.utils.image_dataset_from_directory(
            directory_path,
            validation_split=validation_split,
            subset="training",
            seed=123,
            image_size=(img_size, img_size),
            batch_size=batch_size,
            label_mode="categorical"
        )

        val = tf.keras.utils.image_dataset_from_directory(
            directory_path,
            validation_split=validation_split,
            subset="validation",
            seed=123,
            image_size=(img_size, img_size),
            batch_size=batch_size,
            label_mode="categorical"
        )

        # Mapper l'optimiseur
        optimizers = {
            'adam': Adam(learning_rate=learning_rate),
            'sgd': SGD(learning_rate=learning_rate),
            'rmsprop': RMSprop(learning_rate=learning_rate)
        }
        if optimizer_name not in optimizers:
            raise ValueError(f"Invalid optimizer: {optimizer_name}")
        optimizer = optimizers[optimizer_name]

        # Construire le modèle avec ResNet50
        base_model = ResNet50(weights="imagenet", include_top=False, input_shape=(img_size, img_size, 3))
        base_model.trainable = False

        model = Sequential([
            Rescaling(1./255, input_shape=(img_size, img_size, 3)),
            base_model,
            GlobalAveragePooling2D(),
            Dense(120, activation=activation_function)  
        ])

        model.compile(
            loss=tf.keras.losses.CategoricalCrossentropy(),
            optimizer=optimizer,
            metrics=["accuracy"]
        )

        # Définir les callbacks
        checkpoint = ModelCheckpoint("best_model.keras", monitor="val_loss", save_best_only=True, verbose=1)

        earlystopping = EarlyStopping(monitor="val_loss", patience=patience, restore_best_weights=True, verbose=1)
        reduce_lr = ReduceLROnPlateau(monitor="val_loss", factor=0.2, patience=3, min_lr=1e-5, verbose=1)

        # Entraîner le modèle
        history = model.fit(
            train,
            epochs=epochs,
            validation_data=val,
            callbacks=[checkpoint, earlystopping, reduce_lr]
        )

        # Retourner des informations sur la performance finale
        accuracy = history.history['accuracy'][-1]
        val_loss = history.history['val_loss'][-1]
        return f"Model training completed. Final Accuracy: {accuracy:.4f}, Final Validation Loss: {val_loss:.4f}"

    except Exception as e:
        return f"An error occurred: {str(e)}"

if __name__ == "__main__":
    result = train_model(
        learning_rate=0.001,
        epochs=10,
        patience=3,
        optimizer_name="adam",
        activation_function="softmax",
        validation_split=0.2,
        directory_path="C:/Users/lenovo/Downloads/images/Images"
    )
    print(result)
