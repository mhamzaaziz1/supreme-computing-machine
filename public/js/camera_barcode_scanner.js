/**
 * Camera Barcode Scanner for POS
 * 
 * This script handles the camera-based barcode scanning functionality for the POS system.
 * It uses the HTML5 QR Code library to access the device camera and scan barcodes.
 */

/**
 * Helper function to safely update the scanner result element
 * @param {string} html - The HTML content to set
 */
function updateScannerResult(html) {
    if ($('#scanner-result').length > 0) {
        $('#scanner-result').html(html);
    } else {
        console.error('Scanner result element not found in the DOM');
    }
}

// Initialize when the document is ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Initialize the camera scanner
    // The HTML5 QR Code library is already loaded in the main template
    initCameraScanner();
});

// Global variables
var html5QrCode;
var isScanning = false;
var cameras = [];
var currentCameraId = null;
var debugMode = false; // Set to true to enable debug mode

/**
 * Initialize the camera scanner
 */
function initCameraScanner() {
    // Check if the camera modal exists
    if ($('#camera_barcode_modal').length === 0) {
        console.error('Camera barcode modal not found in the DOM');
        return;
    }

    console.log('Initializing camera scanner');

    // Add camera selection dropdown to the modal
    $('#camera_barcode_modal .modal-header').after(
        '<div class="camera-selection-container" style="padding: 10px 15px; border-bottom: 1px solid #e5e5e5;">' +
        '<select id="camera-selection" class="form-control" style="width: 100%;">' +
        '<option value="">Loading cameras...</option>' +
        '</select>' +
        '</div>'
    );

    // Add debug toggle button to the modal footer
    $('#camera_barcode_modal .modal-footer').prepend(
        '<div class="pull-left">' +
        '<label class="checkbox-inline" style="margin-right: 10px;">' +
        '<input type="checkbox" id="debug-mode-toggle"> Debug Mode' +
        '</label>' +
        '</div>'
    );

    // Add debug panel to the modal
    $('#camera_barcode_modal .modal-body').append(
        '<div id="debug-panel" style="display: none; margin-top: 15px; padding: 10px; background-color: #f8f9fa; border-radius: 5px; font-family: monospace; font-size: 12px;">' +
        '<h5>Debug Information</h5>' +
        '<div id="debug-info"></div>' +
        '</div>'
    );

    // Handle debug mode toggle
    $(document).on('change', '#debug-mode-toggle', function() {
        debugMode = $(this).is(':checked');
        $('#debug-panel').toggle(debugMode);

        if (debugMode) {
            updateDebugInfo('Debug mode enabled');
        }
    });

    // Initialize the scanner when the modal is shown
    $('#camera_barcode_modal').on('shown.bs.modal', function() {
        console.log('Camera modal shown, starting scanner');
        startScanner();
    });

    // Stop the scanner when the modal is hidden
    $('#camera_barcode_modal').on('hidden.bs.modal', function() {
        console.log('Camera modal hidden, stopping scanner');
        stopScanner();
    });

    // Handle camera selection change
    $(document).on('change', '#camera-selection', function() {
        const cameraId = $(this).val();
        if (cameraId && cameraId !== currentCameraId) {
            stopScanner();
            startScanningWithCamera(cameraId);
        }
    });
}

/**
 * Start the barcode scanner
 */
