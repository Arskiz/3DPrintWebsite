var mysql = require('mysql');

var con = mysql.createConnection({
    host: "localhost",
    user: "root",
    password: "",
    database: "3darchive"
});

con.connect(function(err){
    console.clear()
    if(err) throw err;
    console.log("Connected - port 3000!\n")
    
    var sql = "SELECT * from users WHERE id='0'";
    con.query(sql, function (err, result) {
        if (err) throw err;
        console.log("------------------------------------------------------\nQuery: " + sql + "\n------------------------------------------------------\n")
        console.log("Result: \n" + JSON.stringify(result, null, 2));
      });
})