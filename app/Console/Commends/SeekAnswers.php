<?php


namespace App\Console\Commends;


use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * 爬取问题
 * Class SeekQuestions
 * @package App\Console\Commends
 */
class SeekAnswers extends Command
{

    /**
     * 控制台命令 signature 的名称。
     *
     * @var string
     */
    protected $signature = 'seek-answers';

    /**
     * 控制台命令说明。
     *
     * @var string
     */
    protected $description = 'seek answers';

    /**
     * 执行控制台命令。
     *
     * @return mixed
     */
    public function handle()
    {
        $question = DB::table('questions')
            ->leftJoin('answers', 'questions.id', '=', 'answers.question_id')
            ->select('questions.id', 'questions.link', 'questions.text_cn', DB::raw('count(*) as answers_count'))
            ->groupBy('questions.id')
            ->having('answers_count', '<', 10)  //回答数少于10个的问题，继续爬取回答
            ->whereNull('questions.text_cn')
            ->orderBy('seek_answers_time')   //  seek_answers_time 最后爬取时间
            ->orderBy('id', 'desc')
            ->first();

        if (!$question) {
            return true;
        }

        //更新最新爬取时间
        DB::table('questions')->where('id', $question->id)->update(['seek_answers_time' => time()]);

        $nodePort = env('NODE_HTTP_PORT');
        $url = '127.0.0.1:' . $nodePort . '/answers?question_id=' . $question->id . '&question=' . $question->link;
        $request = new \GuzzleHttp\Psr7\Request('GET', $url);
        $promise = (new Client())->sendAsync($request)->then(function ($response) {
            echo 'I completed! ' . $response->getBody();
        });
        $promise->wait();
        echo 'seek answers success';
    }
}
