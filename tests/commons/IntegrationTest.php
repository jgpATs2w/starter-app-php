<?php

class IntegrationTest extends \AppTest
{
    public function test_home(){

      $client = new GuzzleHttp\Client();
      $res = $client->request('GET', LAND_URL . 'home' );

      $status= $res->getStatusCode();

      $json= json_encode((string)$res->getBody());

      $this->assertSame($status,200);
    }

}
