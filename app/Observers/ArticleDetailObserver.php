<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 * @license http://www.larva.com.cn/license/
 */

namespace App\Observers;

use App\Models\Article;
use App\Models\ArticleDetail;
use App\Models\ArticleMod;
use Larva\Censor\Censor;
use Larva\Censor\CensorNotPassedException;

/**
 * 文章详情观察者
 * @author Tongle Xu <xutongle@gmail.com>
 */
class ArticleDetailObserver
{
    /**
     * Handle the "saving" event.
     *
     * @param ArticleDetail $articleDetail
     * @return void
     */
    public function saving(ArticleDetail $articleDetail)
    {
        if (!is_array($articleDetail->extra)) {
            $articleDetail->extra = [
                'form' => null,
                'form_url' => null,
                'bd_daily' => 0
            ];
        }
    }

    /**
     * Handle the "saved" event.
     *
     * @param ArticleDetail $articleDetail
     * @return void
     */
    public function saved(ArticleDetail $articleDetail)
    {
        $censor = Censor::getFacadeRoot();
        try {
            $articleDetail->content = $censor->textCensor($articleDetail->content);
            if ($censor->isMod) {//需要审核
                $articleDetail->article->status = Article::STATUS_UNAPPROVED;
            }
        } catch (CensorNotPassedException $e) {
            $articleDetail->article->status = Article::STATUS_REJECTED;
        }

        // 记录触发的审核词
        if ($articleDetail->article->status === Article::STATUS_UNAPPROVED && $censor->wordMod) {
            if (($stopWords = $articleDetail->stopWords) == null) {
                $stopWords = new ArticleMod(['article_id' => $articleDetail->article_id]);
            }
            $stopWords->stop_word = implode(',', array_unique($censor->wordMod));
            $stopWords->save();
        }

        //保存并且不再触发 事件
        $articleDetail->saveQuietly();
        $articleDetail->article->saveQuietly();

        //自动提取Tag
        if (empty($articleDetail->article->tag_values)) {
            \App\Jobs\Article\ExtractTagJob::dispatch($articleDetail->article);
        }

        //提取关键词
        if (empty($articleDetail->article->metas['keywords'])) {
            \App\Jobs\Article\ExtractKeywordJob::dispatch($articleDetail->article)->delay(now()->addSeconds(20));
        }
        $articleDetail->article->notifySearchEngines();
    }
}
