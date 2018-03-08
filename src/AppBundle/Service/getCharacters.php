<?php

namespace AppBundle\Service;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Response;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

use AppBundle\DataWrapper\AbstractDataWrapper;
use AppBundle\DataWrapper\CharacterDataWrapper;

class getCharacters
{

    /**
     * @var apiKey
     */
    protected $_apiKey;
    
    /**
     * @var privateKey
     */
    protected $_privateKey;

    public $httpClient;

    public $url = 'https://gateway.marvel.com:443/v1/public/characters';

    /**
     * @param ProductRepository $productRepository
     */
    public function __construct()
    {
        $this->_apikey = '4743737a0dfc65da76d3d7be068e19c3';
        $this->_privatekey = '7379e66405384ed5d549ac22b1b52af48cf025a4';
    }

    /**
     * @return Chatacters[]
     */
    public function __invoke(array $params = array())
    {
        $client = new GuzzleClient();
        $query = [
            'ts' => time(),
            'apikey' => $this->_apikey,
            'hash' => md5(time() . $this->_privatekey . $this->_apikey),
        ];
        foreach (array_filter($params) as $key => $value) {
            $query[$key] = $value;
        }
        try {
            $response = $client->request('GET', $this->url, ['query' => $query]);
            $serializer = SerializerBuilder::create()
                ->addMetadataDir(__DIR__ . '/../Serializer', 'AppBundle')
                ->build();

            
            return $serializer->deserialize($response->getBody()->getContents(), CharacterDataWrapper::class, 'json');
        } catch (ClientException $e) {
            if ($e->hasResponse()) {
                throw new ClientException($e->getMessage(), $e->getRequest());
            }
        }


        return json_decode($this->response->getBody(), true);

    }
}