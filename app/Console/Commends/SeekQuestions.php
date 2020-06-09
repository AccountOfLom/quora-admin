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
        $topic = Topics::where('status', 1)->where('seeked_at', '<', time() - 3600)->first();  //查１小时内没有爬取的话题
        if (!$topic) {
            return false;
        }
        $nodePort = env('NODE_HTTP_PORT');
        $url = '127.0.0.1:' . $nodePort . '/questions?topic=' . $topic->topic;
        $request = new \GuzzleHttp\Psr7\Request('GET', $url);
        $promise = (new Client())->sendAsync($request)->then(function ($response) {
            echo 'I completed! ' . $response->getBody();
        });
        $promise->wait();
        Topics::where('id', $topic->id)->update(['seeked_at' => time()]);
        echo 'seek questions success';
    }
}
