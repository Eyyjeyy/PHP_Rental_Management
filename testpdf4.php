<?php
    // session_start(); // Start the session (important for checking session variables)
    include 'admin.php';
    $admin = new Admin();
    use setasign\Fpdi\Fpdi;
    
    // $admin->handleRedirect(); // Call handleRedirect to check login status and redirect

    // Removing this causes website to proceed to index.php despite isLoggedIn from admin.php returning false
    if(!$admin->isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
    // if($admin->isLoggedIn() && $admin->session_role == 'user') {
    //     header("Location: index.php");
    //     exit();
    // }

    // Check if there's an error message stored in the session
    if (isset($_SESSION['error_message'])) {
        // Display the error message as an alert
        echo '<div class="alert alert-danger mb-0" role="alert">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
        // Unset the session variable to clear the error message
        unset($_SESSION['error_message']);
    }


    $pdf = new FPDI();

    // Load the PDF template
    $templateFile = __DIR__ . "/asset/testpdf/contract.pdf";
    $pageCount = $pdf->setSourceFile($templateFile);

    // Import the first page of the template
    $template = $pdf->importPage(1);

    // Add a page to the new PDF
    $pdf->AddPage();

    // Use the imported template
    $pdf->useTemplate($template);

    // Set the font for adding text
    $pdf->SetFont('Arial', '', 12);

    $pdf->SetXY(50, 27); // Adjust these coordinates to match where the placeholder is located
    $pdf->Cell(0, 10, "username", 0, 1);

    $pdf->SetXY(50, 38); 
    $pdf->Cell(0, 10, "tenantusername", 0, 1);

    $pdf->SetXY(25, 90); 
    $pdf->MultiCell(150, 7, "apartmentaddressinput", 0, 'L');
    
    $pdf->SetXY(42, 110); 
    $pdf->Cell(50, 7, "datestart", 0, 'L');

    $pdf->SetXY(102, 110); 
    $pdf->Cell(50, 7, "expirationdate", 0, 'L');

    $pdf->SetXY(115, 116); 
    $pdf->Cell(50, 7, "rentprice", 0, 'L');

    $pdf->SetXY(62, 122); 
    $pdf->Cell(50, 7, "formattedDay", 0, 'L');

    $pdf->SetXY(42, 142); 
    $pdf->Cell(50, 7, "deposit", 0, 'L');



    $pdf->SetXY(25, 50); 
    $pdf->MultiCell(150, 7, "(COMPLETE CONTRACT FOR USER)tenant's previous address = SQUEEEE", 0, 'L');



    $template2 = $pdf->importPage(2); // Import the second page
    $pdf->AddPage(); // Add another new page
    $pdf->useTemplate($template2); // Use the imported second page

    $pdf->SetXY(42, 111); 
    $pdf->Cell(50, 7, "current date", 0, 'L');

    $pdf->SetXY(32, 126); 
    $pdf->Cell(50, 7, "username", 0, 'L');

    $pdf->SetXY(32, 188); 
    $pdf->Cell(50, 7, "lessorwitness", 0, 'L');

    $pdf->SetXY(142, 126); 
    $pdf->Cell(50, 7, "tenantusername", 0, 'L');


    $pdf->SetXY(48, 147); 
    $pdf->Cell(50, 7, "CTC ID NO #", 0, 'L');

    $pdf->SetXY(44, 152); 
    $pdf->Cell(50, 7, "ID Type", 0, 'L');

    $pdf->SetXY(52, 157); 
    $pdf->Cell(50, 7, "ID Date Issued", 0, 'L');

    $pdf->SetXY(50, 162); 
    $pdf->Cell(50, 7, "ID ExpirationDate", 0, 'L');


    ////// COMPLETE CONTRACT FOR USER    
    $pdf->SetXY(148, 147); 
    $pdf->Cell(50, 7, "CTC ID NO #", 0, 'L');

    $pdf->SetXY(144, 152); 
    $pdf->Cell(50, 7, "ID Type", 0, 'L');

    $pdf->SetXY(154, 157); 
    $pdf->Cell(50, 7, "ID Date Issued", 0, 'L');

    $pdf->SetXY(152, 162); 
    $pdf->Cell(50, 7, "ID ExpirationDate", 0, 'L');
    ////// COMPLETE CONTRACT FOR USER

    $signatureDatatt = "asset/eviction_tenant/admin_signature_86_67825e6e20998.png";
    $signatureData2tt = "asset/eviction_tenant/admin_signature_86_67825e6e20998.png";

    $pdf->Image($signatureDatatt, 32, 116, 50);  // Adjust position and size
    $pdf->Image($signatureData2tt, 34, 180, 50);  // Adjust position and size

    ////// COMPLETE CONTRACT FOR USER
    $pdf->SetXY(140, 188); 
    $pdf->Cell(50, 7, "lesseewitness", 0, 'L');
    $pdf->Image($signatureDatatt, 132, 116, 50);  // Adjust position and size
    $pdf->Image($signatureData2tt, 132, 180, 50);  // Adjust position and size
    ////// COMPLETE CONTRACT FOR USER

    // Output the generated PDF (you can also save it to a file or send it as an email)
    $pdf->Output('I', 'asset/testpdf/' . time() . '-' . uniqid() . '_tenant_data_filled.pdf'); // 'F' means save it on the server, 'I'




    // Set the title for this page
    $pageTitle = "RentTrackPro"; // Change this according to the current page
    $page = "testpdf";
    
?>