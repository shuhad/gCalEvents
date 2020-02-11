// const getData = async () => {
//     const response = await fetch('/api/cal');
//     const allData = await response.json();
//     return allData;
// }

// getData().then(val => {
//    eventData=val;
//    document.getElementById("cal-set").click();
// });
const getData = async () => {
    let allData = await makeRequest("GET", '/api/cal',true);
    return allData;
}
getData().then(val => {
    eventData=val;
    document.getElementById("cal-set").click();
});

function makeRequest(method, url) {
    return new Promise(function (resolve, reject) {
        let xhr = new XMLHttpRequest();
        xhr.open(method, url);
        xhr.onload = function () {
            if (this.status >= 200 && this.status < 300) {
                resolve(JSON.parse(xhr.response));
            } else {
                reject({
                    status: this.status,
                    statusText: xhr.statusText
                });
            }
        };
        xhr.onerror = function () {
            reject({
                status: this.status,
                statusText: xhr.statusText
            });
        };
        xhr.send();
    });
}

var cal = {
  /* [PROPERTIES] */
  mName : ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"], // Month Names
  data : null, // Events for the selected period
  sDay : 0, // Current selected day
  sMth : 0, // Current selected month
  sYear : 0, // Current selected year
  sMon : false, // Week start on Monday?

  /* [FUNCTIONS] */
  list : function () {
  // cal.list() : draw the calendar for the given month

    // BASIC CALCULATIONS
    // Note - Jan is 0 & Dec is 11 in JS.
    // Note - Sun is 0 & Sat is 6
    cal.sMth = parseInt(document.getElementById("cal-mth").value); // selected month
    cal.sYear = parseInt(document.getElementById("cal-yr").value); // selected year
    var daysInMth = new Date(cal.sYear, cal.sMth+1, 0).getDate(), // number of days in selected month
        startDay = new Date(cal.sYear, cal.sMth, 1).getDay(), // first day of the month
        endDay = new Date(cal.sYear, cal.sMth, daysInMth).getDay(); // last day of the month

    //cal.data = await (await fetch("http://127.0.0.1:8000/api/cal")).json();
    
    cal.data=eventData;
    //console.log(cal.data);
    // DRAWING CALCULATIONS
    // Determine the number of blank squares before start of month
    var squares = [];
    if (cal.sMon && startDay != 1) {
      var blanks = startDay==0 ? 7 : startDay ;
      for (var i=1; i<blanks; i++) { squares.push("b"); }
    }
    if (!cal.sMon && startDay != 0) {
      for (var i=0; i<startDay; i++) { squares.push("b"); }
    }

    // Populate the days of the month
    for (var i=1; i<=daysInMth; i++) { squares.push(i); }

    // Determine the number of blank squares after end of month
    if (cal.sMon && endDay != 0) {
      var blanks = endDay==6 ? 1 : 7-endDay;
      for (var i=0; i<blanks; i++) { squares.push("b"); }
    }
    if (!cal.sMon && endDay != 6) {
      var blanks = endDay==0 ? 6 : 6-endDay;
      for (var i=0; i<blanks; i++) { squares.push("b"); }
    }

    // DRAW HTML
    // Container & Table
    var container = document.getElementById("cal-container"),
    cTable = document.createElement("table");
    cTable.id = "calendar";
    container.innerHTML = "";
    container.appendChild(cTable);

    // First row - Days
    var cRow = document.createElement("tr"),
        cCell = null,
        days = ["Sun", "Mon", "Tue", "Wed", "Thur", "Fri", "Sat"];
    if (cal.sMon) { days.push(days.shift()); }
    for (var d of days) {
      cCell = document.createElement("td");
      cCell.innerHTML = d;
      cRow.appendChild(cCell);
    }
    cRow.classList.add("head");
    cTable.appendChild(cRow);

    // Days in Month
    var total = squares.length;
    cRow = document.createElement("tr");
    cRow.classList.add("day");
    dateArray=[];
    if(cal.data && cal.data.length>0){
      cal.data.forEach(function(element) {
        if (element.start.dateTime) {
          dateArray.push(element.start.dateTime.split("T")[0]);
        }
    })
    }

      //console.log(dateArray[0]);  // output as wished: apple, banana, grape
    for (var i=0; i<total; i++) {
      cCell = document.createElement("td");
      if (squares[i]=="b") { cCell.classList.add("blank"); }
      else {
        cCell.innerHTML = "<div class='dd'>"+squares[i]+"</div>";
        //console.log(cal.data[0].start.dateTime);
        specificDay=cal.sYear+'-'+String("0" + parseInt(cal.sMth+1)).slice(-2)+'-'+String("0" + squares[i]).slice(-2);
        if(dateArray.includes(specificDay))
          {  
            pos = cal.data.map(function(e) { 
              if (e.start.dateTime) {
                return e.start.dateTime.split("T")[0] 
              }
            }).indexOf(specificDay);
            cCell.innerHTML += "<div class='evt'>" + cal.data[pos].summary + "</div>";
            cCell.innerHTML += "<div class='evtID' style='display:none'>" + cal.data[pos].id + "</div>";
          }
        cCell.addEventListener("click", function(){
          cal.show(this);
        });
      }
      cRow.appendChild(cCell);
      if (i!=0 && (i+1)%7==0) {
        cTable.appendChild(cRow);
        cRow = document.createElement("tr");
        cRow.classList.add("day");
      }
    }

    // REMOVE ANY ADD/EDIT EVENT DOCKET
    cal.close();
  },

  show : function (el) {
  // cal.show() : show edit event docket for selected day
  // PARAM el : Reference back to cell clicked

    // FETCH EXISTING DATA
    cal.sDay = el.getElementsByClassName("dd")[0].innerHTML;
    let childd = el.querySelector('.evt');
    evtDesc=null;
    evtID=null;
    if(childd){
          evtDesc = childd.innerHTML;
          var evtID = el.querySelector('.evtID');
          evtID = evtID.innerHTML;
    }
    specificDay=cal.sYear+'-'+String("0" + parseInt(cal.sMth+1)).slice(-2)+'-'+String("0" + cal.sDay).slice(-2);
    let today = new Date();
let cureTime = ("0" + today.getHours()).slice(-2) + ":" + ("0" + today.getMinutes()).slice(-2) + ":" + ("0" + today.getSeconds()).slice(-2);
specificDay+="T"+cureTime+"+01:00";
    // DRAW FORM
    var tForm = "<h1>" + (evtDesc ? "EDIT" : "ADD") + " EVENT</h1>";
    tForm += "<div id='evt-date'>" + cal.sDay + " " + cal.mName[cal.sMth] + " " + cal.sYear + "</div>";
    tForm += "<textarea id='evt-details' name='description' required>" + (evtDesc ? evtDesc : "") + "</textarea>";
    tForm += "<input type='button' value='Close' onclick='cal.close()'/>";
    tForm += "<input type='button' value='Delete' id='test' onclick='cal.del()'/>";
    tForm += "<input type='hidden' name='start' value='"+specificDay+"' />";
    tForm += "<input type='submit' value='Save'/>";

    // ATTACH
    var eForm = document.createElement("form");
    eForm.name='myForm';
    eForm.method='POST';
    if(evtDesc){
      tForm += "<input type='hidden' name='evtID' id='evtID' value='"+evtID+"' />";
      tForm += "<input type='hidden' name='_method' value='PUT' />";
      tForm += "<input type='hidden' name='oldEvtDes' value='"+evtDesc+"' />";
      eForm.action='/api/cal/'+evtID;
    }
    else{
      eForm.action='/api/cal';
    }
    
    //eForm.addEventListener("submit", cal.save);
    eForm.innerHTML = tForm;
    var container = document.getElementById("cal-event");
    container.innerHTML = "";
    container.appendChild(eForm);
  },

  close : function () {
  // cal.close() : close event docket

    document.getElementById("cal-event").innerHTML = "";
  },

  del : function () {
    const f = document.myForm;
    if(f.elements.namedItem("evtID")!=null){
    evtID=document.getElementById('evtID').value
    makeRequest("DELETE",'/api/cal/'+evtID,true);
      function myUrl(){
          return window.location.replace(window.location.href);
      }
    setTimeout(myUrl, 1000)
  }else{
    alert("The event does not exist and therefore cannot be deleted");
  }
    
   
  }
};

// INIT - DRAW MONTH & YEAR SELECTOR
window.addEventListener("load", function () {
  // DATE NOW
  var now = new Date(),
      nowMth = now.getMonth(),
      nowYear = parseInt(now.getFullYear());

  // APPEND MONTHS SELECTOR
  var month = document.getElementById("cal-mth");
  for (var i = 0; i < 12; i++) {
    var opt = document.createElement("option");
    opt.value = i;
    opt.innerHTML = cal.mName[i];
    if (i==nowMth) { opt.selected = true; }
    month.appendChild(opt);
  }

  // APPEND YEARS SELECTOR
  // Set to 10 years range. Change this as you like.
  var year = document.getElementById("cal-yr");
  for (var i = nowYear-20; i<=nowYear+5; i++) {
    var opt = document.createElement("option");
    opt.value = i;
    opt.innerHTML = i;
    if (i==nowYear) { opt.selected = true; }
    year.appendChild(opt);
  }

  // START - DRAW CALENDAR



  document.getElementById("cal-set").addEventListener("click", cal.list);
  cal.list();
});





