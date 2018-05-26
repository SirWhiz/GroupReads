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

io.on('connection', function(data){
	console.log('a user connected');
});

// Start the server
app.listen(process.env.PORT || 3000);