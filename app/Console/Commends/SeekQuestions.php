<?php


namespace App\Console\Commends;


use App\Models\Topics;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * 爬取问题
 * Class SeekQuestions
 * @package App\Console\Commends
 */
class SeekQuestions extends Command
{

    /**
     * 控制台命令 signature 的名称。
     *
     * @var string
     */
    protected $signature = 'seek-questions';

    /**
     * 控制台命令说明。
     *
     * @var string
     */
    protected $description = 'seek questions';

    /**
     * 执行控制台命令。
     *
     * @return mixed
     */
    public function handle()
    {
        $topics = Topics::where('status', 1)->get();
        $currentTopic = '';
        foreach ($topics as $topic) {
            //查当天没有爬取的话题
            $seeked = DB::table('questions')
                ->where('topic', $topic['topic'])
                ->whereDate('created_at', date('Y-m-d'))
                ->exists();
            if (!$seeked) {
                $currentTopic = $topic['topic'];
                break;
            }
        }
        if (!$currentTopic) {
            return false;
        }

        $nodePort = env('NODE_HTTP_PORT');
        $url = '127.0.0.1:' . $nodePort . '/questions?topic=' . $currentTopic;
        $request = new \GuzzleHttp\Psr7\Request('GET', $url);
        $promise = (new Client())->sendAsync($request)->then(function ($response) {
            echo 'I completed! ' . $response->getBody();
        });
        $promise->wait();
        echo 'seek questions success';
    }
}
