localStorage.clear();

function timeChecker() {
    setInterval(() => {
        var storedTimeStamp = sessionStorage.getItem("lastTimeStamp");
        timeCompare(storedTimeStamp);
    }, 2000);
}

function timeCompare(timeString) {
    var currentTime = new Date();
    var pastTime = new Date(timeString);
    var timeDiff = currentTime - pastTime;
    var minPast = Math.floor((timeDiff/60000));
    // console.log(currentTime+ " - "+pastTime+" - "+minPast+"min past" );
    if(minPast > 30) {
        sessionStorage.removeItem("lastTimeStamp");
        swal("Session Timeout", {
            icon: "error",
            closeOnClickOutside: false
        }).then((response) => {
            window.location = "../../../../../configs/logout.php";
        });
        return false;
    }
}

$(document).mousemove(()=> {
    var timeStamp = new Date();
    sessionStorage.setItem("lastTimeStamp", timeStamp);
    
});

timeChecker();