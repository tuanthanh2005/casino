<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InitialContentSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();

        $categories = [
            [
                'name' => 'Beginners',
                'slug' => 'beginners',
                'description' => 'Essential guides and tips for those just starting their aquarium journey.',
            ],
            [
                'name' => 'Setup Guides',
                'slug' => 'setup-guides',
                'description' => 'Step-by-step instructions on setting up your first tank, substrate, filters, and more.',
            ],
            [
                'name' => 'Fish Care',
                'slug' => 'fish-care',
                'description' => 'Learn how to keep your fish healthy, happy, and thriving.',
            ],
            [
                'name' => 'Problems & Fixes',
                'slug' => 'problems-fixes',
                'description' => 'Troubleshooting common aquarium issues like cloudy water, algae, and fish illness.',
            ],
            [
                'name' => 'Product Reviews',
                'slug' => 'product-reviews',
                'description' => 'Honest reviews of filters, heaters, lighting, and fish food.',
            ],
            [
                'name' => 'Comparisons',
                'slug' => 'comparisons',
                'description' => 'Comparing different aquarium equipment and fish species.',
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], $cat);
        }

        $cats = Category::all()->pluck('id', 'slug');

        $posts = [
            [
                'title' => 'Best Fish for Beginners',
                'category_id' => $cats['beginners'],
                'content' => 'If you are just starting out with your first aquarium, choosing the right fish is crucial... [Seed Content]',
            ],
            [
                'title' => 'How to Set Up an Aquarium Step by Step',
                'category_id' => $cats['setup-guides'],
                'content' => 'Setting up a fish tank is an exciting process. Here is our step-by-step guide... [Seed Content]',
            ],
            [
                'title' => 'Why Are My Fish Dying?',
                'category_id' => $cats['problems-fixes'],
                'content' => 'It is heartbreaking to find your fish floating at the top of the tank. Here is why it might be happening... [Seed Content]',
            ],
            [
                'title' => 'Best Aquarium Filter for Beginners',
                'category_id' => $cats['product-reviews'],
                'content' => 'A good filtration system is the heart of any aquarium. We review the top 5 beginner filters... [Seed Content]',
            ],
            [
                'title' => 'Cloudy Aquarium Water Fix',
                'category_id' => $cats['problems-fixes'],
                'content' => 'Cloudy water is a common issue for new tanks. Learn how to clear it up quickly... [Seed Content]',
            ],
            [
                'title' => 'Betta Fish Care Guide',
                'category_id' => $cats['fish-care'],
                'content' => 'Bettas are popular for their vibrant colors and personality. Here is how to care for them properly... [Seed Content]',
            ],
            [
                'title' => 'How Often Should You Feed Fish?',
                'category_id' => $cats['fish-care'],
                'content' => 'Overfeeding is a lead cause of water quality issues. Discover the ideal feeding schedule... [Seed Content]',
            ],
            [
                'title' => 'Best Small Fish Tanks',
                'category_id' => $cats['product-reviews'],
                'content' => 'Sometimes a small footprint is all you need. Here are the best nano and small tanks for beginners... [Seed Content]',
            ],
            [
                'title' => 'Sponge Filter vs HOB Filter',
                'category_id' => $cats['comparisons'],
                'content' => 'Both filters have their pros and cons. Let us compare which one is right for your setup... [Seed Content]',
            ],
            [
                'title' => 'Easiest Fish to Take Care Of',
                'category_id' => $cats['beginners'],
                'content' => 'Low maintenance fish are great for busy beginners. Here is our top 10 list... [Seed Content]',
            ],
        ];

        foreach ($posts as $post) {
            Post::updateOrCreate(
                ['slug' => Str::slug($post['title'])],
                array_merge($post, [
                    'slug' => Str::slug($post['title']),
                    'excerpt' => Str::limit(strip_tags($post['content']), 150),
                    'author_id' => $admin->id,
                    'status' => 'published',
                    'published_at' => now(),
                    'is_featured' => rand(0, 5) === 0, // Randomly feature some posts
                ])
            );
        }
    }
}