function startScanner() {
    if (isScanning) return;

    updateDebugInfo('Starting scanner initialization');

    // Check if Html5Qrcode is defined
    if (typeof Html5Qrcode === 'undefined') {
        updateScannerResult('<div class="alert alert-danger">Error: Barcode scanning library not loaded. Please refresh the page and try again.</div>');
        updateDebugInfo('Error: Html5Qrcode library not defined');
        return;
    }

    // Check if we're in a secure context (HTTPS or localhost)
    if (!window.isSecureContext && window.location.protocol !== 'https:' && window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1') {
        updateScannerResult('<div class="alert alert-danger">Camera access requires a secure connection (HTTPS). Please access this page via HTTPS or contact your administrator.</div>');
        console.error('Camera access requires HTTPS. Current protocol:', window.location.protocol);
        return;
    }

    // Check if the browser supports the MediaDevices API
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        updateScannerResult('<div class="alert alert-danger">Your browser does not support camera access. Please use a modern browser like Chrome, Firefox, Safari, or Edge.</div>');
        console.error('Browser does not support MediaDevices API');
        return;
    }

    // Check if scanner container exists
    if ($('#scanner-container').length === 0) {
        updateScannerResult('<div class="alert alert-danger">Scanner container element not found. Please refresh the page and try again.</div>');
        console.error('Scanner container element not found in the DOM');
        return;
    }

    try {
        // Create an instance of the scanner
        html5QrCode = new Html5Qrcode("scanner-container");

        // Get available cameras
        Html5Qrcode.getCameras().then(devices => {
            cameras = devices;
            console.log("Cameras detected:", devices);

            if (devices && devices.length) {
                // Populate camera selection dropdown
                const $cameraSelection = $('#camera-selection');
                $cameraSelection.empty();

                if (devices.length > 1) {
                    $cameraSelection.append('<option value="">Select a camera</option>');
                    devices.forEach((device, index) => {
                        const label = device.label || `Camera ${index + 1}`;
                        $cameraSelection.append(`<option value="${device.id}">${label}</option>`);
                    });

                    // Show the camera selection dropdown
                    $('.camera-selection-container').show();

                    // Start with the first camera by default
                    $cameraSelection.val(devices[0].id).trigger('change');
                } else {
                    // Only one camera available, hide the dropdown
                    $('.camera-selection-container').hide();

                    // Start scanning with the only camera
                    startScanningWithCamera(devices[0].id);
                }
            } else {
                $('.camera-selection-container').hide();
                updateScannerResult('<div class="alert alert-warning">No camera devices found. Please make sure your device has a camera and you have granted permission to use it.</div>');
            }
        }).catch(err => {
            console.error("Error getting cameras:", err);
            $('.camera-selection-container').hide();

            // Try a fallback method for older browsers
            tryFallbackCameraAccess();
        });
    } catch (error) {
        updateScannerResult('<div class="alert alert-danger">Error initializing scanner: ' + error + '. Please refresh the page and try again.</div>');
    }
}

/**
 * Start scanning with a specific camera
 * 
 * @param {string} cameraId - The ID of the camera to use
 */
function startScanningWithCamera(cameraId) {
    currentCameraId = cameraId;
    console.log("Starting camera with ID:", cameraId);
    updateDebugInfo("Starting camera with ID: " + cameraId);

    // Add visual indicator for the scanning area
    addScanningAreaIndicator();

    // Improved configuration for better barcode detection
    const config = {
        fps: 15, // Increased frame rate for better detection
        qrbox: { width: 300, height: 150 }, // Wider box for barcode scanning (barcodes are usually wider than tall)
        aspectRatio: 1.0,
        formatsToSupport: [
            Html5QrCode.FORMATS.EAN_13,
            Html5QrCode.FORMATS.EAN_8,
            Html5QrCode.FORMATS.UPC_A,
            Html5QrCode.FORMATS.UPC_E,
            Html5QrCode.FORMATS.CODE_39,
            Html5QrCode.FORMATS.CODE_93,
            Html5QrCode.FORMATS.CODE_128,
            Html5QrCode.FORMATS.ITF,
            Html5QrCode.FORMATS.CODABAR
        ],
        experimentalFeatures: {
            useBarCodeDetectorIfSupported: true // Use the built-in BarcodeDetector API if available
        },
        verbose: true // Enable verbose logging for debugging
    };

    updateScannerResult('<span>Starting camera...</span>');

    // Check if camera permissions are already granted
    navigator.permissions.query({ name: 'camera' })
        .then(permissionStatus => {
            console.log("Camera permission status:", permissionStatus.state);

            if (permissionStatus.state === 'denied') {
                updateScannerResult('<div class="alert alert-danger">Camera access denied. Please allow camera access in your browser settings and try again.</div>');
                return;
            }

            // Start the scanner
            html5QrCode.start(
                cameraId, 
                config,
                onScanSuccess,
                onScanFailure
            ).then(() => {
                console.log("Camera started successfully");
                isScanning = true;
                updateScannerResult('<span>Scanning for barcodes... Position the barcode in the center of the camera view.</span>');
            }).catch(err => {
                console.error("Error starting camera:", err);
                updateScannerResult('<div class="alert alert-danger">Error starting scanner: ' + err + '. Please try a different camera or refresh the page.</div>');
            });
        })
        .catch(error => {
            console.error("Error checking camera permissions:", error);
            // Fallback to direct camera access if permissions API is not supported
            html5QrCode.start(
                cameraId, 
                config,
                onScanSuccess,
                onScanFailure
            ).then(() => {
                console.log("Camera started successfully (fallback)");
                isScanning = true;
                updateScannerResult('<span>Scanning for barcodes... Position the barcode in the center of the camera view.</span>');
            }).catch(err => {
                console.error("Error starting camera (fallback):", err);
                updateScannerResult('<div class="alert alert-danger">Error starting scanner: ' + err + '. Please try a different camera or refresh the page.</div>');
            });
        });
}

