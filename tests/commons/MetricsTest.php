<?php

class MetricsTest extends \AppTestCase
{

  public function test_metrics(){

    \metrics\increase("tests");
    \metrics\set("last test",date('c'));

    $nTests= \metrics\get("tests");

    $this->assertGreaterThan(0, $nTests,'metric of tests should have at least one');

  }
}

?>
