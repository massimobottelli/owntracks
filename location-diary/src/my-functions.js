$(function () {
   $("#datepicker").datepicker({
      dateFormat: "yy-mm-dd",
      changeMonth: true,
      changeYear: true,
      yearRange: "-3:+0"
   });
});

function track(i) {
   var pointA = [lat[i - 1], lon[i - 1]];
   var pointB = [lat[i], lon[i]];
   var pointC = [lat[i + 1], lon[i + 1]];
   var pointList = [pointA, pointB, pointC];
   polyline = L.polyline(pointList).addTo(mymap);
}

function submitForm() {
   document.forms["datepicker"].submit();
}

function previousDay() {
   var date = new Date(document.getElementById("datepicker").value);
   date.setDate(date.getDate() - 1);
   document.getElementById("datepicker").value = date.toISOString().substring(0, 10);
   submitForm();
}

function nextDay() {
   var date = new Date(document.getElementById("datepicker").value);
   date.setDate(date.getDate() + 1);
   document.getElementById("datepicker").value = date.toISOString().substring(0, 10);
   submitForm();
}

function reloadPage(value) {
   var form = document.createElement("form");
   form.setAttribute("method", "post");
   form.setAttribute("action", "");
   var hiddenField = document.createElement("input");
   hiddenField.setAttribute("type", "hidden");
   hiddenField.setAttribute("name", "view");
   hiddenField.setAttribute("value", value);
   form.appendChild(hiddenField);
   document.body.appendChild(form);
   form.submit();
}


