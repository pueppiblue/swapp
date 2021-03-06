<?php

namespace Tests\AppBundle\EdgeToEdge;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageUploadTest extends WebTestCase
{
    /**
     * @return array
     */
    public function testNavigateToCreateWaypointForm()
    {
        $this->loadAllFixtures();
        $client = $this->createAuthenticatedClient();

        $crawler = $client->request('GET', '/walks');
        $crawler = $crawler->selectLink('Runde beginnen');
        $crawler = $client->click($crawler->link());
        $this->isSuccessful($client->getResponse());

        $form = $crawler->selectButton('Wegpunkt anlegen')->form(
            [
                'app_create_walk_prologue[name]' => 'name test',
                'app_create_walk_prologue[conceptOfDay]' => 'conceptOfDay test',
            ]
        );

        $crawler = $client->submit($form);
        $this->isSuccessful($client->getResponse());

        return [$client, $crawler];
    }

    /**
     * @depends testNavigateToCreateWaypointForm
     * @param array $args
     */
    public function testSubmittingEmptyFormRedirectsToForm(array $args)
    {
        /**@var Client $client */
        $client = $args[0];
        /**@var Crawler $crawler */
        $crawler = $args[1];

        $form = $crawler->selectButton('speichern')->form();
        $crawler = $client->submit($form);
        $this->isSuccessful($client->getResponse());

        $form = $crawler->selectButton('speichern')->form();
        $this->assertArrayHasKey('app_create_way_point', $form->getPhpValues(),
            'Form didnt not redirect to itself when submitting with invalid values');
    }

    /**
     * @depends testNavigateToCreateWaypointForm
     * @param array $args
     */
    public function testSubmittingValidImageSavesImage(array $args)
    {
        /**@var Client $client */
        $client = $args[0];
        /**@var Crawler $crawler */
        $crawler = $args[1];

        $form = $crawler->selectButton('speichern')->form();
        $fileLocation = $client->getKernel()->getRootDir();
        $fileLocation .= '/../tests/AppBundle/fixtures/image.jpg';

        $form['app_create_way_point[imageFile][file]']->upload($fileLocation);
        $form['app_create_way_point[locationName]'] = 'Buxtehude is the locationName value';
        $form['app_create_way_point[note]'] = 'note value';

        $crawler = $client->submit($form);
        $this->isSuccessful($client->getResponse());

        // check for saved image - todo refactor to own testmethod
        $link = $crawler->selectLink('Wegpunkt ansehen');
        $crawler = $client->click($link->link());
        $this->isSuccessful($client->getResponse());

        $img = $crawler->filter('img');
        $this->assertSame('/images/way_points/image.jpg', $img->attr('src'));
    }

    /**
     * @depends testNavigateToCreateWaypointForm
     * @param array $args
     */
    public function testUploadingImageExcceedingMaxFileSizeRendersFormErrors(array $args)
    {
        /**@var Client $client */
        $client = $args[0];
        /**@var Crawler $crawler */
        $crawler = $args[1];

        $fileName = 'test_image_42MB.png';
        $fileLocation = $client->getKernel()->getRootDir();
        $fileLocation .= '/../tests/AppBundle/fixtures/' . $fileName;
        $imgStub = new UploadedFile($fileLocation, $fileName);

        $form = $crawler->selectButton('speichern')->form();
        $form['app_create_way_point[imageFile][file]'] = $imgStub;
        $form['app_create_way_point[locationName]'] = 'Buxtehude is the locationName value';
        $form['app_create_way_point[note]'] = 'note value';
        $crawler = $client->submit($form);

        // only working with german translation messages
        $text = $crawler->text();
        $this->assertContains('Datei ist zu groß',
            $text,
            'Error uploading ' . $fileName . '. Either upload_max_filesize in php.ini is lower than the assertion.
                or file was uploaded without triggering a form error.');
    }

    /**
     * @return \Doctrine\Common\DataFixtures\Executor\AbstractExecutor|null|void
     */
    private function loadAllFixtures()
    {
        $this->loadFixtureFiles(
            [
                '@AppBundle/DataFixtures/ORM/test/tag.yml',
                '@AppBundle/DataFixtures/ORM/test/guest.yml',
                '@AppBundle/DataFixtures/ORM/test/team.yml',
                '@AppBundle/DataFixtures/ORM/test/user.yml',
                '@AppBundle/DataFixtures/ORM/test/walk.yml',
                '@AppBundle/DataFixtures/ORM/test/systemicQuestion.yml',
                '@AppBundle/DataFixtures/ORM/test/wayPoint.yml',
            ]
        );
    }

    /**
     * @return Client
     */
    private function createAuthenticatedClient()
    {
        $credentials = [
            'username' => 'waldi_beta',
            'password' => 'waldi_beta',
        ];

        $client = static::makeClient($credentials);
        $client->followRedirects(true);
        return $client;
    }
}
