<?php

namespace App\Repositories\Mail;
use App\Repositories\Website\WebsiteRepositoryInterface as WebsiteRepositoryInterface;
use App\Models\Website;
use App\Models\Mail as Mailing;

class WebsiteRepository implements WebsiteRepositoryInterface
{
    public function getWebsiteDetail()
    {
        return Website::find(1)->first();
    }

    public function updateWebsite()
    {

    }
}
