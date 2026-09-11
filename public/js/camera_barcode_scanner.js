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
        // Clear history list on fresh open
        $('#scan-history-list').html('<li class="scan-history-empty text-muted">No items scanned yet.</li>');
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

    // Improved configuration for maximum speed and format support
    const config = {
        fps: 30, // Maximize frames per second for instant scanning
        disableFlip: false, // Ensure upside-down barcodes are still scanned
        experimentalFeatures: {
            useBarCodeDetectorIfSupported: true // Uses native hardware acceleration (blazing fast) when available
        },
        verbose: false // Disable verbose logging to free up CPU resources
    };

    updateScannerResult('<div class="scanner-status"><i class="fa fa-spinner fa-spin"></i> <span>Starting camera...</span></div>');

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
                updateScannerResult('<div class="scanner-status"><i class="fa fa-camera"></i> <span>Scanning for barcodes...</span></div>');
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
                updateScannerResult('<div class="scanner-status"><i class="fa fa-camera"></i> <span>Scanning for barcodes...</span></div>');
            }).catch(err => {
                console.error("Error starting camera (fallback):", err);
                updateScannerResult('<div class="alert alert-danger">Error starting scanner: ' + err + '. Please try a different camera or refresh the page.</div>');
            });
        });
}

let lastScannedBarcode = null;
let lastScanTime = 0;

/**
 * Handle successful barcode scan
 * 
 * @param {string} decodedText - The decoded barcode text
 */
function onScanSuccess(decodedText, decodedResult) {
    const now = Date.now();
    // Prevent duplicate scans of the same barcode within 2 seconds
    if (decodedText === lastScannedBarcode && (now - lastScanTime) < 2000) {
        return;
    }
    lastScannedBarcode = decodedText;
    lastScanTime = now;

    // Log the successful scan for debugging
    console.log("Barcode successfully scanned:", decodedText);
    console.log("Scan result details:", decodedResult);

    // Update debug info
    updateDebugInfo("Barcode detected: " + decodedText + " (Format: " + 
        (decodedResult.result.format ? decodedResult.result.format.formatName : 'Unknown') + ")");

    // Update the result display
    updateScannerResult('<div class="scanner-status success">' +
        '<i class="fa fa-check-circle"></i> <span><strong>Scanned:</strong> ' + decodedText + '</span>' +
        '</div>');

    // Add to session history list
    $('#scan-history-list .scan-history-empty').remove();
    const timeString = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', second:'2-digit'});
    $('#scan-history-list').prepend('<li><i class="fa fa-barcode"></i> <div><strong>' + decodedText + '</strong><br><small class="text-muted" style="font-size: 0.8rem;">' + timeString + '</small></div></li>');

    // Flash the scanner container to provide visual feedback
    $('#scanner-container').css('border', '3px solid #5cb85c');
    setTimeout(function() {
        $('#scanner-container').css('border', '');
    }, 500);

    // Set the barcode in the search field
    const $searchProduct = $('#search_product');
    $searchProduct.val(decodedText);
    updateDebugInfo('Setting search field value to: ' + decodedText);

    // Directly trigger the autocomplete search if the widget is initialized
    if ($searchProduct.data('ui-autocomplete')) {
        $searchProduct.autocomplete('search');
        updateDebugInfo('Triggered autocomplete search directly');
    } else {
        // Fallback if autocomplete is somehow not initialized
        var e = $.Event('keydown');
        e.which = 13;
        $searchProduct.trigger(e);
        updateDebugInfo('Triggered keydown Enter event as fallback');
    }

    // Play a beep sound to indicate successful scan
    playBeepSound();
}

/**
 * Handle scan failure
 * 
 * @param {string} error - The error message
 */
function onScanFailure(error) {
    // We don't need to show errors as they happen frequently during scanning (15 times a second)
    // We'll ignore the standard "No barcode found in this frame" errors to prevent flooding
    if (!error) return;
    
    const errorStr = error.toString();
    const isStandardParseError = errorStr.includes('QR code parse error') || 
                                 errorStr.includes('No MultiFormat Readers') ||
                                 errorStr.includes('No barcode or QR code detected');

    if (!isStandardParseError) {
        console.debug('Scan error:', error);
        updateDebugInfo('Scan error: ' + error);
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
    console.log("Trying fallback camera access method using facingMode");

    updateScannerResult('<div class="alert alert-info">Trying alternative method to access your camera... Please allow camera access if prompted.</div>');

    // Add visual indicator for the scanning area
    addScanningAreaIndicator();

    const config = {
        fps: 30, // Maximize frames per second
        disableFlip: false, 
        experimentalFeatures: {
            useBarCodeDetectorIfSupported: true
        },
        verbose: false // Disable verbose logging to free up CPU resources
    };

    html5QrCode.start(
        { facingMode: "environment" },
        config,
        onScanSuccess,
        onScanFailure
    ).then(() => {
        console.log("Fallback camera started successfully");
        isScanning = true;
        updateScannerResult('<div class="scanner-status"><i class="fa fa-camera"></i> <span>Scanning for barcodes...</span></div>');
    }).catch(err => {
        console.error("Error starting camera with environment facing mode:", err);
        // If environment fails, try generic camera
        html5QrCode.start(
            { facingMode: "user" },
            config,
            onScanSuccess,
            onScanFailure
        ).then(() => {
            console.log("Fallback camera started successfully (user)");
            isScanning = true;
            updateScannerResult('<div class="scanner-status"><i class="fa fa-camera"></i> <span>Scanning for barcodes...</span></div>');
        }).catch(err2 => {
            console.error("Error starting camera with user facing mode:", err2);
            updateScannerResult('<div class="alert alert-danger">Error accessing camera: ' + err2 + '. Please check permissions and try again.</div>');
        });
    });
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
