<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProcessProductImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120;
    public $tries = 3;

    public function __construct(
        public int $productId,
        public string $tempPath
    ) {}

    public function handle(): void
    {
        try {
            $filename = time() . '_' . uniqid() . '.jpg';
            $path = 'products/' . $filename;
            
            $fullTempPath = storage_path('app/public/' . $this->tempPath);
            
            if (!file_exists($fullTempPath)) {
                Log::error("Temp file not found: {$fullTempPath}");
                return;
            }
            
            $extension = pathinfo($fullTempPath, PATHINFO_EXTENSION);
            
            // Load and convert image
            $sourceImage = $this->loadImage($fullTempPath, $extension);
            
            if (!$sourceImage) {
                Log::error("Failed to load image: {$fullTempPath}");
                Storage::disk('public')->delete($this->tempPath);
                return;
            }
            
            // Resize if needed
            $width = imagesx($sourceImage);
            $height = imagesy($sourceImage);
            
            if ($width > 1200) {
                $newWidth = 1200;
                $newHeight = (int)(($height / $width) * $newWidth);
                $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagedestroy($sourceImage);
                $sourceImage = $resizedImage;
            }
            
            // Save as JPG
            $fullPath = storage_path('app/public/' . $path);
            $directory = dirname($fullPath);
            
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
            
            imagejpeg($sourceImage, $fullPath, 85);
            imagedestroy($sourceImage);
            
            // Create database record
            ProductImage::create([
                'product_id' => $this->productId,
                'image_url' => $path
            ]);
            
            // Delete temp file
            Storage::disk('public')->delete($this->tempPath);
            
            Log::info("Image processed successfully: {$path}");
            
        } catch (\Exception $e) {
            Log::error("Image processing failed: " . $e->getMessage());
            // Clean up temp file on error
            Storage::disk('public')->delete($this->tempPath);
            throw $e;
        }
    }

    private function loadImage($filePath, $extension)
    {
        $extension = strtolower($extension);
        
        try {
            switch ($extension) {
                case 'png':
                    $image = imagecreatefrompng($filePath);
                    if (!$image) return false;
                    
                    // Handle transparency for PNG
                    $width = imagesx($image);
                    $height = imagesy($image);
                    $newImage = imagecreatetruecolor($width, $height);
                    $white = imagecolorallocate($newImage, 255, 255, 255);
                    imagefill($newImage, 0, 0, $white);
                    imagecopy($newImage, $image, 0, 0, 0, 0, $width, $height);
                    imagedestroy($image);
                    return $newImage;
                    
                case 'jpg':
                case 'jpeg':
                    return imagecreatefromjpeg($filePath);
                    
                default:
                    return imagecreatefromjpeg($filePath);
            }
        } catch (\Exception $e) {
            Log::error("Failed to load image: " . $e->getMessage());
            return false;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Job failed after {$this->tries} attempts: " . $exception->getMessage());
        // Clean up temp file on final failure
        Storage::disk('public')->delete($this->tempPath);
    }
}