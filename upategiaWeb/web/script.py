# pip install mysql-connector-python smtplib email

import mysql.connector
import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart

DB_HOST = "localhost"
DB_USER = "root"  
DB_PASSWORD = ""  
DB_NAME = "bdweb" 

SMTP_SERVER = "localhost"  
SMTP_PORT = 2525  

def check_users_without_avatar():
    try:
        connection = mysql.connector.connect(
            host=DB_HOST,
            user=DB_USER,
            password=DB_PASSWORD,
            database=DB_NAME
        )

        cursor = connection.cursor(dictionary=True)

        # Consulta SQL para obtener los usuarios sin avatar
        cursor.execute("SELECT username FROM users WHERE irudia IS NULL OR irudia = '';")
        
        # Obtenemos todos los usuarios sin avatar
        users = cursor.fetchall()

        return users

    except mysql.connector.Error as err:
        print(f"Error de conexión: {err}")
        return []

    finally:
        if connection.is_connected():
            cursor.close()
            connection.close()

# Función para enviar correo a los usuarios
def send_email(to_email):
    from_email = "herrero.julen@protonmail.com"  # Cambia esto por tu correo de envío
    subject = "Zure perfila eguneratu"
    body = "Kaixo, dirudienez ez duzu zure perfilean irudirik, eguneratu ezazu irudi bat aukeratuz :D"

    # Crear el mensaje de correo
    message = MIMEMultipart()
    message["From"] = from_email
    message["To"] = to_email
    message["Subject"] = subject
    message.attach(MIMEText(body, "plain"))

    try:
        # Conectar al servidor SMTP (smtp4dev)
        with smtplib.SMTP(SMTP_SERVER, SMTP_PORT) as server:
            server.sendmail(from_email, to_email, message.as_string())
            print(f"Korreoa {to_email} erabiltzaileari bidalita")
    
    except Exception as e:
        print(f"Errore {to_email}-i korreoa bidaltzen {e}")

# Función principal que verifica usuarios y envía correos
def main():
    users = check_users_without_avatar()

    if users:
        for user in users:
            send_email(user['username'])
    else:
        print("Ez daude erabiltzailerik irdui gabe")
    
if __name__ == "__main__":
    main()
