<?php


namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ConferenceControllerTest extends WebTestCase
{
//    public function testIndex()
//    {
//        $client = static::createClient();
//        $client->request('GET', '/');
//
//        $this->assertResponseIsSuccessful();
//        $this->assertSelectorTextContains('h2', 'Give your feedback');
//    }

//    public function testConferencePage()
//    {
//        $client = static::createClient();
//        $crawler = $client->request('GET', '/');
//
//
//        $this->assertCount(2,$crawler->filter('h4'));
//
//        $client->clickLink('View');
//
//        $this->assertPageTitleContains('Conference Guestbook-New-York 2020');
//        $this->assertResponseIsSuccessful();
//        $this->assertSelectorTextContains('h2', 'New-York');
//        $this->assertSelectorExists('div:contains("There are 1 comments")');
//    }

    public function testCommentSubmission()
    {
        $client = static::createClient();
        $client->request('GET', '/conference/new-york-2020');
        $client->submitForm('Submit',[
            'comment_form[author]' => 'Pavel',
            'comment_form[text]' => 'Some feedback from an automated functional test',
            'comment_form[email]' => 'me@automat.ed',
            'comment_form[photo]'=> dirname(__DIR__, 2).'/public/images/under-construction.gif',
        ]);
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('div:contains("There are 2 comments")');
    }

}