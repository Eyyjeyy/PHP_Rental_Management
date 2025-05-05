

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer Example</title>
    <style>
        .footerall {
            width: 100%;
            position: relative;
             left: 0;
             bottom: 0!important;
             margin-top: 15%;        
        }

        .w-65 {
            width: 60%;
        }

        .w-100 {
            width: 100%;
        }

        #bodyfooter {
            display: flex!important;
            justify-content: center!important;
            padding-top: 25px;
            padding-bottom: 25px;
        }

        #linkcontact {
            margin-left: 25%;
        }
        #titlefooter {
            margin-left: 25%;
        }

       /* Initial Styles */
.phone-icon {
    fill: #C95B0E; /* Initial color */
    transition: fill 0.3s ease; /* Smooth transition */
}

#linklink {
    color: #F9E8D9; 
    margin-left: 10px;
    text-decoration: none; /* Removes underline */
    transition: color 0.3s ease, text-shadow 0.3s ease; /* Smooth transition */
}

/* Hover Effects when either element is hovered */
.hover-wrapper:hover .phone-icon {
    fill: #F7B787; /* Hover color */
}

.hover-wrapper:hover #linklink {
    color: #F7B787; /* Change color on hover */
    text-shadow: 0 0 1px rgba(0, 0, 0, 0.1); /* Optional: Adds a subtle shadow effect */
}





            @media (max-width: 780px) {

        #footercontent {
            width: 100%;
        }
        #bodyfooter {
            display: flex!important;           /* Make the container a flex container */
            flex-direction: column;  /* Align children in a column */
            align-items: center;     /* Center items horizontally */
            justify-content: center; /* Center items vertically */
            width: 100%!important;
            padding: 2.5%;
        }

        #linkcontact {
            width: 100%;               /* Ensure the element takes the full width of its container */
            margin-left: 0;           /* No margin on the left */
            overflow-wrap: break-word; /* Ensure long words or URLs wrap to the next line */
            word-break: break-word;   /* Additional property to handle long words */
            white-space: normal;      /* Ensure text wraps to the next line (default behavior) */
        }

        #titlefooter {
            justify-content: left;
            margin: 0;
            margin-bottom: 15px;
            margin-top: 20px;
            padding: 0;
        }

        }
    </style>
