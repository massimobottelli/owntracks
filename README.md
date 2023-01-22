# owntracks

Owntracks PHP script allows the user to view location data on a map, with the option to select a specific date and view the data as markers or a heatmap. The data is retrieved from a MySQL database and displayed on a Leaflet map. 

## MQTT 

A dedicated Python script is using the paho MQTT library to subscribe to an Owntracks topic and receive location updates. 
When it receives a message, it stores the "lat" and "lon" value in a MySQL database. 
If the message contains the "inregions" key, it prints the value and publishes it to another topic e.g., for Home Assistant automation.
