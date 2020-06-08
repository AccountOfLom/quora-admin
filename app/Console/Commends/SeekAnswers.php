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
        //查没有答案数据的问题
        $questions = DB::table('questions')
            ->leftJoin('answers', 'questions.id', '=', 'answers.question_id')
            ->whereNull('answers.id')
            ->select('questions.id', 'questions.link')
            ->get();
        if (count($questions) == 0) {
            return false;
        }
        $currentQuestion = current($questions->toArray());

        $nodePort = env('NODE_HTTP_PORT');
        $url = '127.0.0.1:' . $nodePort . '/answers?question_id=' . $currentQuestion->id . '&question=' . $currentQuestion->link;
        $request = new \GuzzleHttp\Psr7\Request('GET', $url);
        $promise = (new Client())->sendAsync($request)->then(function ($response) {
            echo 'I completed! ' . $response->getBody();
        });
        $promise->wait();
        echo 'seek answers success';
    }
}