/**
 * Handle successful barcode scan
 * 
 * @param {string} decodedText - The decoded barcode text
 */
function onScanSuccess(decodedText, decodedResult) {
    // Log the successful scan for debugging
    console.log("Barcode successfully scanned:", decodedText);
    console.log("Scan result details:", decodedResult);

    // Update debug info
    updateDebugInfo("Barcode detected: " + decodedText + " (Format: " + 
        (decodedResult.result.format ? decodedResult.result.format.formatName : 'Unknown') + ")");

    // Update the result display
    updateScannerResult('<div class="alert alert-success">' +
        '<strong>Barcode detected:</strong> ' + decodedText + '<br>' +
        '<small>Format: ' + (decodedResult.result.format ? decodedResult.result.format.formatName : 'Unknown') + '</small>' +
        '</div>');

    // Flash the scanner container to provide visual feedback
    $('#scanner-container').css('border', '3px solid #5cb85c');
    setTimeout(function() {
        $('#scanner-container').css('border', '');
    }, 500);

    // Stop scanning
    stopScanner();

    // Close the modal
    $('#camera_barcode_modal').modal('hide');

    // Set the barcode in the search field and trigger search
    $('#search_product').val(decodedText);
    updateDebugInfo('Setting search field value to: ' + decodedText);

    // Trigger both input and keyup events to ensure the search is triggered
    $('#search_product').trigger('input');
    $('#search_product').trigger('keyup');
    updateDebugInfo('Triggered input and keyup events');

    // If direct triggering doesn't work, try to simulate an enter key press
    var e = $.Event('keyup');
    e.which = 13; // Enter key
    $('#search_product').trigger(e);
    updateDebugInfo('Simulated Enter key press');

    // Add a small delay and check if the product was found
    setTimeout(function() {
        // Check if any products were added to the cart
        const productRows = $('#pos_table tbody tr').length;
        updateDebugInfo('Product rows after search: ' + productRows);

        // If no products were found, try an alternative approach
        if (productRows === 0) {
            updateDebugInfo('No products found, trying alternative search approach');

            // Try to directly call the product search function if it exists
            if (typeof pos_product_row === 'function') {
                updateDebugInfo('Attempting direct product lookup');

                // This is a fallback that attempts to find the product by barcode
                // We'll use the POS system's existing search functionality
                $.getJSON('/products/list', {
                    term: decodedText,
                    location_id: $('input#location_id').val(),
                    not_for_selling: 0
                }, function(data) {
                    if (data && data.length > 0) {
                        updateDebugInfo('Product found via API: ' + data[0].text);
                        pos_product_row(data[0].id);
                    } else {
                        updateDebugInfo('Product not found in database: ' + decodedText);
                        toastr.error('Product not found: ' + decodedText);
                    }
                });
            }
        }
    }, 1000);

    // Play a beep sound to indicate successful scan
    playBeepSound();
}

/**
 * Handle scan failure
 * 
 * @param {string} error - The error message
 */
function onScanFailure(error) {
    // We don't need to show errors as they happen frequently during scanning
    // But we'll log them for debugging purposes
    if (error && error !== 'QR code parse error') {
        console.debug('Scan error:', error);

        // Only log significant errors to the debug panel to avoid flooding it
        if (error !== 'No barcode or QR code detected.') {
            updateDebugInfo('Scan error: ' + error);
        }
    }

    // Periodically update debug info with scanning status
    if (debugMode && Math.random() < 0.05) { // Only log occasionally (5% chance)
        updateDebugInfo('Scanning active, no barcode detected yet');
    }
}

