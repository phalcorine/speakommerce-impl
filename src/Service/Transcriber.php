<?php


namespace App\Service;


use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Aws\TranscribeService\TranscribeServiceClient;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Transcriber
{
    const AUDIO_FILE_PATH = "audio_files_directory";
    const ACCESS_KEY = "aws_client_access_key";
    const SECRET_ACCESS_KEY = "aws_client_secret_access_key";
    const S3_BUCKET_NAME = "aws_client_s3_bucket_name";
    const S3_REGION_CODE = "aws_client_s3_bucket_region_code";
    const TRANSCRIBE_REGION_CODE = "aws_client_transcribe_region_code";

    private $logger;
    private $httpClient;
    private $parameterBag;

    public function __construct(
        LoggerInterface $logger,
        HttpClientInterface $httpClient,
        ParameterBagInterface $parameterBag)
    {
        $this->logger = $logger;
        $this->httpClient = $httpClient;
        $this->parameterBag = $parameterBag;
    }

    /**
     * @param string $filename
     * @return string
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function transcribe(string $filename)
    {
        $audioFullPath = $this->getAudioFileFullPath($filename);
        $bucketName = $this->getS3BucketName();
        $s3Client = $this->getS3Client();

        try {

            $result = $s3Client->putObject([
                'Bucket'    => $bucketName,
                'Key'       => $filename,
                'Body'      => fopen($audioFullPath, 'r'),
                'ACL'       => 'public-read'
            ]);

            $audioUrl = $result->get('ObjectURL');

            $transcribeServiceClient = $this->getTranscribeServiceClient();

            $transcriptionId = uniqid('sgt-trans-');

            $transcriptionResult = $transcribeServiceClient->startTranscriptionJob([
                'LanguageCode'  => 'en-US',
                'Media' => [
                    'MediaFileUri'  => $audioUrl
                ],
                'TranscriptionJobName'  => $transcriptionId
            ]);

            $transcriptionStatus = [];

            while (true)
            {
                $transcriptionStatus = $transcribeServiceClient->getTranscriptionJob([
                    'TranscriptionJobName'  => $transcriptionId
                ]);

                if($transcriptionStatus->get('TranscriptionJob')['TranscriptionJobStatus'] == 'COMPLETED')
                    break;

                sleep(5);
            }

            /** @var string $transcriptionJobUrl */
            $transcriptionJobUrl = $transcriptionStatus->get('TranscriptionJob')['Transcript']['TranscriptFileUri'];

            $httpResponse = $this->httpClient->request(
                'GET',
                $transcriptionJobUrl
            );

            $responseContent = json_decode($httpResponse->getContent());

            /** @var string $transcript */
            $transcript = $responseContent->results->transcripts[0]->transcript;

            return $transcript;


        } catch (S3Exception $exception)
        {
            $this->logger->critical($exception->getAwsErrorCode());
            $this->logger->critical($exception->getAwsErrorMessage());
            $this->logger->critical($exception->getMessage());

            throw new \LogicException($exception);
        }
    }

    /**
     * @return S3Client
     */
    private function getS3Client()
    {
        $awsAccessKey = $this->parameterBag->get(self::ACCESS_KEY);
        $awsSecretAccessKey = $this->parameterBag->get(self::SECRET_ACCESS_KEY);
        $s3Client = new S3Client([
            'region'    => $this->getS3BucketRegionCode(),
            'version'   => 'latest',
            'credentials'   => [
                'key'       => $awsAccessKey,
                'secret'    => $awsSecretAccessKey
            ]
        ]);

        return $s3Client;
    }

    private function getTranscribeServiceClient()
    {
        $awsAccessKey = $this->parameterBag->get(self::ACCESS_KEY);
        $awsSecretAccessKey = $this->parameterBag->get(self::SECRET_ACCESS_KEY);
        $client = new TranscribeServiceClient([
            'region'        => $this->getTranscribeRegionCode(),
            'version'       => 'latest',
            'credentials'   => [
                'key'       => $awsAccessKey,
                'secret'    => $awsSecretAccessKey
            ]
        ]);

        return $client;
    }

    /**
     * @return string
     */
    private function getS3BucketName()
    {
        return $this->parameterBag->get(self::S3_BUCKET_NAME);
    }

    /**
     * @return string
     */
    private function getS3BucketRegionCode()
    {
        return $this->parameterBag->get(self::S3_REGION_CODE);
    }

    /**
     * @return string
     */
    private function getTranscribeRegionCode()
    {
        return $this->parameterBag->get(self::TRANSCRIBE_REGION_CODE);
    }

    /**
     * @param string $filename
     * @return string
     */
    private function getAudioFileFullPath(string $filename)
    {
        $audioDirectoryPath = $this->parameterBag->get(self::AUDIO_FILE_PATH);

        $realpath = $audioDirectoryPath . "/" . $filename;

        return $realpath;
    }
}