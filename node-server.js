const express = require('express');
const app = express();
const connect = require('./node-connect');

app.get('/', (req, res) => {
    connect();
    res.render('index');
})

app.listen(3000);
console.log("Listening on port 3000.")