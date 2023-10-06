<?php

namespace App\tests\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FormationsControllerTest extends WebTestCase
{
    public function testAccesPage()
    {
        $client = static::createClient();
        $client->catchExceptions(false);
        $client->request('GET', '/formations');
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatuscode());
    }

    public function testContenuPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formations');
        $this->assertSelectorTextContains('th', 'formation');
        $this->assertCount(5, $crawler->filter('th'));
    }

    public function testLinkFormation()
    {
        $client = static::createClient();
        $crawler =  $client->request('GET', '/formations');
        //click sur un lien ( l'image de la première formation)
        $link = $crawler->filter('a')->eq(9)->attr('href');
        $crawler = $client->request('GET',$link);
        // récupération du résultat du clic
        $response = $client->getResponse();
        //control si le lien existe
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        //récupération de la route et controle qu'elle est correcte
        $uri = $client->getRequest()->server->get("REQUEST_URI");
        $this->assertEquals('/formations/formation/1', $uri); 
    }

    public function testFiltreFormation()
    {
        $client = static::createClient();
        $client->request('GET', '/formations');
        //simulation de la soumission du formulaire
        $crawler = $client->submitForm('filtrer', [
            'recherche' => 'Eclipse n°3 : GitHub et Eclipse'
        ]);
        // vérifie le nombre de lignes obtenues
        $this->assertCount(1, $crawler->filter('h5'));
        // vérifie si la formation correspond à la recherche
        $this->assertSelectorTextContains('h5', 'Eclipse n°3 : GitHub et Eclipse');
    }
    
    public function testSortFormation()
    {
        $client = static::createClient();
        $client->request('GET', '/formations/tri/title/DESC');
        $this->assertSelectorTextContains('h5', 'UML : Diagramme de paquetages');
        $client->request('GET', '/formations/tri/name/DESC/playlist');
        $this->assertSelectorTextContains('h5', 'C# : ListBox en couleur');
        $client->request('GET', '/formations/tri/publishedAt/ASC');
        $this->assertSelectorTextContains('h5', 'Cours UML (1 à 7 / 33) : introduction et cas d\'utilisation');
    }
}