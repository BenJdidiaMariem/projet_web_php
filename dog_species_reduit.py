# -*- coding: utf-8 -*-
import sys
import argparse
import tensorflow as tf
import tensorflow_hub as hub
from tensorflow.keras.models import Sequential
from tensorflow.keras.callbacks import ModelCheckpoint, EarlyStopping, ReduceLROnPlateau
from tensorflow.keras.optimizers import Adam, SGD, RMSprop

# Fonction pour définir et entraîner le modèle avec les hyperparamètres donnés
def train_model(learning_rate, epochs, patience, monitor, optimizer_name, model_name, activation_function, validation_split, test_split, directory_path):
    try:
        img_size = 224
        batch_size = 64
        fpath = directory_path  

        # Charger les jeux de données
        train = tf.keras.utils.image_dataset_from_directory(
            fpath,
            validation_split=validation_split,
            subset="training",
            seed=123,
            image_size=(img_size, img_size),
            batch_size=batch_size,
            label_mode="categorical"
        )

        val = tf.keras.utils.image_dataset_from_directory(
            fpath,
            validation_split=validation_split,
            subset="validation",
            seed=123,
            image_size=(img_size, img_size),
            batch_size=batch_size,
            label_mode="categorical"
        )

        # Vérifiez l'activation et mappez l'optimiseur
        valid_activations = ['relu', 'sigmoid', 'tanh', 'softmax', 'elu', 'selu', 'swish']
        if activation_function not in valid_activations:
            raise ValueError(f"Invalid activation function: {activation_function}")

        optimizers = {
            'adam': Adam(learning_rate=learning_rate),
            'sgd': SGD(learning_rate=learning_rate),
            'rmsprop': RMSprop(learning_rate=learning_rate)
        }

        if optimizer_name not in optimizers:
            raise ValueError(f"Invalid optimizer: {optimizer_name}")
        optimizer = optimizers[optimizer_name]

        # Modèle de base avec ResNet50 via TensorFlow Hub
        model_url = "https://tfhub.dev/google/imagenet/resnet_v2_50/feature_vector/5"
        model = Sequential([
            tf.keras.layers.Rescaling(1./255, input_shape=(img_size, img_size, 3)),
            hub.KerasLayer(model_url, trainable=False),
            tf.keras.layers.Dense(120, activation=activation_function)  # Ajustez le nombre de classes
        ])

        model.compile(
            loss=tf.keras.losses.CategoricalCrossentropy(),
            optimizer=optimizer,
            metrics=["accuracy"]
        )

        # Callbacks pour sauvegarder le meilleur modèle et éviter le surapprentissage
        model_name = f"{model_name}.h5"
        checkpoint = ModelCheckpoint(model_name, monitor="val_loss", mode="min", save_best_only=True, verbose=1)
        earlystopping = EarlyStopping(monitor='val_loss', min_delta=0, patience=patience, verbose=1, restore_best_weights=True)
        reduce_lr = ReduceLROnPlateau(monitor='val_loss', factor=0.2, patience=3, min_lr=0.0001)

        # Entraînement du modèle
        history = model.fit(train, epochs=epochs, validation_data=val, callbacks=[checkpoint, earlystopping, reduce_lr])

        # Retourner des informations sur la performance finale
        accuracy = history.history['accuracy'][-1]
        val_loss = history.history['val_loss'][-1]
        return f"Model training completed. Final Accuracy: {accuracy}, Final Validation Loss: {val_loss}"

    except Exception as e:
        return f"An error occurred: {str(e)}"

# Fonction principale pour gérer les arguments
def main():
    parser = argparse.ArgumentParser(description="Train a deep learning model with given hyperparameters")
    parser.add_argument('--learning_rate', type=float, required=True, help="Learning rate")
    parser.add_argument('--epochs', type=int, required=True, help="Number of epochs")
    parser.add_argument('--patience', type=int, required=True, help="Patience for early stopping")
    parser.add_argument('--monitor', type=str, required=True, help="Monitor for early stopping")
    parser.add_argument('--optimizer', type=str, required=True, help="Optimizer to use (adam, sgd, rmsprop)")
    parser.add_argument('--model_name', type=str, required=True, help="Name of the model to save")
    parser.add_argument('--activation_function', type=str, required=True, help="Activation function for the last layer")
    parser.add_argument('--validation_split', type=float, required=True, help="Validation split")
    parser.add_argument('--test_split', type=float, required=True, help="Test split (not used currently)")
    parser.add_argument('--directory_path', type=str, required=True, help="Path to the dataset")

    args = parser.parse_args()

    # Appeler la fonction d'entraînement du modèle avec les arguments
    result = train_model(args.learning_rate, args.epochs, args.patience, args.monitor, args.optimizer,
                         args.model_name, args.activation_function, args.validation_split, args.test_split, args.directory_path)

    print(result)

if __name__ == "__main__":
    main()
