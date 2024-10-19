<?php

class ImageController
{
    public function loadImage($filename)
    {
        $path = __DIR__ . '/../icons/' . $filename;

        if (file_exists($path)) {
            // Set appropriate headers
            header('Content-Type: ' . mime_content_type($path));
            header('Content-Length: ' . filesize($path));

            // Read the file and send it to the output buffer
            readfile($path);
            exit;
        } else {
            http_response_code(404);
            echo "Image not found!";
        }
    }
}

// If filename is passed via GET, serve the image
if (isset($_GET['image'])) {
    $controller = new ImageController();
    $controller->loadImage($_GET['image']);
}
