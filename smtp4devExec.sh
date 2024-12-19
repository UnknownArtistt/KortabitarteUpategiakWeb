#!/bin/bash

# Comprobar si ya existe un contenedor llamado "smtp4dev" y eliminarlo
if docker ps -q -f name=smtp4dev; then
    echo "Smtp4dev kontainerra ezabatzen..."
    sudo docker rm -f smtp4dev  # Eliminar el contenedor
fi

# Levantar el contenedor smtp4dev (nueva instancia)
echo "Smtp4dev altzatzen..."
sudo docker run -d -p 25:25 -p 8080:80 --name smtp4dev rnwood/smtp4dev

# Espera para asegurarse de que el contenedor esté listo
echo "10 segundu itxaroten smtp4dev abiarazteko ..."
sleep 10

# Ejecutar el script Python para enviar los correos
echo "Python script-a exekutatzen..."
sudo /usr/bin/python3 /home/uni/Mahaigaina/script.py
