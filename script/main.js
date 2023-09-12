"use strict";
let emailvalid = false;
let usernamevalid = false;
 

//**************************************************** */

//Documnt load event
document.addEventListener("DOMContentLoaded", () => {

  // Add an event listener to the button
  document.getElementById("copyButton").addEventListener("click", function() {
    // Select the text or perform your copy operation here
    // For example:
    // var textToCopy = "Your text to copy";
    // navigator.clipboard.writeText(textToCopy);
  
    // After copying, display the tooltip for a brief period
    var tooltip = document.getElementById("tooltipText");
    tooltip.textContent = "Link Copied!";
    setTimeout(function() {
        tooltip.textContent = ""; // Clear the tooltip text
    }, 2000); // Display the tooltip for 2 seconds (adjust as needed)
  });

  /////This whole part is for sharing the list item 
  const copybutton = document.querySelector(".copybutton");
  //this will tigger when share button is clicked this will copy the text in the input to the clipboard
  copybutton.addEventListener("click",(ev)=>{
      var copyText = document.getElementById("myInput");
      copyText.select();
      copyText.setSelectionRange(0, 99999);
      navigator.clipboard.writeText(copyText.value);
})
  
//**API**
let printDataContent = document.querySelectorAll("#PrintActivity");
const getActivity = document.querySelectorAll('button[name="getAPI"]');

// E1. Start of Get Countries - to get the all countries from URL
  for(let i = 0 ; i < getActivity.length; i++){
       getActivity[i].addEventListener("click",(e) => {
              
      const xhr1 = new XMLHttpRequest();
      xhr1.open("GET", "https://www.boredapi.com/api/activity"); //Loading the API for sending XMLHttps request
              
              xhr1.addEventListener("load", () => {          
                  if (xhr1.status != 200) { //if the request dosent loads successfull
                      //handling errors
                      printDataContent[0].innerHTML = "<h2>Something went wrong.</h2>"; //printing the error
                  }
                  //else when code is success
                  else{
                      const stats = JSON.parse(xhr1.responseText); //decoding the value from encoded json value - getting the response text from the API
                      //if data found for the selected input
                      if(stats !=null)
                      {
                          //creating a table field for displaying the country data
                          let activityPara ="<p> Activity : <span class='spanActivity'>" + stats.activity + "</span></p>";
                          //output in table element
                          printDataContent[0].innerHTML = activityPara;

                      }else{ //when data is not recieved for the selected input 
                          let StatError = "<span>No Data Found</span>";
                          printDataContent[0].innerHTML = StatError; //printing error
                      }
                  }
              });
          xhr1.send(); //send another request via open connection
  
            });
  }

  //Modal window controls
  const open =  document.getElementsByClassName("open");//all the labels of items
  const close = document.getElementById('close'); //the close button on the modal class
  const modal_container = document.getElementById('modal_container');//modal division
  const inputvalue = document.getElementsByName('gift');//input checkbox for items
  for (let x=0 ; x<open.length;x++){

            
              open[x].addEventListener('click',()=>{//lists all the labels 
                  modal_container.classList.add('show');// adds visibility to the modal class

                  const ajax = new XMLHttpRequest();
                  const method = "GET";// method to be used
                  
                  const variableItemIdValue=inputvalue[x].value;// this is taking the value from the input for specfic label we clicked
                  console.log(variableItemIdValue);//testing
                  var url ="listcontent.php?listID="+variableItemIdValue;// making the url specific to the itemID

                  ajax.open(method,url);
                  ajax.send();

                  ajax.addEventListener("load", () => {
                      if(ajax.readyState==4 && ajax.status==200){
                         document.querySelector(".divcontent").innerHTML=ajax.responseText;//populating the modal content into the and then in the HTML
                      }
                  })
              })
              close.addEventListener('click',()=>{
                  modal_container.classList.remove('show');
              })
          }  


          const enterUsername = document.getElementById("username");

          enterUsername.addEventListener("blur", () => {
              const prevUserError = document.getElementById("prevUserError");
              
              if (prevUserError != null){
                prevUserError.remove();
              }
          
              const xhruser = new XMLHttpRequest();
          
              xhruser.open("GET", "checkUser.php?username=" + enterUsername.value);
          
              xhruser.addEventListener("load", () => {
                if (xhruser.status != 200) {
                  console.log("Error:", xhruser.status, xhruser.statusText);
                }
                else {
                  const response = xhruser.responseText.trim();
          
                  if (response === 'true' || response === 'error') {
                    let errorUsername = "<span class='error' id='prevUserError'>Username Already Registered!</span>";
                    enterUsername.parentElement.insertAdjacentHTML("afterend", errorUsername);
                  } else {
                    // Handle the case when the username is valid (if needed)
                  }
                }
              });
          
              xhruser.send();
          });
          
  
  //Validation For email on Sign up page
  const enterEmail = document.getElementById("email");

  //1. New Event Listener for input for email
  //We have used the blur event because if the user leave the box after enering email, it will check for that email exists in database or not.
  enterEmail.addEventListener("blur", () => {

    const prevError = document.getElementById("prevError");
    //if error exist (i.e prevError is not null or empty) which is about email already has been used for voting.
    if (prevError != null){
      prevError.remove(); //remove the span element error which I have created down below
    }

    //2. making an ajax request by using an XMLHttpRequest Object
    const xhr = new XMLHttpRequest();

    //open a URL connection by using the GET method and supplying the email as a query - string
    xhr.open("GET", "checkemail.php?email=" + enterEmail.value);

    //to ensure request has been successful
    xhr.addEventListener("load", () => {
      //if XMLHttpRequest fails
      if (xhr.status != 200) {
        console.log(xhr);
        console.log("Error");  //log the error   
      }
      //else when code is success
      else{
        //3. if we get response and check an error about email exists
        if(xhr.response == 'true' || xhr.response == 'error'){
          //handle error! creating email error using span- added class error and ID
          let errorEmail = "<span class ='error' id = 'prevError' > Email Already Registered!.</span>"
          //appending the span to div
          enterEmail.parentElement.insertAdjacentHTML("afterend", errorEmail);
          emailvalid = false;
        }
        //i.e entered email is unique
        else
        {
          emailvalid = true;
        }
      }
    });
    //sending request via open connection
    xhr.send();
    
  });

  if (emailvalid && usernamevalid) ev.preventDefault();

  const AccountDelConfirm = document.querySelectorAll('button[name="ConfirmDelete"]');
  for (let i = 0; i < AccountDelConfirm.length; i++) {
    AccountDelConfirm[i].addEventListener("click", (e) => {
      let confirmDelete = confirm("Are you sure to Delete your Account?");
      if (!confirmDelete) {
        e.preventDefault(); // Prevent the default button click action
      }
    });
  }
  





  

});


