<?php

namespace App\Repositories;

interface NewsParserInterface
{
    public function handleFetchtedNews();
    public function getNewsId($node);
    public function getTags($node);
    public function fetchHtmlLinks();

}
