<?php


namespace App\Server;

use App\Admin\Repositories\Answers;
use App\Admin\Repositories\Questions;

/**
 * Class AnswersHtml
 * @package App\Server
 */
class AnswersHtml
{
    /**
     * @param $questionID
     * @return string
     */
    public static function en($questionID)
    {
        $answers = (new Answers())->info($questionID);
        if (!$answers) {
//            return '';
        }
        $html = self::header();
        foreach ($answers as $answer) {
            $html .= self::userInfo($answer);
            $content = str_replace(['<p>', '< p>', '<p >', '<P>', '< P>', '<P >'], self::pElementStart(), $answer['content']);
            $content = str_replace(['</p>', '< /p>', '</p >', '</P>', '< /P>', '</P >'], self::pElementEnd(), $content);
            $content = str_replace(['<ul>', '< ul>', '<ul >'], self::ulElementStart(), $content);
            $content = str_replace(['</ul>', '< /ul>', '</ul >'], self::ulElementEnd(), $content);
            $content = str_replace(['<ol>', '< ol>', '<ol >'], self::olElementStart(), $content);
            $content = str_replace(['</ol>', '< /ol>', '</ol >'], self::olElementEnd(), $content);
            $content = str_replace(['<div>', '< div>', '<div >'], self::imgElementStart(), $content);
            $content = str_replace(['</div>', '</ div>', '</div >', '< /div>'], self::imgElementEnd(), $content);
            $html .= $content;
            $html .= self::separate();
        }
        return $html;
    }


    public static function wx($questionID)
    {
        $html = (new Questions())->getWxHtml($questionID);
        if ($html) {
//            return $html;
        }
        $answers = (new Answers())->info($questionID);
        if (!$answers) {
            return '';
        }
        $html = self::header();
        foreach ($answers as $answer) {
            (new \App\Admin\Repositories\Answers())->replaceImgElement($answer['id']);
            if (strpos('https://qph.fs.quoracdn.net', $answer['content_cn']) !== false) {
                $answer['content_cn'] = (new \App\Admin\Repositories\Answers())->replaceContentCNImgElement($answer['id'], $answer['content_cn']);
                \App\Models\Answers::where('id', $answer['id'])->update(['content_cn' => $answer['content_cn']]);
            }
            $html .= self::userInfo($answer);
            $content = str_replace(['<p>', '< p>', '<p >', '<P>', '< P>', '<P >'], self::pElementStart(), $answer['content_cn']);
            $content = str_replace(['</p>', '< /p>', '</p >', '</P>', '< /P>', '</P >'], self::pElementEnd(), $content);
            $content = str_replace(['<ul>', '< ul>', '<ul >'], self::ulElementStart(), $content);
            $content = str_replace(['</ul>', '< /ul>', '</ul >'], self::ulElementEnd(), $content);
            $content = str_replace(['<ol>', '< ol>', '<ol >'], self::olElementStart(), $content);
            $content = str_replace(['</ol>', '< /ol>', '</ol >'], self::olElementEnd(), $content);
            $content = str_replace(['<div>', '< div>', '<div >'], self::imgElementStart(), $content);
            $content = str_replace(['</div>', '</ div>', '</div >', '< /div>'], self::imgElementEnd(), $content);
            $html .= $content;
            $html .= self::separate();
        }
        $html .= self::footer();
        return $html;
    }


    protected static function pElementStart()
    {
        return '<p style="margin-top: 10px;margin-bottom: 20px;white-space: normal;">
                    <span style="letter-spacing: 1px;">';
    }

    protected static function pElementEnd()
    {
        return '</span>
            </p>';
    }

    protected static function ulElementStart()
    {
        return '<p style="margin-top: 10px;margin-bottom: 20px;white-space: normal;">
                    <ul>';
    }

    protected static function ulElementEnd()
    {
        return '</ul>
                </p>';
    }

    protected static function olElementStart()
    {
        return '<p style="margin-top: 10px;margin-bottom: 20px;white-space: normal;">
                    <ol>';
    }

    protected static function olElementEnd()
    {
        return '</ol>
                </p>';
    }

    protected static function imgElementStart()
    {
        return '<p style="white-space: normal;text-align: center;">';
    }

