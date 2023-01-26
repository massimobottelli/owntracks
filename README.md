# OwnTracks Location Diary

OwnTracks is a great alternative for people who are looking for more control over their location data compare to commercial solutions like Google Maps Timeline.

Owntracks is based on open-source and open protocols, so your data stays secure and private.

OwnTracks platform consists of two components:

1. A mobile app that runs in the background on your mobile device and sends out your current coordinates to an MQTT endpoint with a message in JSON format
2. Your server where your backend runs to listen to the data broadcasted by the mobile app and processes your received data, e.g., showing your position history on a map.

Since we love coding and self-hosted solutions here, let’s have fun creating your own self-hosted location system to provide the same functions as Google Map Timeline, but in a privacy-oriented way.

First we need to set up the server to listen to the messages the application sends and to process the data, then we will configure the mobile application on your smartphone.

## Setup the backend on your server

Before we can configure the mobile app, we need to setup the self-hosted backend on your server, which is made up of four components:

* the MQTT broker
* the Database
* the Listener
* the Location Diary

### The MQTT broker

MQTT (Message Queue Telemetry Transport) is a protocol for communication between devices and a central server called “broker”.

MQTT uses a “publish-subscribe” model, in which devices publish messages to a specific topic, and other devices can Zephyrto that topic to receive the messages.

In OwnTracks platform, the MQTT broker allows the Listener to subscribe to the topic that we will later configure in the mobile app.

We choose Eclipse Mosquitto broker, an easy to install MQTT broker.

### The Database

We need to setup the database and table to store the data that the Listener will receive from the mobile app.

The proper configuration for the MySql database is in /listener folder.

### The Listener

The Listener is a Python script that runs in the background, receives the location from the mobile app and stores the data in a database.

The script connects to the MQTT broker, subscribes to the Owntracks topic, and listens for incoming messages.

When a message is received, it decodes the payload that contains latitude and longitude, converts it to a JSON object, and it inserts those values into a MySQL database table.

The script reads configurations from ‘config.ini’ file, which contains information on the broker address and credentials, topic to listen, and database host, user, password, database and table name. So make sure do insert your specific information in config.ini file.

### The Location Diary web application

Finally, the Location Diary is the web application that you will use to see you location history.

Two view options are available:

* Markers view: a marker is displayed for each location point tracked, with a line connecting them to show your route. In marker view, you can filters by date using the date picker.
* Heatmap view: colored areas represent the density of points in a given area, from warmer to cooler colors. This allows you to see where you have spent the most time.

The script uses the Leaflet and Leaflet-heat libraries for showing OpenStreetMap tiles, markers, polylines and heatmap.

## How to setup OwnTracks mobile app

Now it’s time to install and setup the OwnTracks application on your mobile phone!

Remember that all components of this project are open source, and also the app guarantees your privacy as it sends your location only to your server and not to any commercial cloud.

1. Download the OwnTracks app from the App Store (iOS) or the Google Play Store (Android)
2. In the app settings (info icon on top left corner), set the connection mode to MQTT and configure the connection settings with your self-hosted MQTT broker’s hostname, port number and authentication with your username and password (we will define these values in the setup of the backend in the next chapter)
3. Go back to the map, click on your marker and retrieve your personal topic (“owntracks/mqtt/<token>”) that you will later use in the configuration of the MQTT listener on your backend.

## Your private location tracker is now live!

And here we are! The mobile app will start sending your position to your server (especially when you move, to save your device battery) and the Listener will constantly listen to the messages sent over MQTT protocol and save your location to your personal database.

And the Location Diary will do the magic!

Relive your trips, navigate in the past with the date picker, and discover where you spent the most of your time with the heatmap!

