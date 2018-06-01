// Define variables
const express = require('express');
const app = express();
var http = require('http').Server(app);
var server = app.listen(3001);
var io = require('socket.io').listen(server);

// Use the /dist directory
app.use(express.static(__dirname + '/dist'));

// Catch all other invalid routes
app.all('*', function(req,res){
    res.status(200).sendFile(__dirname + '/dist/index.html');
});

io.on('connection', (socket) => {

  console.log("User connected");

  socket.on('message', function(msg){
    io.emit('message', msg);
  });

  socket.on('comment', function(msg){
    io.emit('comment', msg);
  });

})