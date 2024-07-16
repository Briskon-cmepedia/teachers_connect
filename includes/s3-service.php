<?php

use Aws\S3\S3Client;
use GuzzleHttp\Psr7\Stream;


class S3Service
{
    private $client;

    public function __construct()
    {
        $this->client = new S3Client([
            'version' => Config::AWS_VERSION,
            'region' => Config::AWS_REGION,
            'endpoint' => Config::AWS_ENDPOINT,
            'credentials' => [
                'key' => Config::ACCESS_KEY,
                'secret' => Config::SECRET_KEY,
            ],
        ]);
    }

    public function getImageStream(string $fileName): ?Stream
    {
        try {
            $result = $this->client->getObject([
                'Bucket' => Config::BUCKET,
                'Key' => $fileName

            ]);
            return $result->get('Body');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function uploadImage(string $sourceFileName, string $destinationFileName, bool $isPublicAccess = false)
    {
        $options = [
            'Bucket' => Config::BUCKET,
            'Key' => $destinationFileName,
            'SourceFile' => $sourceFileName,
        ];
        if ($isPublicAccess) {
            $options['ACL'] = 'public-read';
        }
        return $this->client->putObject($options);
    }

    public function doesImageExist(string $fileName): bool
    {
        return $this->client->doesObjectExist(Config::BUCKET, $fileName);
    }
}