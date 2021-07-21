#include <WiFi.h>
#include <HTTPClient.h>
#include <Arduino_JSON.h>
#include <Wire.h>
#include <Adafruit_Sensor.h>
#include <Adafruit_BME280.h>

//Home SSID
const char* ssid = "EE-Hub-cZ28";
const char* password = "6-Lynmouth-Road";

//Your IP addresses or domain name with URL path
const char* outputsServerName = "https://panky68.ddns.net/officeRoomOutputs.php?action=outputs_state&board=1"; //Board 1, page to control I/O's, ie switches 
const char* inputsServerName = "https://panky68.ddns.net/officeRoomInputs.php";   //script that gets temp, humidity and pressure readings from esp32 (via POST), and saves them to dB table officeSensorData

// Keep this API Key value to be compatible with the PHP code provided in the project page.
// If you change the apiKeyValue value, the PHP file /officeRoomInputs.php also needs to have the same key
String apiKeyValue = "tPmAT5Ab3j7F9";   //API Key Value
String sensorName = "BME280";           //type of Sensor
String sensorLocation = "officeRoom";   //Board Location

#define SEALEVELPRESSURE_HPA (1013.25)

Adafruit_BME280 bme;                //BME-280 I2C Sensor instance

//Timer intervals
const long readInputsInterval = 1000;         //set a 1sec interval delay for reading the switches
unsigned long readInputsPreviousMillis = 0;   //initilise a count for time
const long readOutputsInterval = 600000;         //set a 10mins interval delay for reading the temps, humdity and presure inputs
unsigned long readOutputsPreviousMillis = 0;   //initilise a count for time

String outputsState;

void setup() {
  Serial.begin(115200);                         //serial port baud rate
  
  WiFi.begin(ssid, password);                   //start wifi
  Serial.println("Connecting");                 //serial print message
  while(WiFi.status() != WL_CONNECTED) {        //has esp32 connected to wifi
    delay(500);                                 //wait .5s
    Serial.print(".");                          //serial print .
  }
  Serial.println("");                           //serial print carriage return
  Serial.print("Connected to WiFi network with IP Address: ");  //serial print
  Serial.println(WiFi.localIP());               //serial print IP address
  // (you can also pass in a Wire library object like &Wire2)
  bool status = bme.begin(0x76); //connect to sensor with address 0x76
  if (!status) {        //sensor connection status, connected?
    Serial.println(status);
    Serial.println("Could not find a valid BME280 sensor, check wiring or change I2C address!");
    while (1);
  }
}

void loop() {
  unsigned long currentMillis = millis();               //get milliSeconds since board began
  if(WiFi.status()== WL_CONNECTED ){                  //Wait until WiFi connected
    
    //read in Switch inputs every 1 secs
    if(currentMillis - readInputsPreviousMillis >= readInputsInterval) {      //check 1 sec passed, 0 (current milliseconds) - 0 >= 600000 on first cycle
      outputsState = httpGETRequest(outputsServerName); //HTTP request from webpage, get gpio pins and it states
      Serial.println(outputsState);                     //Serial print gpio and it states
      JSONVar myObject = JSON.parse(outputsState);      //parse into JSON format and save object to array
  
      // JSON.typeof(jsonVar) can be used to get the type of the var
      if (JSON.typeof(myObject) == "undefined") {
        Serial.println("Parsing input failed!");
        return;
      }
      
      Serial.print("JSON object = ");
      Serial.println(myObject);
    
      // myObject.keys() can be used to get an array of all the keys in the object
      JSONVar keys = myObject.keys();
    
      for (int i = 0; i < keys.length(); i++) {
        JSONVar value = myObject[keys[i]];
        Serial.print("GPIO: ");
        Serial.print(keys[i]);
        Serial.print(" - SET to: ");
        Serial.println(value);
        pinMode(atoi(keys[i]), OUTPUT);
        digitalWrite(atoi(keys[i]), atoi(value));
      }
      readInputsPreviousMillis = currentMillis; //update time
    }

    //read in Temp, humidity and Pressure inputs every 10 mins
    if(currentMillis - readOutputsPreviousMillis >= readOutputsInterval){
      httpPOSTRequest(inputsServerName);  //get the temp, humidity and pressure readings and send to webpage
      readOutputsPreviousMillis = currentMillis; //update time
    }
  }else{
      Serial.println("WiFi Disconnected");
  }
}
String httpGETRequest(const char* outputsServerName) {
  HTTPClient http;    //Declare an object of class HTTPClient 
  http.begin(outputsServerName);   //Specify request destination 
  int httpResponseCode = http.GET();      // Send HTTP POST request
  String payload = "{}"; 
  
  if (httpResponseCode>0) {   //Check the returning code
    Serial.print("HTTP Response code: ");
    Serial.println(httpResponseCode);
    payload = http.getString();   //Get the request response payload
  }else {
    Serial.print("Error code: ");
    Serial.println(httpResponseCode);
  }
  http.end();   //Close connection
  return payload;
}
String httpPOSTRequest(const char* inputsServerName) {
  HTTPClient http;    //Declare an object of class HTTPClient
  http.begin(inputsServerName);   //Specify request destination, NOTE: originall set to outputsServerName, which was wrong i think, need to test
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");    // Specify content-type header

  // Prepare your HTTP POST request data
  String httpRequestData = "api_key=" + apiKeyValue + "&sensor=" + sensorName + "&location=" + sensorLocation + "&value1=" + String(bme.readTemperature()) + "&value2=" + String(bme.readHumidity()) + "&value3=" + String(bme.readPressure()/100.0F) + "";
  Serial.print("httpRequestData: ");
  Serial.println(httpRequestData);

  // dummy httpRequestData variable below (for testing purposes without the BME280 sensor)
  //  String httpRequestData = "api_key=tPmAT5Ab3j7F9&sensor=BME280&location=Office&value1=28.75&value2=78.54&value3=1005.14";
  //  Serial.print("httpRequestData: ");
  //  Serial.println(httpRequestData);
  int httpResponseCode = http.POST(httpRequestData);    // Send HTTP POST request
  
  if (httpResponseCode>0) {   //Check the returning code
    Serial.print("HTTP Response code: ");
    Serial.println(httpResponseCode);
  }else {
    Serial.print("Error code: ");
    Serial.println(httpResponseCode);
  }
  http.end();   //Close connection
}