    protected static function imgElementEnd()
    {
        return '</p>';
    }

    /**
     * @param $answer
     * @return string
     */
    protected static function userInfo($answer)
    {
        $userCredentialCN = empty($answer['user_credential_cn']) ? '不详' : $answer['user_credential_cn'];
        $userInfoHtml = '<section style="margin-top: 5px;white-space: normal;">
                  <section style="width: 578px;display: flex;">
                    <section>
                      <p style="text-align: center;">
                        <img data-cropselx1="0" data-cropselx2="50" data-cropsely1="0" data-cropsely2="50" data-ratio="1" src="' . $answer['user_avatar'] . '" data-type="jpeg" data-w="100" style="height: 50px;border-radius: 33px;width: 50px;">
                      </p>
                    </section>
                    <section style="display: flex;flex-direction: column;align-items: flex-end;">
                      <section>
                        <p style="margin-bottom: 3px;margin-left: 8px;text-align: left;font-size: 15px;color: rgb(0, 0, 0);">
                          <span style="color: rgb(136, 136, 136);font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Oxygen-Sans, Ubuntu, Cantarell, &quot;Helvetica Neue&quot;, sans-serif;letter-spacing: 1px;">' . $answer['user_name_cn'] . "({$answer['user_name']})" . '
                            </span>
                          <br>
                        </p>
                        <p style="margin-left: 8px;text-align: left;font-size: 15px;color: rgb(0, 0, 0);">
                          <span style="background-color: rgb(255, 255, 255);color: rgb(136, 136, 136);font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Oxygen-Sans, Ubuntu, Cantarell, &quot;Helvetica Neue&quot;, sans-serif;letter-spacing: 1px;">' . $userCredentialCN . '
                            </span>
                        </p>
                      </section>
                    </section>
                  </section>
                </section>';
        return $userInfoHtml;
    }


    protected static function separate()
    {
        return '<section style="margin-bottom: 20px;white-space: normal;">
                    <br>
                </section>';
    }

    protected static function header()
    {
        $headerHtml = '<p style="white-space: normal;text-align: center;" data-mpa-powered-by="yiban.io">
                  <img class="rich_pages" data-cropselx1="0" data-cropselx2="578" data-cropsely1="0" data-cropsely2="195" data-ratio="0.43440233236151604" data-s="300,640" src="https://mmbiz.qpic.cn/mmbiz_png/JqCDSg4nHefqPyibIza2HH7Mt6fUTDbW4H9dOJ8bYOA97mftrGR9soSV5Lyu4bJQyYQR9RCiapzWY7PfxRR3Q3zA/640?wx_fmt=png" data-type="png" data-w="686" style="height: auto;width: 100%;" data-backw="578" data-backh="251">
                </p>
                <p line="NuCL" style="white-space: normal;">​&nbsp;&nbsp;</p>
                <section style="white-space: normal;margin-bottom: 20px;">
                  <span style="letter-spacing: 1px;">内容摘要部分...</span>
                </section>
                <p line="x0IU" style="white-space: normal;">&nbsp;</p>
                <p style="text-align: center;">
                  <img class="rich_pages" data-backh="417" data-backw="578" data-cropselx1="0" data-cropselx2="578" data-cropsely1="0" data-cropsely2="325" data-ratio="0.722" data-s="300,640" src="https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1591958147513&di=e5c714e89e72d0d44bb1418b162cb51f&imgtype=0&src=http%3A%2F%2Fimg.1ppt.com%2Fuploads%2Fallimg%2F1903%2F1_190321152420_1.jpg" data-type="jpeg" data-w="500" style="width: 100%;height: auto;">
                </p>
                <p style="text-align: center;"><br></p>
                <section data-mpa-template="t" mpa-from-tpl="t" style="white-space: normal;">
                  <section mpa-from-tpl="t">
                    <section mpa-from-tpl="t" style="margin-top: 10px;margin-bottom: 10px;">
                      <section mpa-from-tpl="t" style="padding-right: 30px;display: inline-block;font-size: 18px;height: 40px;line-height: 40px;border-bottom: 1px solid rgb(67, 46, 46);">
                        外国网友评论：
                      </section>
                      <section mpa-from-tpl="t" style="width: 50px;border-top: 3px solid rgb(67, 46, 46);">
                        <br>
                      </section>
                    </section>
                  </section>
                </section>
                <p>
                  <span style="color: rgb(136, 136, 136);font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Oxygen-Sans, Ubuntu, Cantarell, \'Helvetica Neue\', sans-serif;font-size: 15px;letter-spacing: 1px;text-align: left;"></span>
                </p>';
        return $headerHtml;
    }


