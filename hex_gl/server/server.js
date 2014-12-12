// Dependencies
var express = require('express');
var http = require('http');
var io = require('socket.io')
var crypto = require('crypto');
 
// Set up our app with Express framework
var app = express();
 
// Create our HTTP server
var server = http.createServer(app);
 
// Configure the app's document root to be HexGl/
app.configure(function() {
   app.use(
      &quot;/&quot;,
      express.static(&quot;../&quot;)
   );
});
 
// Tell Socket.io to pay attention
io.listen(server);
 
// Tell HTTP Server to begin listening for connections on port 3250
server.listen(3250);