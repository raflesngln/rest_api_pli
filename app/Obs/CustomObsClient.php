<?php
namespace App\Obs;
use Obs\ObsClient;
use App\Obs\Internal\Common\ExtendedCheckoutStream;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\StreamInterface;

class CustomObsClient extends ObsClient
{
    public function putObject(array $params = []): array
    {
        if (isset($params['SourceFile'])) {
            $sourceFile = $params['SourceFile'];
            $stream = $this->createExtendedStream($sourceFile);
            $params['Body'] = $stream;
        }

        return parent::putObject($params);
    }

    private function createExtendedStream(string $sourceFile): StreamInterface
    {
        $fileStream = Utils::streamFor(fopen($sourceFile, 'r'));
        $expectedLength = $fileStream->getSize();
        $extendedStream = new ExtendedCheckoutStream($fileStream, $expectedLength);
        return $extendedStream;
    }
}