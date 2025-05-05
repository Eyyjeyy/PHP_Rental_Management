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
    
    if(isset($_POST['testpdf'])) {
        $_SESSION['error_message'] = "test";

        $name = $_POST['name'];  // Assuming you have these fields in your form
        $email = $_POST['email'];
        $message = $_POST['message'];

        // The signature is a base64 image string
        $signatureData = $_POST['signature'];
        $signatureData2 = $_POST['signature2'];

        // Initialize FPDI (extend FPDF class)
        $pdf = new FPDI();

        // Load the PDF template
        $templateFile = 'asset/testpdf/contract.pdf';  // Path to your static PDF template
        $pageCount = $pdf->setSourceFile($templateFile);

        // Import the first page of the template
        $template = $pdf->importPage(1);

        // Add a page to the new PDF
        $pdf->AddPage();

        // Use the imported template
        $pdf->useTemplate($template);

        // Set the font for adding text
        $pdf->SetFont('Arial', '', 12);

        // Position where you want to add the text and fill in data (replace placeholders)
        $pdf->SetXY(50, 27); // Adjust these coordinates to match where the placeholder is located
        $pdf->Cell(0, 10, $name, 0, 1);

        $pdf->SetXY(50, 38); 
        $pdf->Cell(0, 10, $email, 0, 1);

        $pdf->SetXY(35, 48); // Adjust as needed for message
        $pdf->MultiCell(140, 7, $message, 0, 'L');

        $pdf->SetXY(25, 90); 
        $pdf->MultiCell(150, 7, 'addresssss', 0, 'L');

        $pdf->SetXY(42, 110); 
        $pdf->Cell(50, 7, '2020-01-01', 0, 'L');

        $pdf->SetXY(102, 110); 
        $pdf->Cell(50, 7, '2020-01-01', 0, 'L');

        $pdf->SetXY(115, 116); 
        $pdf->Cell(50, 7, '2000000', 0, 'L');

        $pdf->SetXY(62, 122); 
        $pdf->Cell(50, 7, '2020-01-01', 0, 'L');

        $pdf->SetXY(42, 142); 
        $pdf->Cell(50, 7, '2000000', 0, 'L');

        // Handle the second page
        $template2 = $pdf->importPage(2); // Import the second page
        $pdf->AddPage(); // Add another new page
        $pdf->useTemplate($template2); // Use the imported second page

        $pdf->SetXY(42, 116); 
        $pdf->Cell(50, 7, '2020-01-01', 0, 'L');

        $pdf->SetXY(32, 131); 
        $pdf->Cell(50, 7, 'Admin username', 0, 'L');

        $pdf->SetXY(32, 188); 
        $pdf->Cell(50, 7, 'Signatory Lessor', 0, 'L');

        // Now, handle the signature (base64 image)
        if (!empty($signatureData) && !empty($signatureData2)) {
            // Decode base64 image and add it to PDF
            $signatureImage = str_replace('data:image/png;base64,', '', $signatureData);
            $signatureImage = base64_decode($signatureImage);

            $signatureImage2 = str_replace('data:image/png;base64,', '', $signatureData2);
            $signatureImage2 = base64_decode($signatureImage2);

            // Create a temporary file for the signature image
            $tempSignatureFile = 'asset/testpdf/signature.png';
            file_put_contents($tempSignatureFile, $signatureImage);

            $tempSignatureFile2 = 'asset/testpdf/signature2.png';
            file_put_contents($tempSignatureFile2, $signatureImage2);

            // Add the signature image to the PDF
            $pdf->Image($tempSignatureFile, 30, 120, 50);  // Adjust position and size
            $pdf->Image($tempSignatureFile2, 30, 170, 50);  // Adjust position and size

            // Remove temporary image file after adding to PDF
            unlink($tempSignatureFile);
        }

        // Output the generated PDF (you can also save it to a file or send it as an email)
        $pdf->Output('F', 'asset/testpdf/' . time() . '-' . uniqid() . '_user_data_filled.pdf'); // 'F' means save it on the server, 'I'

        header("Location: testpdf.php");
        exit();
    }

    // Check if there's an error message stored in the session
    if (isset($_SESSION['error_message'])) {
        // Display the error message as an alert
        echo '<div class="alert alert-danger mb-0" role="alert">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
        // Unset the session variable to clear the error message
        unset($_SESSION['error_message']);
    }

    

    

    

    // Retrieve users data from the database
    // $sql = "SELECT * FROM users ORDER BY $sortColumn $sortDirection";
    // $result = $admin->conn->query($sql);

    // Set the title for this page
    $pageTitle = "RentTrackPro"; // Change this according to the current page
    $page = "testpdf";