/**
 * Stop the barcode scanner
 */
function stopScanner() {
    if (!html5QrCode) {
        console.log('Scanner not initialized, nothing to stop');
        return;
    }

    if (!isScanning) {
        console.log('Scanner not currently scanning, nothing to stop');
        return;
    }

    console.log('Stopping scanner...');

    html5QrCode.stop().then(() => {
        console.log('Scanner stopped successfully');
        isScanning = false;
        currentCameraId = null;
    }).catch(err => {
        console.error('Error stopping scanner:', err);
        // Even if there's an error, mark as not scanning to allow restarting
        isScanning = false;
        currentCameraId = null;

        // Try to clean up the scanner instance
        try {
            html5QrCode = null;
        } catch (e) {
            console.error('Error cleaning up scanner instance:', e);
        }
    });
}

/**
 * Try a fallback method for camera access on older browsers
 */
function tryFallbackCameraAccess() {
    console.log("Trying fallback camera access method");

    // Display a message to the user
    updateScannerResult('<div class="alert alert-info">Trying alternative method to access your camera...</div>');

    // Try direct camera access without enumerating devices
    try {
        // Create a simple video element for camera preview
        const videoElement = document.createElement('video');
        videoElement.style.width = '100%';
        videoElement.style.height = '300px';
        videoElement.autoplay = true;

        // Replace the scanner container with the video element
        $('#scanner-container').empty().append(videoElement);

        // Try to access the camera directly
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(function(stream) {
                    console.log("Fallback camera access successful");
                    videoElement.srcObject = stream;

                    // Create a container for the manual barcode input with improved styling
                    const manualInputContainer = document.createElement('div');
                    manualInputContainer.className = 'manual-barcode-input';
                    manualInputContainer.style.marginTop = '15px';
                    manualInputContainer.style.padding = '15px';
                    manualInputContainer.style.backgroundColor = '#f8f9fa';
                    manualInputContainer.style.borderRadius = '5px';
                    manualInputContainer.style.border = '1px solid #ddd';
                    manualInputContainer.style.boxShadow = '0 2px 5px rgba(0,0,0,0.1)';

                    // Add a title for the manual input section
                    const inputTitle = document.createElement('h4');
                    inputTitle.innerHTML = '<i class="fa fa-keyboard"></i> Manual Barcode Entry';
                    inputTitle.style.marginTop = '0';
                    inputTitle.style.marginBottom = '10px';
                    inputTitle.style.color = '#333';
                    manualInputContainer.appendChild(inputTitle);

                    // Add instructions
                    const instructions = document.createElement('p');
                    instructions.innerText = 'Type or paste the barcode number below and press Enter or click Search';
                    instructions.style.marginBottom = '15px';
                    instructions.style.fontSize = '13px';
                    instructions.style.color = '#666';
                    manualInputContainer.appendChild(instructions);

                    // Create a form for manual barcode input
                    const inputGroup = document.createElement('div');
                    inputGroup.className = 'input-group';
                    inputGroup.style.marginBottom = '10px';

                    // Add barcode icon to input
                    const inputGroupAddon = document.createElement('span');
                    inputGroupAddon.className = 'input-group-addon';
                    inputGroupAddon.innerHTML = '<i class="fa fa-barcode"></i>';
                    inputGroup.appendChild(inputGroupAddon);

                    // Create the input field with improved styling
                    const barcodeInput = document.createElement('input');
                    barcodeInput.type = 'text';
                    barcodeInput.className = 'form-control input-lg';
                    barcodeInput.placeholder = 'Enter barcode number';
                    barcodeInput.id = 'manual-barcode-input';
                    barcodeInput.style.fontSize = '16px';
                    barcodeInput.style.height = '46px';
                    barcodeInput.autocomplete = 'off';
                    barcodeInput.autofocus = true;

                    const inputGroupBtn = document.createElement('span');
                    inputGroupBtn.className = 'input-group-btn';

                    // Create clear button
                    const clearButton = document.createElement('button');
                    clearButton.className = 'btn btn-default btn-lg';
                    clearButton.type = 'button';
                    clearButton.innerHTML = '<i class="fa fa-times"></i>';
                    clearButton.title = 'Clear';
                    clearButton.onclick = function() {
                        barcodeInput.value = '';
                        barcodeInput.focus();
                    };

                    // Create search button with improved styling
                    const submitButton = document.createElement('button');
                    submitButton.className = 'btn btn-primary btn-lg';
                    submitButton.type = 'button';
                    submitButton.innerHTML = '<i class="fa fa-search"></i> Search';
                    submitButton.style.marginLeft = '5px';

                    // Function to process the barcode
                    const processBarcode = function() {
                        const barcode = barcodeInput.value.trim();
                        if (barcode) {
                            // Show loading indicator
                            submitButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Searching...';
                            submitButton.disabled = true;

                            // Use the same logic as in onScanSuccess
                            $('#search_product').val(barcode);
                            $('#search_product').trigger('input');
                            $('#search_product').trigger('keyup');

                            // Simulate Enter key press
                            var e = $.Event('keyup');
                            e.which = 13;
                            $('#search_product').trigger(e);

                            // Add a small delay before closing to show the loading state
                            setTimeout(function() {
                                // Close the modal
                                $('#camera_barcode_modal').modal('hide');

                                // Stop the camera
                                stream.getTracks().forEach(track => track.stop());
                            }, 500);
                        } else {
                            // Shake the input to indicate it's empty
                            barcodeInput.style.animation = 'shake 0.5s';
                            setTimeout(function() { 
                                barcodeInput.style.animation = ''; 
                            }, 500);
                            barcodeInput.focus();
                        }
                    };

                    // Set onclick handler
                    submitButton.onclick = processBarcode;

                    // Add event listener for Enter key
                    barcodeInput.addEventListener('keyup', function(event) {
                        if (event.key === 'Enter') {
                            processBarcode();
                        }
                    });

                    // Add CSS for shake animation
                    const style = document.createElement('style');
                    style.textContent = `
                        @keyframes shake {
                            0%, 100% { transform: translateX(0); }
                            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
                            20%, 40%, 60%, 80% { transform: translateX(5px); }
                        }
                    `;
                    document.head.appendChild(style);

                    // Assemble the input group
                    inputGroupBtn.appendChild(clearButton);
                    inputGroupBtn.appendChild(submitButton);
                    inputGroup.appendChild(barcodeInput);
                    inputGroup.appendChild(inputGroupBtn);
                    manualInputContainer.appendChild(inputGroup);

                    // Add a note about supported barcode types
                    const barcodeNote = document.createElement('div');
                    barcodeNote.className = 'help-block';
                    barcodeNote.innerHTML = '<small><i class="fa fa-info-circle"></i> Supports most common barcode formats including EAN, UPC, Code 39, Code 128</small>';
                    barcodeNote.style.marginTop = '5px';
                    barcodeNote.style.color = '#777';
                    manualInputContainer.appendChild(barcodeNote);

                    // Show success message with improved guidance and browser-specific advice
                    const browserInfo = detectBrowser();
                    let browserAdvice = '';

                    if (browserInfo.name) {
                        if (browserInfo.name === 'IE' || browserInfo.name === 'Edge' && browserInfo.version < 79) {
                            browserAdvice = 'You\'re using ' + browserInfo.name + ' which has limited camera support. We recommend switching to Chrome, Firefox, or the new Edge browser.';
                        } else if (browserInfo.name === 'Safari' && browserInfo.version < 13) {
                            browserAdvice = 'You\'re using an older version of Safari. Please update to Safari 13+ or try Chrome/Firefox for better camera support.';
                        } else {
                            browserAdvice = 'Your browser (' + browserInfo.name + ') may need additional permissions or settings to access the camera properly.';
                        }
                    }

                    updateScannerResult(
                        '<div class="alert alert-warning">' +
                        '<h4><i class="fa fa-exclamation-triangle"></i> Compatibility Mode Detected</h4>' +
                        '<p>Your browser is using a compatibility mode that doesn\'t support automatic barcode scanning.</p>' +
                        (browserAdvice ? '<p><strong>' + browserAdvice + '</strong></p>' : '') +
                        '<hr>' +
                        '<p><strong>Please use the manual barcode entry below:</strong></p>' +
                        '</div>'
                    );

                    // Add the manual input container to the scanner result
                    if ($('#scanner-result').length > 0) {
                        $('#scanner-result').append(manualInputContainer);
                    }

                    // Create a container for the buttons with improved styling
                    const buttonContainer = document.createElement('div');
                    buttonContainer.className = 'button-container';
                    buttonContainer.style.marginTop = '20px';
                    buttonContainer.style.display = 'flex';
                    buttonContainer.style.justifyContent = 'space-between';
                    buttonContainer.style.alignItems = 'center';

                    // Create a button to close the camera
                    const closeButton = document.createElement('button');
                    closeButton.className = 'btn btn-danger';
                    closeButton.innerHTML = '<i class="fa fa-times"></i> Close Camera';
                    closeButton.style.marginRight = '10px';
                    closeButton.onclick = function() {
                        stream.getTracks().forEach(track => track.stop());
                        $('#camera_barcode_modal').modal('hide');
                    };

                    // Create a button to switch to a different browser based on current browser
                    // Use the browserInfo variable that was already declared above
                    const switchBrowserButton = document.createElement('a');
                    switchBrowserButton.className = 'btn btn-default';

                    // Customize the recommendation based on the detected browser
                    let recommendedBrowser = 'Chrome';
                    let browserUrl = 'https://www.google.com/chrome/';

                    if (browserInfo.name === 'Chrome') {
                        recommendedBrowser = 'Firefox';
                        browserUrl = 'https://www.mozilla.org/firefox/';
                    } else if (browserInfo.name === 'Firefox') {
                        recommendedBrowser = 'Chrome';
                        browserUrl = 'https://www.google.com/chrome/';
                    } else if (browserInfo.name === 'Safari') {
                        recommendedBrowser = 'Chrome';
                        browserUrl = 'https://www.google.com/chrome/';
                    } else if (browserInfo.name === 'Edge' && browserInfo.version < 79) {
                        recommendedBrowser = 'New Edge';
                        browserUrl = 'https://www.microsoft.com/edge';
                    } else if (browserInfo.name === 'IE') {
                        recommendedBrowser = 'Chrome or Edge';
                        browserUrl = 'https://www.google.com/chrome/';
                    }

                    switchBrowserButton.innerHTML = '<i class="fa fa-external-link"></i> Get ' + recommendedBrowser;
                    switchBrowserButton.href = browserUrl;
                    switchBrowserButton.target = '_blank';
                    switchBrowserButton.rel = 'noopener noreferrer';

                    // Add buttons to the container
                    buttonContainer.appendChild(closeButton);
                    buttonContainer.appendChild(switchBrowserButton);

                    // Safely append the button container to the scanner result element
                    if ($('#scanner-result').length > 0) {
                        $('#scanner-result').append(buttonContainer);

                        // Focus on the manual input field
                        setTimeout(function() {
                            $('#manual-barcode-input').focus();
                        }, 500);
                    } else {
                        console.error('Scanner result element not found in the DOM');
                    }
                })
                .catch(function(error) {
                    console.error("Fallback camera access failed:", error);
                    updateScannerResult('<div class="alert alert-danger">Could not access camera: ' + error.message + '. Please check your camera permissions and try again.</div>');
                });
        } else {
            updateScannerResult('<div class="alert alert-danger">Your browser does not support camera access. Please use a modern browser like Chrome, Firefox, Safari, or Edge.</div>');
        }
    } catch (error) {
        console.error("Error in fallback camera access:", error);
        updateScannerResult('<div class="alert alert-danger">Error accessing camera: ' + error.message + '. Please try using a different browser.</div>');
    }
}

