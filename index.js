// Define variables
const express = require('express');
const app = express();
var http = require('http').Server(app);
var io = require('socket.io')(http);

// Use the /dist directory
app.use(express.static(__dirname + '/dist'));

// Catch all other invalid routes
app.all('*', function(req,res){
    res.status(200).sendFile(__dirname + '/dist/index.html');
});

io.on('connection', (socket) => {
  console.log("Connected to Socket!!");
})

// Start the server
app.listen(3000, function(){
  console.log('listening on *:3000');
});