<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Controller\SecurityController as userBase;
use AppBundle\Service\getCharacters;


use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Response;
use AppBundle\DataWrapper\CharacterDataWrapper;
use JMS\Serializer\SerializerBuilder;
class DefaultController extends userBase
{
    /**
     * @var string
     */
    private $baseUrl = 'http://gateway.marvel.com/v1/public/';

    /**
     * @var string
     */
    private $publicApiKey;

    /**
     * @var string
     */
    private $privateApiKey;

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request, AuthorizationCheckerInterface $authorizationChecker, getCharacters $getCharacters)
    {
        // get the login error if there is one
        if ($authorizationChecker->isGranted('ROLE_ADMIN')===false && $authorizationChecker->isGranted('ROLE_USER')===false) {
            return $this->redirect('/login', 301);
        } else if ($authorizationChecker->isGranted('ROLE_USER')===true) {
            $characterFilter = array();
            $response = $this->call('characters', $characterFilter);
            $formattedResponse = $this->formatResponse($response, '..\Entity\Character');
            //$characters = $this->get(AppBundle\Service\getCharacters::class)->__invoke();
            return $this->render('default/index.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            ]);    
        } else {
            return $this->redirect('/admin', 301);
        }
    }


    /**
     * @param string $operation
     * @param object|null $query
     *
     * @return Response
     */
    private function call(string $operation, $query = null) : Response
    {
        $url = $this->baseUrl . $operation;
        $params = array();
        if (!empty($query)) {
            $params = get_object_vars($query);
        }
        return $this->send($url, $params);
    }
    /**
     * @param string $url
     * @param array $params
     *
     * @return Response
     */
    private function send(string $url, array $params = array()) : Response
    {
        $this->publicApiKey = '4743737a0dfc65da76d3d7be068e19c3';
        $this->privateApiKey = '7379e66405384ed5d549ac22b1b52af48cf025a4';

        $client = new GuzzleClient();
        $query = [
            'ts' => time(),
            'apikey' => $this->publicApiKey,
            'hash' => md5(time() . $this->privateApiKey . $this->publicApiKey),
        ];
        foreach (array_filter($params) as $key => $value) {
            $query[$key] = $value;
        }
        try {
            return $client->request('GET', $url, ['query' => $query]);
        } catch (ClientException $e) {
            if ($e->hasResponse()) {
                throw new ClientException($e->getMessage(), $e->getRequest());
            }
        }
    }
    /**
     * @param Response $response
     * @param string $dataWrapper
     *
     * @return Object
     */
    private function formatResponse(Response $response, string $dataWrapper)
    {
        $serializer = SerializerBuilder::create()
            ->addMetadataDir(__DIR__ . '/../Serializer', 'AppBundle')
            ->build();
        return $serializer->deserialize($response->getBody()->getContents(), $dataWrapper, 'json');
    }
}
