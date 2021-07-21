#include <WiFi.h>         
#include <HTTPClient.h>
#include <Arduino_JSON.h>
#include <Wire.h>
#include <Adafruit_Sensor.h>
#include <Adafruit_BME280.h>              //include the BME280 library

#define SEALEVELPRESSURE_HPA (1013.25)
Adafruit_BME280 bme;  // I2C
String outputsState;

//Home SSID
const char* ssid = "EE-Hub-cZ28";         //WIFI SSID
const char* password = "6-Lynmouth-Road"; //WIFI Password

//IP addresses with URL path
const char* outputsServerName = "https://panky68.ddns.net/garageRoomOutputs.php?action=outputs_state&board=2"; //Board 2 GET Request URL for reading GPIO pins and its states (JSON Format)
const char* inputsServerName = "https://panky68.ddns.net/garageRoomInputs.php"; //url for POST data for temp, humidity and altitude values

// Keep this API Key value to be compatible with the PHP code provided in the project page.
String apiKeyValue = "tPmAT5Ab3j7FA";   //API Key Value
String sensorName = "BME280";           //Sensor Name Value
String sensorLocation = "Garage";   //Sensor Location Name

//Timer intervals in mS
const long readInputsInterval = 1000;         //1sec interval delay for reading GPIO's
unsigned long readInputsPreviousMillis = 0;   //initilise a count for time
const long readOutputsInterval = 600000;         //10mins interval delay value for reading the sensor
unsigned long readOutputsPreviousMillis = 0;   //initilise a count for time

void setup() {
  Serial.begin(115200); //start serial port at 115200 baud rate
  
  WiFi.begin(ssid, password);     //initialise wifi with username and password
  Serial.println("Connecting");   //print line with EOL
  while(WiFi.status() != WL_CONNECTED) {  //try to establish connection
    delay(500);                           //wait .5 seconds           
    Serial.print(".");                    //print dot
  }
  Serial.println("");                     //print line with EOL
  Serial.print("Connected to WiFi network with IP Address: ");  //print connection message
  Serial.println(WiFi.localIP());                               //print line with IP address
  bool status = bme.begin(0x76);                                //initalise BME280 sensor and check status, pass sensor type value
  if (!status) {                //check if successful initialise 
    Serial.println(status);     //print failed initialisation 
    Serial.println("Could not find a valid BME280 sensor, check wiring or change I2C address!");  //error message
    while (1);    //stop execution
  }
}

void loop() {
  unsigned long currentMillis = millis();           //get time of board initialisaion (in mS)
  if(WiFi.status()== WL_CONNECTED ){                //check WiFi connection
    
    //read/write GPIO's every 1 secs
    if(currentMillis - readInputsPreviousMillis >= readInputsInterval) {      //1S timer, 0 (current milliseconds) - 0 >= 1000 on first cycle
      outputsState = httpGETRequest(outputsServerName); //HTTP request to webpage, get gpio pins and their states
      Serial.println(outputsState);                     //print gpio and states to serial 
      JSONVar myObject = JSON.parse(outputsState);      //parse JSON format and save object to array
  
      // JSON.typeof(jsonVar) can be used to get the type of the var
      if (JSON.typeof(myObject) == "undefined") { //check valid data
        Serial.println("Parsing input failed!");  //Error message
        return;                                   //exit
      }
      
      Serial.print("JSON object = ");   //print message to serial port
      Serial.println(myObject);         //print array format, GPIO and their states
    
      // myObject.keys() can be used to get an array of all the keys in the object
      JSONVar keys = myObject.keys(); 
    
      for (int i = 0; i < keys.length(); i++) { //loop through array and print out each GPIO and its state
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
    if(currentMillis - readOutputsPreviousMillis >= readOutputsInterval){   //count check for 10 mins loop
      httpPOSTRequest(inputsServerName);          //get the temp, humidity and pressure readings and send to webpage
      readOutputsPreviousMillis = currentMillis;  //update time
    }
    
  }else{
      Serial.println("WiFi Disconnected");      //message for no wifi connection
  }
}
//function to get POST data from URL for Sensor readings
String httpPOSTRequest(const char* inputsServerName) {
  HTTPClient http;                                                        //Declare an object of class HTTPClient
  http.begin(inputsServerName);                                           //Specify request destination 
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");    // Specify content-type header

  // Prepare your HTTP POST request data, get values from sensor, api key and insert into vars for URL
  String httpRequestData = "api_key=" + apiKeyValue + "&sensor=" + sensorName + "&location=" + sensorLocation + "&value1=" + String(bme.readTemperature()) + "&value2=" + String(bme.readHumidity()) + "&value3=" + String(bme.readPressure()/100.0F) + "";
  Serial.print("httpRequestData: ");
  Serial.println(httpRequestData);

  int httpResponseCode = http.POST(httpRequestData);    //Send HTTP POST data
  
  if (httpResponseCode>0) {               //Check HTTP POST has been sent
    Serial.print("HTTP Response code: "); //if HTTP request success
    Serial.println(httpResponseCode);     //print HTTP request code
  }else {
    Serial.print("Error code: ");         //print string if HTTP request Failed
    Serial.println(httpResponseCode);     //print HTTP request error code
  }
  http.end();   //Close connection
}

//Function for GET Request, to change individual GPIO state
String httpGETRequest(const char* outputsServerName) {
  HTTPClient http;                  //Declare an object of class HTTPClient
  http.begin(outputsServerName);    //Specify request destination 
  int httpResponseCode = http.GET();// Send HTTP POST request
  String payload = "{}";            //initialise 
  
  if (httpResponseCode>0) {               //Check HTTP Response
    Serial.print("HTTP Response code: "); //if HTTP request success
    Serial.println(httpResponseCode);
    payload = http.getString();           //Get the request response payload
  }else {
    Serial.print("Error code: ");       //print string if HTTP request Failed
    Serial.println(httpResponseCode);   //print HTTP request error code
  }
  http.end();         //Close connection
  return payload;     //return 
}
