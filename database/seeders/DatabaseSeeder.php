<?php

namespace Database\Seeders;

use App\Models\InstagramComment;
use App\Models\InstagramPost;
use App\Models\Sentiment;
use App\Models\Tweet;
use App\Models\TweetComment;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        collect([
            [Sentiment::POSITIVE, 'Positif', '#2563eb'],
            [Sentiment::NEUTRAL, 'Netral', '#16a34a'],
            [Sentiment::NEGATIVE, 'Negatif', '#b91c1c'],
        ])->each(fn (array $sentiment) => Sentiment::firstOrCreate(
            ['name' => $sentiment[0]],
            ['label' => $sentiment[1], 'color' => $sentiment[2]]
        ));

        User::updateOrCreate(
            ['email' => 'admin'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin123'),
                'is_admin' => true,
            ]
        );

        $this->seedDashboardData();
    }

    private function seedDashboardData(): void
    {
        $sentiments = Sentiment::query()->pluck('id', 'name');
        $today = CarbonImmutable::now();

        $instagramPosts = collect([
            ['id' => 'demo-instagram-1', 'caption' => 'Informasi layanan pajak daerah Kota Bandung', 'daysAgo' => 2],
            ['id' => 'demo-instagram-2', 'caption' => 'Jadwal pelayanan Bapenda minggu ini', 'daysAgo' => 5],
            ['id' => 'demo-instagram-3', 'caption' => 'Program pelayanan publik terbaru', 'daysAgo' => 9],
        ])->mapWithKeys(function (array $post) use ($today) {
            $publishedAt = $today->subDays($post['daysAgo'])->setTime(9, 0);
            $record = InstagramPost::updateOrCreate(
                ['instagram_media_id' => $post['id']],
                [
                    'caption' => $post['caption'],
                    'permalink' => 'https://instagram.com/p/'.$post['id'],
                    'media_type' => 'IMAGE',
                    'published_at' => $publishedAt,
                    'posted_at' => $publishedAt,
                ]
            );

            return [$post['id'] => $record];
        });

        $instagramSentiments = [Sentiment::POSITIVE, Sentiment::NEUTRAL, Sentiment::NEGATIVE];
        $instagramComments = [
            'Informasinya sangat membantu, akhirnya tahu cara cek tagihan pajak online.',
            'Kalau mau datang langsung, pelayanan dibuka sampai jam berapa ya?',
            'Saya sudah coba, tetapi halaman pembayarannya masih error sejak pagi.',
            'Terima kasih infonya, prosesnya jadi lebih mudah dan tidak perlu antre lama.',
            'Untuk pembayaran bulan ini batas akhirnya tanggal berapa?',
            'Mohon diperjelas dokumen yang harus dibawa agar tidak bolak-balik.',
            'Pelayanannya cepat sekali hari ini, petugasnya juga ramah. Terima kasih!',
            'Apakah layanan ini tersedia untuk semua kecamatan di Kota Bandung?',
            'Saya sudah mengisi formulir, tetapi belum menerima email konfirmasi.',
            'Kontennya informatif, semoga jadwal pelayanan seperti ini rutin dibagikan.',
            'Bisa dibantu cek status pengajuan saya? Sudah tiga hari belum ada kabar.',
            'Noted, akan saya bagikan ke keluarga supaya tidak ketinggalan informasinya.',
        ];
        foreach (range(1, 12) as $index) {
            $sentiment = $instagramSentiments[($index - 1) % count($instagramSentiments)];
            $commentedAt = $today->subDays(($index - 1) % 7)->setTime(10 + ($index % 8), 15);
            $post = $instagramPosts->values()[($index - 1) % $instagramPosts->count()];

            InstagramComment::updateOrCreate(
                ['instagram_comment_id' => 'demo-instagram-comment-'.$index],
                [
                    'instagram_post_id' => $post->id,
                    'sentiment_id' => $sentiments[$sentiment],
                    'comment_id' => 'demo-instagram-comment-'.$index,
                    'username' => ['warga_bandung', 'rani.pratama', 'andi_setiawan', 'nisa_hapsari'][$index % 4].$index,
                    'comment' => $instagramComments[$index - 1],
                    'sentiment' => $sentiment,
                    'created_time' => $commentedAt,
                    'commented_at' => $commentedAt,
                    'like_count' => $index,
                ]
            );
        }

        $tweets = collect([
            ['id' => 'demo-tweet-1', 'text' => 'Bapenda Kota Bandung menghadirkan layanan pajak yang mudah.', 'daysAgo' => 1],
            ['id' => 'demo-tweet-2', 'text' => 'Simak informasi pelayanan publik terbaru dari Bapenda.', 'daysAgo' => 4],
            ['id' => 'demo-tweet-3', 'text' => 'Pelayanan pajak daerah terus kami tingkatkan.', 'daysAgo' => 8],
        ])->mapWithKeys(function (array $tweet) use ($today) {
            $publishedAt = $today->subDays($tweet['daysAgo'])->setTime(11, 0);
            $record = Tweet::updateOrCreate(
                ['tweet_id' => $tweet['id']],
                [
                    'text' => $tweet['text'],
                    'tweet' => $tweet['text'],
                    'author_username' => 'bapenda_bdg',
                    'author' => 'Bapenda Kota Bandung',
                    'reply_count' => 4,
                    'like_count' => 18,
                    'repost_count' => 3,
                    'permalink' => 'https://x.com/bapenda_bdg/status/'.$tweet['id'],
                    'published_at' => $publishedAt,
                    'posted_at' => $publishedAt,
                ]
            );

            return [$tweet['id'] => $record];
        });

        $twitterSentiments = [Sentiment::NEUTRAL, Sentiment::POSITIVE, Sentiment::NEGATIVE];
        $twitterComments = [
            'Min, link informasi lengkapnya bisa dibagikan? Saya ingin cek syaratnya.',
            'Akhirnya ada update yang jelas. Terima kasih sudah responsif!',
            'Website-nya belum bisa dibuka dari tadi, mohon segera dicek.',
            'Apresiasi untuk petugas yang tetap melayani dengan cepat.',
            'Apakah pembayaran bisa dilakukan melalui mobile banking?',
            'Tolong tambahkan contoh pengisian formulir untuk warga.',
            'Informasi seperti ini sangat berguna, mohon rutin di-update.',
            'Saya sudah kirim berkas kemarin, bagaimana cara melihat statusnya?',
            'Jadwalnya sudah jelas, jadi lebih mudah mengatur waktu datang.',
            'Balasan adminnya cepat dan solusinya langsung bisa dipraktikkan.',
            'Mohon ada notifikasi jika layanan sedang mengalami gangguan.',
            'Terima kasih, infonya saya teruskan ke RT agar warga lain tahu.',
        ];
        foreach (range(1, 12) as $index) {
            $sentiment = $twitterSentiments[($index - 1) % count($twitterSentiments)];
            $commentedAt = $today->subDays(($index + 1) % 7)->setTime(13 + ($index % 6), 30);
            $tweet = $tweets->values()[($index - 1) % $tweets->count()];

            TweetComment::updateOrCreate(
                ['tweet_reply_id' => 'demo-tweet-comment-'.$index],
                [
                    'tweet_id' => $tweet->id,
                    'sentiment_id' => $sentiments[$sentiment],
                    'reply_id' => 'demo-tweet-comment-'.$index,
                    'username' => ['bayu_bdg', 'salsa_k', 'fajarwijaya', 'dian_permataa'][$index % 4].$index,
                    'comment' => $twitterComments[$index - 1],
                    'reply' => $twitterComments[$index - 1],
                    'sentiment' => $sentiment,
                    'created_time' => $commentedAt,
                    'commented_at' => $commentedAt,
                    'like_count' => $index,
                ]
            );
        }

        $instagramPosts->each(fn (InstagramPost $post) => $post->update([
            'comments_count' => $post->comments()->count(),
        ]));

        $tweets->each(fn (Tweet $tweet) => $tweet->update([
            'reply_count' => $tweet->comments()->count(),
        ]));
    }
}
