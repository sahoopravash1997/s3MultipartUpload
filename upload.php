<?php
use Aws\Common\Exception\MultipartUploadException;
use Aws\S3\MultipartUploader;
use Aws\S3\S3Client;
require "./vendor/autoload.php";
require "./credentials.php";
$respenseArr = [];
try {
    $s3 = new S3Client(
        array(
            'credentials' => array(
                'key' => $credentials["AWS_ACCESS_KEY"],
                'secret' => $credentials["AWS_SECRET_KEY"]
            ),
            'version' => 'latest',
            'region'  => $credentials["AWS_REGION"]
        )
    );
} catch (Exception $e) {
    // die("Error: " . $e->getMessage());
    $respenseArr["S3"]["Error"] = $e->getMessage();
}

if(isset($_FILES["file"]["tmp_name"]))
{

    $keyname = strtolower($_FILES["file"]["name"]);
    $keyname = str_replace(["_", "\t", "\n\r", "\n", "\\", "\/", "(", ")", "{", "}", "[", "]"], " ", $keyname);
    $keyname = trim(str_replace(" ", "_", strtolower(preg_replace('!\s+!', ' ',  $keyname))));
    $filePath = $_FILES["file"]["tmp_name"];

    // Prepare the upload parameters.
    $uploader = new MultipartUploader($s3, $filePath, [
        'bucket' => $credentials["AWS_BUCKET_NAME"],
        'key'    => $keyname
    ]);

    // Perform the upload.
    try {
        $result = $uploader->upload();
        echo "Upload complete: {$result['ObjectURL']}" . PHP_EOL;
    } catch (MultipartUploadException $e) {
        echo $e->getMessage() . PHP_EOL;
    }
}
else
{
    echo "Plese Choose A Valid File To Upload.";
}
exit();