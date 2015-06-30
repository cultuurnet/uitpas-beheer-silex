<?php

namespace CultuurNet\UiTPASBeheer\Counter;

class CounterAwareUitpasServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_provides_methods_to_get_the_dependencies()
    {
        $uitpas = $this->getMock(\CultureFeed_Uitpas::class);

        $key = new CounterConsumerKey('31413BDF-DFC7-7A9F-10403618C2816E44');

        $service = new MockCounterAwareUitpasService($uitpas, $key);

        $this->assertEquals($uitpas, $service->exposeUitpasService());
        $this->assertTrue($key === $service->exposeCounterConsumerKeyObject());
        $this->assertTrue($key->toNative() === $service->exposeCounterConsumerKey());
    }
}
