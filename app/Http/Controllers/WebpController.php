<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class WebpController extends Controller
{
    /**
     * Convert and serve an image in WebP format
     *
     * @param Request $request
     * @param string $path The path to the image
     * @return \Illuminate\Http\Response
     */
    public function convert(Request $request, $path)
    {
        // Reconstruct the full path from the URL parameters
        $fullPath = public_path($path);
        
        // Check if the original file exists
        if (!File::exists($fullPath)) {
            abort(404, 'Image not found');
        }
        
        // Get the file extension
        $extension = pathinfo($fullPath, PATHINFO_EXTENSION);
        
        // Create the WebP filename
        $webpPath = public_path('uploads/webp/' . md5($path) . '.webp');
        
        // Create the directory if it doesn't exist
        $webpDir = public_path('uploads/webp');
        if (!File::exists($webpDir)) {
            File::makeDirectory($webpDir, 0755, true);
        }
        
        // Check if the WebP version already exists and is newer than the original
        if (File::exists($webpPath) && filemtime($webpPath) >= filemtime($fullPath)) {
            return $this->serveWebp($webpPath);
        }
        
        // Convert the image to WebP based on its type
        switch (strtolower($extension)) {
            case 'jpg':
            case 'jpeg':
                $image = imagecreatefromjpeg($fullPath);
                break;
            case 'png':
                $image = imagecreatefrompng($fullPath);
                // Handle transparency
                imagepalettetotruecolor($image);
                imagealphablending($image, true);
                imagesavealpha($image, true);
                break;
            case 'gif':
                $image = imagecreatefromgif($fullPath);
                break;
            default:
                // For unsupported formats, return the original image
                return Response::file($fullPath);
        }
        
        // Save the image as WebP
        imagewebp($image, $webpPath, 80); // 80 is the quality (0-100)
        imagedestroy($image);
        
        return $this->serveWebp($webpPath);
    }
    
    /**
     * Serve a WebP image with appropriate headers
     *
     * @param string $webpPath Path to the WebP image
     * @return \Illuminate\Http\Response
     */
    private function serveWebp($webpPath)
    {
        $headers = [
            'Content-Type' => 'image/webp',
            'Cache-Control' => 'public, max-age=31536000', // Cache for 1 year
            'Expires' => gmdate('D, d M Y H:i:s \G\M\T', time() + 31536000),
        ];
        
        return Response::file($webpPath, $headers);
    }
}