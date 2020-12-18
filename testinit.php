<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Document</title>
</head>
<body>
      
   <button type="submit" onclick="loginWithFormData()">Login with Form Data</button>

   <script>
      function loginWithFormData() {
         var myHeaders = new Headers();
         var formdata = new FormData();
         formdata.append("email", "john@mail.com");
         formdata.append("password", "John1234");

         var requestOptions = {
         method: 'POST',
         headers: myHeaders,
         body: formdata,
         redirect: 'follow'
         };

         console.log("Form Data Login");
         fetch("https://globalsportsfans.initframework.com/api/login", requestOptions)
         .then(response => response.text())
         .then(result => console.log(result))
         .catch(error => console.log('error', error));
      }
   </script>

   <br><br><br>

   <button type="submit" onclick="loginWithRawJSON()">Login with RAW JSON data</button>
   <script>
      function loginWithRawJSON() {
         var myHeaders = new Headers();
         myHeaders.append("Content-Type", "application/json");
         var raw = JSON.stringify({"email":"john@mail.com","password":"John1234"});

         var requestOptions = {
         method: 'POST',
         headers: myHeaders,
         body: raw,
         redirect: 'follow'
         };

         console.log("Raw JSON Login");
         fetch("https://globalsportsfans.initframework.com/api/login", requestOptions)
         .then(response => response.text())
         .then(result => console.log(result))
         .catch(error => console.log('error', error));
      }
   </script>

   <br><br><br>

   <button type="submit" onclick="loginWithXWWWFormUrlEncoded()">Login with X-WWW-Form-UrlEncoded</button>
   <script>
      function loginWithXWWWFormUrlEncoded() {
         var myHeaders = new Headers();
         myHeaders.append("Content-Type", "application/x-www-form-urlencoded");

         var urlencoded = new URLSearchParams();
         urlencoded.append("email", "john@mail.com");
         urlencoded.append("password", "John1234");

         var requestOptions = {
         method: 'POST',
         headers: myHeaders,
         body: urlencoded,
         redirect: 'follow'
         };

         console.log("URL Encoded Login");
         fetch("https://globalsportsfans.initframework.com/api/login", requestOptions)
         .then(response => response.text())
         .then(result => console.log(result))
         .catch(error => console.log('error', error));
      }
   </script>


</body>
</html>