/**
 * Update the debug information panel
 * @param {string} message - The message to add to the debug panel
 */
function updateDebugInfo(message) {
    if (!debugMode) return;

    const timestamp = new Date().toLocaleTimeString();
    const debugInfo = $('#debug-info');

    // Add the message to the debug panel
    debugInfo.prepend('<div><strong>' + timestamp + ':</strong> ' + message + '</div>');

    // Limit the number of messages to 20
    if (debugInfo.children().length > 20) {
        debugInfo.children().last().remove();
    }
}

/**
 * Add a visual indicator for the scanning area
 */
function addScanningAreaIndicator() {
    // Remove any existing indicator
    $('.scanning-area-indicator').remove();

    // Create the scanning area indicator
    const indicator = $('<div class="scanning-area-indicator"></div>');

    // Style the indicator
    indicator.css({
        'position': 'absolute',
        'top': '50%',
        'left': '50%',
        'transform': 'translate(-50%, -50%)',
        'width': '300px',
        'height': '150px',
        'border': '2px dashed #fff',
        'border-radius': '10px',
        'box-shadow': '0 0 0 2000px rgba(0, 0, 0, 0.3)',
        'z-index': '10',
        'pointer-events': 'none'
    });

    // Add a label to guide the user
    const label = $('<div class="scanning-label">Position barcode here</div>');
    label.css({
        'position': 'absolute',
        'top': '-30px',
        'left': '50%',
        'transform': 'translateX(-50%)',
        'color': '#fff',
        'background-color': 'rgba(0, 0, 0, 0.7)',
        'padding': '5px 10px',
        'border-radius': '5px',
        'font-size': '12px'
    });

    // Add the label to the indicator
    indicator.append(label);

    // Add the indicator to the scanner container
    $('#scanner-container').css('position', 'relative').append(indicator);

    // Add some animation to draw attention to the scanning area
    animateScanningIndicator();
}

