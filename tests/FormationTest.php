<?php

use App\Entity\Formation;
use PHPUnit\Framework\TestCase;


class FormationTest extends TestCase
{
    public function testGetPublishedAtString() 
    {
        $Formation = new Formation();
        $Formation->setTitle("Formation test");
        $Formation->setPublishedAt(new \DateTime("2022-04-14"));
        $this->assertEquals("14/04/2022", $Formation->getPublishedAtString());
    }
}