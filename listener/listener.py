import paho.mqtt.client as mqtt
import json
import pymysql
from datetime import datetime

import configparser
config = configparser.ConfigParser()
config.read('config.ini')

broker_address = config.get('mqtt', 'broker_address')
mqtt_username = config.get('mqtt', 'username')
mqtt_password = config.get('mqtt', 'password')
mqtt_topic = config.get('mqtt', 'topic')

host = config.get('database', 'host')
user = config.get('database', 'user')
password = config.get('database', 'password')
database = config.get('database', 'database')
table = config.get('database', 'table')

def on_message(client, userdata, message):
    message = message.payload.decode("utf-8")
    value = json.loads(message)
    ts = datetime.now()
    if "lat" in value and "lon" in value:
        try:
            lat = value["lat"]
            lon = value["lon"]
            db = pymysql.connect(host=host, user=user, password=password, database=database)
            cursor = db.cursor()
            sql = "INSERT INTO " + str(table) + " (lat, lon) VALUES (" + str(lat) + "," + str(lon) + ")"
            try:
               cursor.execute(sql)
               db.commit()
            except:
               db.rollback()
            db.close()
        except Exception:
            pass

client = mqtt.Client()
client.on_message = on_message
client.username_pw_set(mqtt_username, mqtt_password)
client.connect(broker_address)
client.loop_start()
client.subscribe(mqtt_topic)
client.loop_forever()