    protected static function footer()
    {
        $footerHtml = '<section data-mpa-template="t" mpa-from-tpl="t">
              <section data-mpa-template="t" mpa-from-tpl="t">
                <section data-id="93731" mpa-from-tpl="t">
                  <section style="padding:0.5em;" mpa-from-tpl="t">
                    <section style="box-shadow: 0px 0px 10px #d3d3d3;background: #ffffff;" mpa-from-tpl="t">
                      <section style="padding: 1em 0px 1em 1em;" mpa-from-tpl="t">
                        <section style="display: flex;justify-content: space-between;align-items:center;padding-bottom: 10px;" mpa-from-tpl="t">
                          <section data-brushtype="text" style="color: rgb(51, 51, 51);font-size: 15px;letter-spacing: 1px;width: 100%;" data-width="100%" mpa-from-tpl="t">
                            <strong>往期文章
                              </strong>
                          </section>
                          <section style="width:2.4em;margin-left: 10px;" mpa-from-tpl="t">
                            <section style="width: 2.4em;margin-top: 2px;" mpa-from-tpl="t">
                              <br>
                            </section>
                          </section>
                        </section>
                        <section style="width: 100%;display: flex;justify-content: space-between;border-top: 1px solid #e5e5e5;align-items:center;padding-top: 10px;padding-bottom: 10px;" data-width="100%" mpa-from-tpl="t">
                          <section data-brushtype="text" style="color: rgb(51, 51, 51);font-size: 15px;letter-spacing: 1px;" mpa-from-tpl="t">
                            <a target="_blank" href="#">Article Title
                              </a>
                            <br>
                          </section>
                          <section style="width:2.4em;margin-left: 10px;" mpa-from-tpl="t">
                            <section style="width: 2.4em;margin-top: 2px;" mpa-from-tpl="t">
                              <img data-ratio="0.7804878048780488" src="https://mmbiz.qpic.cn/mmbiz_png/6aVaON9Kibf6ouXic7Uuc3Q22Yho6GQp2ESgu6l68tycQYQPPAZtZiajdeiaHghFg8N5GlWNX8k28VYPbh1JK31Mgw/640" data-w="82" data-width="100%" style="width: 100%;display: block;">
                            </section>
                          </section>
                        </section>
                        <section style="width: 100%;display: flex;justify-content: space-between;border-top: 1px solid #e5e5e5;align-items:center;padding-top: 10px;padding-bottom: 10px;" data-width="100%" mpa-from-tpl="t">
                          <section data-brushtype="text" style="color: rgb(51, 51, 51);font-size: 15px;letter-spacing: 1px;" mpa-from-tpl="t">
                            <a target="_blank" href="#">Article Title
                              </a>
                            <br>
                          </section>
                          <section style="width:2.4em;margin-left: 10px;" mpa-from-tpl="t">
                            <section style="width: 2.4em;margin-top: 2px;" mpa-from-tpl="t">
                              <img data-ratio="0.7804878048780488" src="https://mmbiz.qpic.cn/mmbiz_png/6aVaON9Kibf6ouXic7Uuc3Q22Yho6GQp2ESgu6l68tycQYQPPAZtZiajdeiaHghFg8N5GlWNX8k28VYPbh1JK31Mgw/640" data-w="82" data-width="100%" style="width: 100%;display: block;">
                            </section>
                          </section>
                        </section>
                        <section mpa-from-tpl="t">
                          <section style="width: 100%;display: flex;justify-content: space-between;border-top: 1px solid #e5e5e5;align-items:center;padding-top: 10px;padding-bottom: 10px;" data-width="100%" mpa-from-tpl="t">
                            <section data-brushtype="text" style="color: rgb(51, 51, 51);font-size: 15px;letter-spacing: 1px;" mpa-from-tpl="t">
                              <a target="_blank" href="#">Article Title
                                </a>
                              <br>
                            </section>
                            <section style="width:2.4em;margin-left: 10px;" mpa-from-tpl="t">
                              <section style="width: 2.4em;margin-top: 2px;" mpa-from-tpl="t">
                                <img data-ratio="0.7804878048780488" src="https://mmbiz.qpic.cn/mmbiz_png/6aVaON9Kibf6ouXic7Uuc3Q22Yho6GQp2ESgu6l68tycQYQPPAZtZiajdeiaHghFg8N5GlWNX8k28VYPbh1JK31Mgw/640" data-w="82" data-width="100%" style="width: 100%;display: block;">
                              </section>
                            </section>
                          </section>
                        </section>
                      </section>
                    </section>
                  </section>
                  <p>
                    <br mpa-from-tpl="t">
                  </p>
                </section>
                <section data-role="paragraph" mpa-from-tpl="t">
                  <p>
                    <br>
                  </p>
                </section>
              </section>
              <section data-mpa-template="t" mpa-from-tpl="t">
                <section mpa-from-tpl="t" style="background-image: url(&quot;https://mmbiz.qpic.cn/mmbiz_gif/xTobqJDEK8QlnkvHtejUoibeXIP6TnuGRBsf6p9ZgMexficoGsiaLuMby0k8Kf7DYgagLWEoxVhuXG6cMmFSGnNVA/640?wx_fmt=gif&quot;);background-position: 100% 100%;background-repeat: repeat;background-attachment: scroll;background-size: 100% 100%;box-sizing: border-box;padding: 20px;text-align: center;">
                  <section style="width: 30%;display: inline-block;vertical-align: middle;box-sizing: border-box;" mpa-from-tpl="t">
                    <img data-cropselx1="0" data-cropselx2="160" data-cropsely1="0" data-cropsely2="160" data-ratio="1" src="https://mmbiz.qpic.cn/mmbiz_jpg/JqCDSg4nHefwPFIUtIiauX66FibVMauuwEShumMm5O7p6HO1JPwcklzrekibsX9bPWQDXFLIYdYHIKm98OdMH4Pfw/640?wx_fmt=jpeg" data-type="jpeg" data-w="1280" style="display: block;width: 160px;height: 160px;">
                  </section>
                  <section style="width: 70%;display: inline-block;vertical-align: middle;" mpa-from-tpl="t">
                    <section style="color: #ffdd4d;letter-spacing: 1px;line-height: 24px;" mpa-from-tpl="t">
                      <p style="letter-spacing: 4px;font-size: 18px;">
                        <strong><em>国际舆情观察
                          </em></strong>
                      </p>
                      <p style="letter-spacing: 4px;font-size: 18px;">
                        <em><strong mpa-from-tpl="t">外国网友对中国的看法
                          </strong></em>
                        <strong mpa-from-tpl="t"></strong>
                      </p>
                    </section>
                  </section>
                </section>
              </section>
              <p>
                <br>
              </p>
              <p>
                <br>
              </p>
            </section>
            <section data-mpa-template="t" mpa-from-tpl="t">
              <section style="max-width: 100%;box-sizing: border-box;color: rgb(51, 51, 51);" mpa-from-tpl="t">
                <section style="max-width: 100%;box-sizing: border-box;word-wrap: break-word !important;" mpa-from-tpl="t">
                  <section style="max-width: 100%;box-sizing: border-box;color: rgb(234, 28, 28);word-wrap: break-word !important;" mpa-from-tpl="t">
                    <p style="max-width: 100%;min-height: 1em;box-sizing: border-box !important;overflow-wrap: break-word !important;">
                      <img class="__bg_gif" data-copyright="0" data-ratio="0.0921875" data-type="gif" data-w="640" style="box-sizing: border-box !important;word-wrap: break-word !important;visibility: visible !important;width: auto !important;height: auto !important;" src="https://mmbiz.qpic.cn/mmbiz_gif/LFNeshgs1H1ibEKvTibqIwvicYTtbXbxicaTg3PZ0dfKC2hYyicRGRQBiaBknKySSnjEEqAgLBfRGKGooqHm7S0U24eg/640?">
                    </p>
                  </section>
                </section>
              </section>
            </section>';
        return $footerHtml;
    }

}
