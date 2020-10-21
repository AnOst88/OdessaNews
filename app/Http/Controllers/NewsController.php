<?php

namespace App\Http\Controllers;

use App\News;
use App\Repositories\NewsRepository as NewsRepository;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function indexAction(NewsRepository $parsing){

        $check_news = News::first();

        if(!$check_news){
            $parser = $parsing->handleFetchtedNews();
            //dd($parser);
            $news = News::orderBy('title', 'desc')->paginate(5);
            return view ('news', compact('news'));

        }

        $news = News::orderBy('id', 'desc')->paginate(5);
        return view ('news', compact('news'));

    }
    public function sortAction(){

        $parameter = request()->input('param');
        $direction = request()->input('dir' , 'asc');

        $news = News::orderBy($parameter, $direction)->paginate(5)->appends(request()->all());

        return view ('news', compact('news'));

    }

}