/**
 * Animate the scanning area indicator
 */
function animateScanningIndicator() {
    // Add a scanning line that moves up and down
    const scanLine = $('<div class="scan-line"></div>');
    scanLine.css({
        'position': 'absolute',
        'top': '0',
        'left': '10px',
        'right': '10px',
        'height': '2px',
        'background-color': 'rgba(76, 175, 80, 0.8)',
        'z-index': '11'
    });

    $('.scanning-area-indicator').append(scanLine);

    // Animate the scan line
    function animateLine() {
        scanLine.animate({
            top: '100%'
        }, 2000, function() {
            scanLine.css('top', '0');
            animateLine();
        });
    }

    animateLine();
}

/**
 * Play a beep sound to indicate successful scan
 */
function playBeepSound() {
    // Create an audio element for the beep sound
    var audio = new Audio('data:audio/wav;base64,UklGRl9vT19XQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YU...'); // Base64 encoded short beep sound
    audio.volume = 0.5;
    audio.play();
}

/**
 * Detect the user's browser and version
 * @returns {Object} An object containing the browser name and version
 */
function detectBrowser() {
    const userAgent = navigator.userAgent;
    let browserName = '';
    let browserVersion = '';

    // Detect browser name
    if (userAgent.indexOf("Firefox") > -1) {
        browserName = "Firefox";
    } else if (userAgent.indexOf("SamsungBrowser") > -1) {
        browserName = "Samsung Browser";
    } else if (userAgent.indexOf("Opera") > -1 || userAgent.indexOf("OPR") > -1) {
        browserName = "Opera";
    } else if (userAgent.indexOf("Trident") > -1 || userAgent.indexOf("MSIE") > -1) {
        browserName = "IE";
    } else if (userAgent.indexOf("Edge") > -1) {
        browserName = "Edge";
    } else if (userAgent.indexOf("Chrome") > -1) {
        browserName = "Chrome";
    } else if (userAgent.indexOf("Safari") > -1) {
        browserName = "Safari";
    }

    // Detect browser version
    let versionMatch;
    if (browserName === "Firefox") {
        versionMatch = userAgent.match(/Firefox\/([0-9.]+)/);
    } else if (browserName === "Samsung Browser") {
        versionMatch = userAgent.match(/SamsungBrowser\/([0-9.]+)/);
    } else if (browserName === "Opera") {
        versionMatch = userAgent.match(/(?:Opera|OPR)\/([0-9.]+)/);
    } else if (browserName === "IE") {
        versionMatch = userAgent.match(/(?:MSIE |rv:)([0-9.]+)/);
    } else if (browserName === "Edge") {
        versionMatch = userAgent.match(/Edge\/([0-9.]+)/);
        if (!versionMatch) {
            // New Edge (Chromium-based)
            versionMatch = userAgent.match(/Edg\/([0-9.]+)/);
        }
    } else if (browserName === "Chrome") {
        versionMatch = userAgent.match(/Chrome\/([0-9.]+)/);
    } else if (browserName === "Safari") {
        versionMatch = userAgent.match(/Version\/([0-9.]+)/);
    }

    if (versionMatch && versionMatch.length > 1) {
        browserVersion = parseFloat(versionMatch[1]);
    }

    return {
        name: browserName,
        version: browserVersion
    };
}
