<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace Database\Seeders;

use App\Enums\Posts\PostStatusEnum;
use App\Models\Post;
use App\Models\PostPublishedSequence;
use App\Traits\HandleExceptionTrait;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostPublishedSequenceSeeder extends Seeder
{
    use HandleExceptionTrait;

    /**
     * Run the database seeds.
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function run()
    {
        DB::beginTransaction();
        try {
            PostPublishedSequence::truncate();
            $posts = Post::where('status', PostStatusEnum::PUBLISHED)
                ->orderBy('published_at', 'ASC')
                ->orderBy('id', 'ASC')
                ->get();

            $postPublishedSequenceData = [];
            foreach ($posts as $post)
            {
                $postPublishedSequenceData[] = [
                    'post_id' => $post->id,
                ];
            }
            PostPublishedSequence::insert($postPublishedSequenceData);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->handleException($e);
        }
    }
}
