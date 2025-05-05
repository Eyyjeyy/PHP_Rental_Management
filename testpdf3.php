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
    if($admin->isLoggedIn() && $admin->session_role == 'user') {
        header("Location: index.php");
        exit();
    }

    // Check if there's an error message stored in the session
    if (isset($_SESSION['error_message'])) {
        // Display the error message as an alert
        echo '<div class="alert alert-danger mb-0" role="alert">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
        // Unset the session variable to clear the error message
        unset($_SESSION['error_message']);
    }


    $pdf = new FPDI();

    // Load the PDF template
    $templateFile = __DIR__ . "/asset/eviction_template.pdf";
    $pageCount = $pdf->setSourceFile($templateFile);

    // Import the first page of the template
    $template = $pdf->importPage(1);

    // Add a page to the new PDF
    $pdf->AddPage();

    // Use the imported template
    $pdf->useTemplate($template);

    // Set the font for adding text
    $pdf->SetFont('Arial', '', 12);

    $pdf->SetXY(80, 60); 
    $pdf->Cell(150, 7, '2020-01-01', 0, 'L');

    // $pdf->SetXY(42, 116); 
    // $pdf->Cell(50, 7, '2020-01-01', 0, 'L');

    $pdf->SetXY(80, 68); 
    $pdf->Cell(50, 7, 'Tenant username', 0, 'L');

    $pdf->SetXY(80, 77); 
    $pdf->Cell(50, 7, 'Tenant Address', 0, 'L');

    $pdf->SetXY(108, 101); 
    $pdf->Cell(50, 7, $missedpaymenttotal = "1000", 0, 'L');

    $pdf->SetXY(108, 101); 
    $pdf->Cell(50, 7, $missedpaymenttotal = "1000", 0, 'L');

    $pdf->SetXY(104, 115); 
    $pdf->Cell(50, 7, $tenant['price'] = "500", 0, 'L');

    $pdf->SetXY(87, 122.5); 
    $pdf->Cell(50, 7, $misseddates = "2020-01-01, 2020-02-02", 0, 'L');

    $pdf->SetXY(99, 131); 
    $pdf->Cell(50, 7, $missedpaymenttotal = "20000", 0, 'L');

    $pdf->SetXY(32, 152); 
    $pdf->Cell(50, 7, $evictionpaydays = "150", 0, 'L',"C");

    $pdf->SetXY(74, 184); 
    $pdf->Cell(50, 7, $admin_name = "Admin Name", 0, 'L');
    
    $pdf->SetXY(44, 192); 
    $pdf->Cell(50, 7, $admin_address = "Admin Address", 0, 'L');

    $pdf->SetXY(48, 200); 
    $pdf->Cell(50, 7, $phonenumber = "Phone Number", 0, 'L');

    $pdf->SetXY(83, 232); 
    $pdf->Cell(50, 7, $admin_name = "Admin Name", 0, 'L',"C");

    $signatureDatatt = "asset/eviction_tenant/admin_signature_86_67825e6e20998.png";
    $signatureData2tt = "asset/eviction_tenant/admin_signature_86_67825e6e20998.png";

    $pdf->Image($signatureDatatt, 83, 222, 50);  // Adjust position and size

    // Now, handle the signature (base64 image)
    if (!empty($signatureData) || !empty($signatureData2)) {
        // Decode base64 image and add it to PDF
        $signatureImage = str_replace('data:image/png;base64,', '', $signatureData);
        $signatureImage = base64_decode($signatureImage);

        $signatureImage2 = str_replace('data:image/png;base64,', '', $signatureData2);
        $signatureImage2 = base64_decode($signatureImage2);

        // Create a temporary file for the signature image
        $tempSignatureFile = 'asset/eviction_tenant/signature.png';
        file_put_contents($tempSignatureFile, $signatureImage);

        $tempSignatureFile2 = 'asset/eviction_tenant/signature2.png';
        file_put_contents($tempSignatureFile2, $signatureImage2);

        // Add the signature image to the PDF
        $pdf->Image($tempSignatureFile, 130, 120, 50);  // Adjust position and size
        $pdf->Image($tempSignatureFile2, 130, 170, 50);  // Adjust position and size

        // Remove temporary image file after adding to PDF
        unlink($tempSignatureFile);
        unlink($tempSignatureFile2);
    }

    // Output the generated PDF (you can also save it to a file or send it as an email)
    $pdf->Output('I', 'asset/testpdf/' . time() . '-' . uniqid() . '_tenant_data_filled.pdf'); // 'F' means save it on the server, 'I'




    // Set the title for this page
    $pageTitle = "RentTrackPro"; // Change this according to the current page
    $page = "testpdf";
    
?>