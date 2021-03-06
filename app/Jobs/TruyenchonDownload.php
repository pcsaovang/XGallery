<?php

namespace App\Jobs;

use App\Crawlers\Crawler\Truyenchon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

/**
 * Class TruyenchonDownload
 * @package App\Jobs
 */
class TruyenchonDownload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int Execute timeout
     */
    public int     $timeout = 300;
    private string $id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $model = \App\Truyenchon::find($this->id);

        $crawler = app(Truyenchon::class);
        $chapers = $crawler->getItemChapters($model->url);

        $chapers->each(function ($chapter, $index) use ($crawler, $model) {
            if (!$item = $crawler->getItemDetail($chapter)) {
                return;
            }

            foreach ($item->images as $image) {
                $crawler->download(
                    $image,
                    'truyenchon'.DIRECTORY_SEPARATOR.Str::slug($model->title).DIRECTORY_SEPARATOR.$index
                );
            }
        });
    }
}
