<!-- <HTML>
    <head>
        <title>Online Signature With HTML + Javascript Example</title>

        <style>
            body {
                max-width: 400px;
                margin: 0 auto;
                font-family:sans-serif;
                text-align: center;
            }
            .wrapper {
                position: relative;
                width: 400px;
                height: 200px;
                -moz-user-select: none;
                -webkit-user-select: none;
                -ms-user-select: none;
                user-select: none;
                border: solid 1px #ddd;
                margin: 10px 0px;
            }
            .signature-pad {
                position: absolute;
                left: 0;
                top: 0;
                width:400px;
                height:200px;
            }
            textarea {
                width: 100%;
                min-height: 100px;
            }
        </style>

    </head>
    <body>
        <h2>Online Signature With HTML + Javascript Example</h2>
        <form id="form-submit" method="POST">
            <div class="wrapper">
                <canvas id="signature-pad" class="signature-pad" width=400 height=200></canvas>
            </div>
            <button id="clear" class="btn btn-sm btn-secondary">Clear</button>
            <button id="save" class="btn btn-success">Save</button>
        </form>

        <br/>
        <hr>
        <h3>Results</h3>
        <textarea type="hidden" id='signature-result' name="signature-result"></textarea>
        <img src="" id="signature-img-result" />

        <script
            src="https://code.jquery.com/jquery-3.5.1.min.js"
            integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
            crossorigin="anonymous"></script>

        <script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
        <script>
        $(function() {
            // init signaturepad
            var signaturePad = new SignaturePad(document.getElementById('signature-pad'), {
                    backgroundColor: 'rgba(255, 255, 255, 0)',
            penColor: 'rgb(0, 0, 0)'
            });

            // get image data and put to hidden input field
            function getSignaturePad() {
                var imageData = signaturePad.toDataURL('image/png');
                $('#signature-result').val(imageData)
                $('#signature-img-result').attr('src',"data:"+imageData);
            }

            // form action
            $('#form-submit').submit(function() {
                getSignaturePad();
                return false; // set true to submits the form.
            });

            // action on click button clea
            $('#clear').click(function(e) {
                e.preventDefault();
                signaturePad.clear();
            })
        });
        </script>
    </body>
</HTML> -->

<script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
<style>
    #signature-pad {min-height:200px;border: 1px solid #000;}
    #signature-pad canvas {position: absolute;left: 0;top: 0;width: 100%;height: 100%}
</style>
<div id="signature-pad">
    <canvas style="border:1px solid #000" id="sign"></canvas>
</div>

<script>
    var wrapper = document.getElementById("signature-pad");
var canvas = wrapper.querySelector("canvas");

var sign = new SignaturePad(document.getElementById('sign'), {
  backgroundColor: 'rgba(255, 255, 255, 0)',
  penColor: 'rgb(0, 0, 0)'
});

function resizeCanvas() {
     var ratio =  Math.max(window.devicePixelRatio || 1, 1);

     canvas.width = canvas.offsetWidth * ratio;
     canvas.height = canvas.offsetHeight * ratio;
     canvas.getContext("2d").scale(ratio, ratio);
}

window.onresize = resizeCanvas;
resizeCanvas();
</script>
