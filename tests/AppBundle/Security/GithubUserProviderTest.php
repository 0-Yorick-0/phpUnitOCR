<?php

namespace Tests\AppBundle\Security;

use PHPUnit\Framework\TestCase;

use AppBundle\Security\GithubUserProvider;
use AppBundle\Entity\User;

class GithubUserProviderTest extends TestCase

{

    public function testLoadUserByUsernameReturningAUser()

    {
    	$client = $this->getMockBuilder('GuzzleHttp\Client')
    		->disableOriginalConstructor()
    		->setMethods(['get']) //Nous indiquons qu'une méthode va être redéfinie.
    		->getMock()
    	;

    	$serialiser = $this->getMockBuilder('JMS\Serializer\Serializer')
    		->disableOriginalConstructor()
    		->setMethods(['deserialize'])
    		->getMock()
    	;

    	$response = $this->getMockBuilder('Psr\Http\Message\ResponseInterface')
    		// ->setMethods(['getBody']) pas besoin de redéfinir une méthode issue d'une interface
    		->getMock()
    	;

    	$streamedResponse = $this->getMockBuilder('Psr\Http\Message\StreamInterface')
    		->getMock()
    	;

    	$userData = [
    		'login' => 'a login', 
    		'name' => 'user name', 
    		'email' => 'adress@mail.com', 
    		'avatar_url' => 'url to the avatar', 
    		'html_url' => 'url to profile'
    	];

    	$client
    		->expects($this->once()) //la méthode ne doît être appelée qu'une seule fois
    		->method('get')
    		->willReturn($response)
    	;
    	$response
    		->expects($this->once())
    		->method('getBody')
    		->willReturn($streamedResponse)
    	;
    	$serialiser
    		->expects($this->once())
    		->method('deserialize')
    		->willReturn($userData)
    	;

    	$githubUserProvider = new GithubUserProvider($client, $serialiser);

    	$user = $githubUserProvider->loadUserByUsername('some-token');

    	$expectedUser = new User($userData['login'], $userData['name'], $userData['email'], $userData['avatar_url'], $userData['html_url']);

    	$this->assertEquals($expectedUser, $user);
    	$this->assertEquals('AppBundle\Entity\User', get_class($user));

    }

}