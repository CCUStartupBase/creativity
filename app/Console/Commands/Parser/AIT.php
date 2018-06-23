<?php

namespace App\Console\Commands\Parser;

use Illuminate\Console\Command;

use App\News;

class AIT extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parser:ait';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'parse AIT news';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
	parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->getAit('http://www.ait.ccu.edu.tw/news.asp');
    }

    public function getAit($link)
    {
	$text = file_get_contents($link); 
	preg_match_all('/<td[^>]*>([\d]+\/[\d]+\/[\d]+)<\/td>/', $text, $match); //抓日期
        preg_match_all('/<td[^>]*>[^>]*<a href="(.*?)">(.*?)<\/td>/s', $text, $match2);  //抓 URL & 標題

        $news = [];

	for($i = 0; $i < count($match[1]); $i++) {
	    $URL = 'http://www.ait.ccu.edu.tw/' . $match2[1][$i];

	    $date = iconv("big5", "UTF-8", $match[1][$i]);
	    $title = iconv("big5", "UTF-8", strip_tags($match2[2][$i])); 
            $content = $this->getContent($URL); //抓內容
        }

        return $news;
    }

    public function getContent($URL)
    {
	$text = file_get_contents($URL);
	$cont = '';
	preg_match_all('/<p class="MsoNormal">(.*)<\/p>/is', $text, $match3); //抓內容
	for($i=0;$i<count($match3[1]);$i++){
	    $sentance = iconv("big5","UTF-8",strip_tags($match3[1][$i]));
	    $cont = $cont.$sentance;
	}
	$cont = str_replace("\n","</br>", $cont);
	return $cont;
    }
}
