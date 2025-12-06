<?php

namespace HoomanMirghasemi\Sms\Tests\Feature\Web;

use HoomanMirghasemi\Sms\Tests\TestCase;

class SmsControllerTest extends TestCase
{
    public function testIndexSuccess()
    {
        $response = $this->get(route('sms.index'));
        $response->assertOk();
    }

    public function testIndexNotFoundInProduction()
    {
        $this->app->config['sms.dont_show_sms_list_page_condition'] = 'production';
        $response = $this->get(route('sms.index'));
        $response->assertNotFound();
    }
}
