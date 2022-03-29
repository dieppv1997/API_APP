<?php
// Project: 6909328e1e6d640e24bc2ba0b79f902e9be28484446a50
namespace Database\Seeders;

use App\Enums\Posts\PostStatusEnum;
use App\Enums\Users\RegisterTypeEnum;
use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\Place;
use App\Models\Post;
use App\Models\PostLike;
use App\Models\PostPublishedSequence;
use App\Models\PostTag;
use App\Models\Province;
use App\Models\Tag;
use App\Models\User;
use App\Models\UserFollow;
use App\Traits\HandleExceptionTrait;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TestDataSeeder extends Seeder
{
    use HandleExceptionTrait;

    public $now;
    public $faker;

    public function __construct()
    {
        $this->now = Carbon::now();
        $this->faker = Factory::create('ja_JP');
    }

    /**
     * Run the database seeds.
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function run()
    {
        DB::beginTransaction();
        try {
            $this->createUsers();

            $this->createTags();

            $this->createPlaces();

            $this->createPosts();

            $this->createPostPublishedSequence();

            $this->createPostTag();

            $this->createPostLike();

            $this->createUserFollow();

            $this->createComments();

            $this->createChildComments();

            $this->createCommentLike();

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->handleException($e);
        }
    }

    private function createUsers()
    {
        //Factory for user
        User::truncate();
        $userAvatarPath = 'avatars/samples';
        $regTypes = [
            RegisterTypeEnum::REGISTER_EMAIL,
            RegisterTypeEnum::REGISTER_NICKNAME
        ];
        $usersData = [];
        for ($i = 1; $i <= 15; $i++) {
            $usersData[] = [
                'id' => $i,
                'nickname' => $this->faker->name,
                'email' => $this->faker->unique()->safeEmail,
                'email_verified_at' => $this->now,
                'password' => Hash::make(12345678),
                'avatar_image' => "{$userAvatarPath}/user-{$i}.jpg",
                'cover_image' => null,
                'birthday' => $this->faker->date('Y-m-d','18 years ago'),
                'gender' => rand(1,3),
                'favorite_shop' => '日比谷花壇',
                'favorite_place' => '日比谷公園',
                'intro' => $this->faker->sentence(rand(20,30)),
                'province_id' => Province::all()->random()->id,
                'activated' => 1,
                'device_id' => Str::random(10),
                'remember_token' => '',
                'register_type' => $regTypes[rand(0,1)],
                'created_at' => $this->now,
                'updated_at' => $this->now,
            ];
        }
        User::insert($usersData);
        echo "Done User\n";
    }

    private function createTags()
    {
        //Tag for post
        Tag::truncate();
        $words = [
            'flower',
            'beautiful',
            'lovely',
            'spring',
            'park',
            'earth',
            'valentine',
            'christmas',
        ];
        $tagsData = [];
        for ($i = 0; $i <= 7; $i++) {
            $tagsData[] = [
                'name' => $words[$i],
                'created_by' => User::all()->random()->id,
                'created_at' => $this->now,
                'updated_at' => $this->now,
            ];
        }
        Tag::insert($tagsData);
        echo "Done Tag\n";
    }

    private function createPlaces()
    {
        //Place for post
        Place::truncate();
        $placesData = [];
        for ($i = 1; $i <= 5; $i++) {
            $placesData[] = [
                'google_place_id' => Str::random(15),
                'place_name' => $this->faker->streetName,
                'place_address' => $this->faker->address,
                'latitude' => $this->faker->latitude(),
                'longitude' => $this->faker->longitude(),
                'created_at' => $this->now,
                'updated_at' => $this->now,
            ];
        }
        Place::insert($placesData);
        echo "Done Place\n";
    }

    private function createPosts()
    {
        //Post
        Post::truncate();
        $postImagePath = 'posts/samples';
        $postsData = [];
        for ($i = 1; $i <= 35; $i++) {
            $postsData[] = [
                'user_id' => User::all()->random()->id,
                'image' => "{$postImagePath}/flower-{$i}.jpg",
                'caption' => $this->faker->sentence(rand(10,30)),
                'place_id' => Place::all()->random()->id,
                'status' => 1,
                'published_at' => $this->now,
                'created_at' => $this->now,
                'updated_at' => $this->now,
            ];
        }
        Post::insert($postsData);
        echo "Done Post\n";
    }

    private function createPostPublishedSequence()
    {
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
        echo "Done PostPublishedSequence\n";
    }

    private function createPostTag()
    {
        PostTag::truncate();
        $posts = Post::all();
        $postTagData = [];
        foreach ($posts as $post)
        {
            $postTagData[] = [
                'post_id' => $post->id,
                'tag_id' => Tag::all()->random()->id,
                'created_at' => $this->now,
                'updated_at' => $this->now,
            ];
        }
        PostTag::insert($postTagData);
        echo "Done PostTag\n";
    }

    private function createPostLike()
    {
        PostLike::truncate();
        $posts = Post::all();
        $postLikeData = [];
        $userIdList = User::all()->pluck('id', 'id')->toArray();
        foreach ($posts as $post)
        {
            $tmpList = $userIdList;
            $countLike = rand(1,5);
            for ($i = 1; $i <= $countLike; $i++) {
                $userId = array_rand($tmpList);
                $postLikeData[] = [
                    'post_id' => $post->id,
                    'user_id' => $userId,
                ];
                unset($tmpList[$userId]);
            }
        }

        PostLike::insert($postLikeData);
        echo "Done PostLike \n";
    }

    private function createUserFollow()
    {
        UserFollow::truncate();
        $userFollowData = [];
        $allUserIdList = User::all()->pluck('id')->toArray();
        foreach ($allUserIdList as $userId)
        {
            $remainIdList = User::all()->where('id', '!=', $userId)->pluck('id', 'id')->toArray();

            $countFollow = rand(1,5);
            for ($i = 1; $i <= $countFollow; $i++) {
                $followingId = array_rand($remainIdList);
                $userFollowData[] = [
                    'user_id' => $userId,
                    'following_id' => $followingId,
                    'created_at' => $this->now,
                    'updated_at' => $this->now,
                ];
                unset($remainIdList[$followingId]);
            }
        }
        UserFollow::insert($userFollowData);
        echo "Done UserFollow \n";
    }

    private function createComments()
    {
        Comment::truncate();
        $posts = Post::all();
        $commentData = [];
        $userIdList = User::all()->pluck('id', 'id')->toArray();
        foreach ($posts as $post)
        {
            $countComment = rand(18,45);
            for ($i = 1; $i <= $countComment; $i++) {
                $userId = array_rand($userIdList);
                $commentData[] = [
                    'post_id' => $post->id,
                    'user_id' => $userId,
                    'parent_id' => 0,
                    'content' => $this->faker->sentence(rand(5,30)),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }
        Comment::insert($commentData);
        echo "Done Comment \n";
    }

    private function createChildComments()
    {
        $comments = Comment::all();
        $childCommentData = [];
        $userIdList = User::all()->pluck('id', 'id')->toArray();
        foreach ($comments as $comment)
        {
            $needSetChild = rand(1,3);
            if ($needSetChild != 2) {
                continue;
            }
            $countComment = rand(15,45);
            for ($i = 1; $i <= $countComment; $i++) {
                $userId = array_rand($userIdList);
                $childCommentData[] = [
                    'post_id' => $comment->post_id,
                    'user_id' => $userId,
                    'parent_id' => $comment->id,
                    'content' => $this->faker->sentence(rand(5,30)),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }

        foreach (array_chunk($childCommentData, 1000, true) as $chunk) {
            Comment::insert($chunk);
        }
        echo "Done ChildComments \n";
    }

    private function createCommentLike()
    {
        CommentLike::truncate();
        $comments = Comment::all();
        $commentLikeData = [];
        $userIdList = User::all()->pluck('id', 'id')->toArray();
        foreach ($comments as $comment)
        {
            $needSetChild = rand(1,3);
            if ($needSetChild != 2) {
                continue;
            }
            $tmpList = $userIdList;
            $countLike = rand(1,5);
            for ($i = 1; $i <= $countLike; $i++) {
                $userId = array_rand($tmpList);
                $commentLikeData[] = [
                    'comment_id' => $comment->id,
                    'user_id' => $userId,
                ];
                unset($tmpList[$userId]);
            }
        }

        foreach (array_chunk($commentLikeData, 1000, true) as $chunk) {
            CommentLike::insert($chunk);
        }
        echo "Done CommentLike \n";
    }
}