?>

    
    <div class="container-fluid">
        <div class="row">
            <form action="testpdf.php" method="POST">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="John S. Milton" required><br><br>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="test@gmail.com" required><br><br>

                <label for="message">Message:</label>
                <textarea id="message" name="message" required>house_1_test</textarea><br><br>

                <!-- Hidden input to store the signature image data -->
                <input type="hidden" name="signature" id="signature-input">
                <label for="signature">Signature:</label>
                <canvas id="signature-pad" width="400" height="200" style="border: 1px solid #000;"></canvas>
                <button type="button" id="clear-signature">Clear</button>

                <input type="hidden" name="signature2" id="signature-input2">
                <label for="signature2">Signature:</label>
                <canvas id="signature-pad2" width="400" height="200" style="border: 1px solid #000;"></canvas>
                <button type="button" id="clear-signature2">Clear</button>

                <button type="submit" name="testpdf">Generate PDF</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <!-- <script>
        // Initialize Signature Pad
        const canvas = document.getElementById('signature-pad');
        const canvas2 = document.getElementById('signature-pad2');
        const signaturePad = new SignaturePad(canvas);
        const signaturePad2 = new SignaturePad(canvas2);

        // Clear button functionality
        document.getElementById('clear-signature').addEventListener('click', function () {
            signaturePad.clear();
        });

        document.getElementById('clear-signature2').addEventListener('click', function () {
            signaturePad2.clear();
        });

        // Before submitting the form, get the signature data in base64 format
        const form = document.querySelector('form');
        form.addEventListener('submit', function (event) {
            // Check if signature is drawn
            if (!signaturePad.isEmpty() && !signaturePad2.isEmpty()) {
                // Get the signature as base64 image data
                const signatureData = signaturePad.toDataURL();
                const signatureData2 = signaturePad2.toDataURL();
                // Set the signature data to the hidden input
                document.getElementById('signature-input').value = signatureData;
                document.getElementById('signature-input2').value = signatureData2;
            } else {
                // If no signature, you can handle it (e.g., show a warning)
                alert("Please provide a signature.");
                event.preventDefault();
            }
        });
    </script> -->

    <script>
        // Initialize Signature Pad
        const canvas = document.getElementById('signature-pad');
        const canvas2 = document.getElementById('signature-pad2');
        const signaturePad = new SignaturePad(canvas);
        const signaturePad2 = new SignaturePad(canvas2);

        // Function to center and process signature data
        function processSignature(signaturePad, canvas) {
            if (signaturePad.isEmpty()) {
                return null;
            }

            const context = canvas.getContext('2d');
            const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
            const { width, height } = canvas;

            // Find the bounding box of the signature
            let minX = width, minY = height, maxX = 0, maxY = 0;
            for (let y = 0; y < height; y++) {
                for (let x = 0; x < width; x++) {
                    const alpha = imageData.data[(y * width + x) * 4 + 3];
                    if (alpha > 0) { // If pixel is not transparent
                        if (x < minX) minX = x;
                        if (y < minY) minY = y;
                        if (x > maxX) maxX = x;
                        if (y > maxY) maxY = y;
                    }
                }
            }

            // Calculate the dimensions of the signature bounding box
            const signatureWidth = maxX - minX;
            const signatureHeight = maxY - minY;

            if (signatureWidth <= 0 || signatureHeight <= 0) {
                return null; 
            }

            // Create a new canvas to center the signature
            const centeredCanvas = document.createElement('canvas');
            centeredCanvas.width = width;
            centeredCanvas.height = height;
            const centeredContext = centeredCanvas.getContext('2d');

            // Center the signature on the new canvas
            const offsetX = (width - signatureWidth) / 2;
            const offsetY = (height - signatureHeight) / 2;
            centeredContext.drawImage(
                canvas,
                minX, minY, signatureWidth, signatureHeight, 
                offsetX, offsetY, signatureWidth, signatureHeight 
            );

            return centeredCanvas.toDataURL(); // Return the centered signature as base64
        }

        // Clear button functionality
        document.getElementById('clear-signature').addEventListener('click', function () {
            signaturePad.clear();
        });

        document.getElementById('clear-signature2').addEventListener('click', function () {
            signaturePad2.clear();
        });

        // Before submitting the form, get the signature data in base64 format
        const form = document.querySelector('form');
        form.addEventListener('submit', function (event) {
            const signatureData = processSignature(signaturePad, canvas);
            const signatureData2 = processSignature(signaturePad2, canvas2);

            if (signatureData && signatureData2) {
                // Set the signature data to the hidden input
                document.getElementById('signature-input').value = signatureData;
                document.getElementById('signature-input2').value = signatureData2;
            } else {
                // If no signature, handle it (e.g., show a warning)
                alert("Please provide a signature.");
                event.preventDefault();
            }
        });
    </script>


    <!-- <?php include 'includes/footer.php'; ?> -->