</head>
<body>
<div class="footerall">
<div class="container-fluid w-100" style="background-color: #F28543;" id="bodyfooter">

            <div class="row mx-auto w-65" id="bodyfooter">
                <div class="col-6 ps-4" id="footercontent">
             
                        <p class="fs-3" style="color: #F9E8D9;  font-weight: bold" id="titlefooter">
                            Contact
                        </p>
             
                    <div class="d-flex" id="linkcontact">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#C95B0E" class="bi bi-envelope-fill" viewBox="0 0 16 16">
                        <path d="M.05 3.555A2 2 0 0 1 2 2h12a2 2 0 0 1 1.95 1.555L8 8.414zM0 4.697v7.104l5.803-3.558zM6.761 8.83l-6.57 4.027A2 2 0 0 0 2 14h12a2 2 0 0 0 1.808-1.144l-6.57-4.027L8 9.586zm3.436-.586L16 11.801V4.697z"/>
                    </svg>
                    <p class="mb-3" style="color: #F9E8D9; margin-left: 10px; align-text: center;">jemillemaxine@gmail.com</p>
                    </div>
                    <div class="d-flex" id="linkcontact">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#C95B0E" class="bi bi-telephone-fill" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z"/>
                        </svg>
                        <p class="mb-3" style="color: #F9E8D9; margin-left: 10px; align-text: center;">+619324404219</p>
                    </div>
                    <div class="d-flex" id="linkcontact">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#C95B0E" class="bi bi-telephone-fill" viewBox="0 0 16 16">
                        <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6"/>
                        </svg>
                        <p class="mb-1 ms-2" style="color: #F9E8D9; margin-left: 20px; align-text: center;">Tandang Sora, Quezon City</p>
                    </div>
                </div>
                <div class="col-6 ps-4" id="footercontent">
                 
                    <p class="fs-3" style="color: #F9E8D9; font-weight: bold" id="titlefooter">
                            Links
                        </p>
                        <div class="hover-wrapper">
                    <div class="d-flex" id="linkcontact">
                        
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" class="bi bi-envelope phone-icon" viewBox="0 0 16 16">
                        <path d="m12.14 8.753-5.482 4.796c-.646.566-1.658.106-1.658-.753V3.204a1 1 0 0 1 1.659-.753l5.48 4.796a1 1 0 0 1 0 1.506z"/>
                        </svg>
                        <a class="mb-2" href="<?php echo (($pageTitle ?? '') != 'Payments Page') ? 'users/payments.php' : 'payments.php'; ?>" id="linklink">Payment</a>
                    </div>
    </div>
    <div class="hover-wrapper">
                    <div class="d-flex" id="linkcontact">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"" class="bi bi-telephone-fill phone-icon" viewBox="0 0 16 16">
                        <path d="m12.14 8.753-5.482 4.796c-.646.566-1.658.106-1.658-.753V3.204a1 1 0 0 1 1.659-.753l5.48 4.796a1 1 0 0 1 0 1.506z"/>
                        </svg>
                        <a class="mb-2" href="<?php echo (($pageTitle ?? '') != 'Payments Page') ? 'info.php' : '../info.php'; ?>" id="linklink">Info</a>
                    </div>
    </div>
    <div class="hover-wrapper">
                    <div class="d-flex" id="linkcontact">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" class="bi bi-telephone-fill phone-icon" viewBox="0 0 16 16">
                        <path d="m12.14 8.753-5.482 4.796c-.646.566-1.658.106-1.658-.753V3.204a1 1 0 0 1 1.659-.753l5.48 4.796a1 1 0 0 1 0 1.506z"/>
                        </svg>
                        <a class="mb-2" href="<?php echo (($pageTitle ?? '') != 'Payments Page') ? 'profile_user.php' : '../profile_user.php'; ?>" id="linklink">Profile</a>
                    </div>
    </div>
    <div class="hover-wrapper">
                    <div class="d-flex" id="linkcontact">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" class="bi bi-telephone-fill phone-icon" viewBox="0 0 16 16">
                        <path d="m12.14 8.753-5.482 4.796c-.646.566-1.658.106-1.658-.753V3.204a1 1 0 0 1 1.659-.753l5.48 4.796a1 1 0 0 1 0 1.506z"/>
                        </svg>
                        <a class="mb-2" href="<?php echo (($pageTitle ?? '') != 'Payments Page') ? 'contract_user.php' : '../contract_user.php'; ?>" id="linklink">Contract</a>
                    </div>
    </div>
    <div class="hover-wrapper">
                    <div class="d-flex" id="linkcontact">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" class="bi bi-telephone-fill phone-icon" viewBox="0 0 16 16">
                        <path d="m12.14 8.753-5.482 4.796c-.646.566-1.658.106-1.658-.753V3.204a1 1 0 0 1 1.659-.753l5.48 4.796a1 1 0 0 1 0 1.506z"/>
                        </svg>
                        <a class="mb-2" href="<?php echo (($pageTitle ?? '') != 'Payments Page') ? 'user_delinquency.php' : '../user_delinquency.php'; ?>" id="linklink">Delinquency</a>
                    </div>
    </div>
    <div class="hover-wrapper">
                    <div class="d-flex" id="linkcontact">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" class="bi bi-telephone-fill phone-icon" viewBox="0 0 16 16">
                        <path d="m12.14 8.753-5.482 4.796c-.646.566-1.658.106-1.658-.753V3.204a1 1 0 0 1 1.659-.753l5.48 4.796a1 1 0 0 1 0 1.506z"/>
                        </svg>
                        <a class="mb-1" href="<?php echo (($pageTitle ?? '') != 'Payments Page') ? 'chat_user.php' : '../chat_user.php'; ?>" id="linklink">Chat</a>
                    </div>
    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid" style="background-color: #C95B0E; padding: 2.5px; padding-top: 15px;">
        <div class="row mx-auto w-65 justify-content-center d-flex align-items-center">
                    <div class="d-flex justify-content-center">
                    <p class="text-white" style="font-weight: bold; font-size: 20px;">
                        Rent Track Pro @ 2024
                        </p>
                    </d-flex>
                    </div>
                </div>
    </div>
    </body>
</html>