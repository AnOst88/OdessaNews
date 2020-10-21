<?php

namespace App\Repositories;

use App\Repositories\NewsParserInterface;
use App\News;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Goutte\Client;
use GuzzleHttp\Client as Guzzle;


class NewsRepository implements NewsParserInterface
{
    /**
     * The attributes that are mass assignable.
     *
     * @var
     */

    public $link = 'https://www.segodnya.ua/regions/odessa.html';
    public $urls = [];

    /**
     * Fetches news ID
     *
     * @method
     * @param $node current news
     * @return string $news_id fetched news_id
     */
    public function getNewsId($node)
    {
        $news_id = $node->filter('.article-section')->attr('data-id');
        return $news_id;
    }

    /**
     * Fetches news tags
     *
     * @method
     * @param $node current news
     * @return string $tags fetched tags
     */
    public function getTags($node)
    {

        $tagsArray = $node->filter('.article__footer_tags .tags a')->each(function ($tag, $i) {
            $temp = $tag->text();
            return $temp;
        });
        $tags = implode(", ", $tagsArray);

        return $tags;
    }

    /**
     * Handle fetched news and save it in DB
     *
     * @method
     */

    public function handleFetchtedNews()
    {

        foreach ($this->fetchHtmlLinks() as $url) {

            $client = new Client();
            $crawler = $client->request('GET', "$url");

            // Loop through each news
            $crawler->filter('.article-section')->each(function ($node) {

                $months_name = [
                    'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря', 'сегодня', 'вчера',
                ];

                $months_number = [
                    '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', Carbon::now()->toDateString(), Carbon::yesterday()->toDateString(),
                ];

                $date = explode(',', $node->filter('.time')->text());

                // Get date
                if ($date[0] != 'Сегодня' && $date[0] != 'Вчера') {
                    $date = str_replace($months_name, $months_number, $date);
                    $md = explode(' ', $date[0]);
                    $hs = explode(':', $date[1]);

                    $date = Carbon::create(null, $md[1], $md[0], $hs[0], $hs[1])->toDateTimeString();

                    $current_date = Carbon::now()->startOfDay();
                    $formatted_date = Carbon::parse($date)->startOfDay();
                    $diff = $current_date->diffInDays($formatted_date);

                    // In case news is older than 5 days then continue loop
                    if ($diff > 5) {
                        return false;
                    }
                }
                if ($date[0] == 'Сегодня' || $date[0] == 'Вчера') {
                    $date = str_replace($months_name, $months_number, $date);
                    $md = explode('-', $date[0]);
                    $hs = explode(':', $date[1]);


                    $date = Carbon::create($md[0], $md[1], $md[2], $hs[0], $hs[1])->toDateTimeString();
                }

                // Get news ID
                $news_id = $this->getNewsId($node);

                // Check if such news exists in DB
                $news = DB::table('news')->where('news_id', $news_id)->value('id');
                if ($news == null) {

                    // Get tags
                    $tags = $this->getTags($node);

                    // Add news to DB
                    $data = [
                        'news_id' => $news_id,
                        'link' => $node->filter('.article-section')->attr('data-urlpath'),
                        'title' => $node->filter('.article__header_title')->text(),
                        'authors' => $node->filter('.authors')->text(),
                        'date' => $date,
                        'tags' => $tags
                    ];

                    News::create($data);
                }
            });
        }
    }

    /**
     * Fetches html links
     *
     * @method
     * @return array $urls fetched html links
     */
    public function fetchHtmlLinks()
    {
        $client = new Client();

        $crawler = $client->request('GET', $this->link);

        $this->urls = $crawler->filter('.st__news-list ul > li')->each(function ($node) {
            return $node->filter('a')->attr('href');
        });
        return $this->urls;
    }
}
