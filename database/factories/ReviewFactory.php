<?php

namespace Database\Factories;

use App\Models\Criteria;
use App\Models\Listing;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $rating = fake()->numberBetween(1, 5);
        $status = fake()->randomElement(['approved', 'approved', 'approved', 'approved', 'pending', 'rejected']);

        return [
            'listing_id' => Listing::factory(),
            'user_id' => User::factory(),
            'rating' => $rating,
            'comment_ar' => fake()->randomElement($this->commentsFor($rating)),
            'status' => $status,
            'moderated_by' => $status !== 'pending'
                ? User::where('is_admin', true)->inRandomOrder()->value('id')
                : null,
            'moderated_at' => $status !== 'pending' ? now() : null,
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Review $review) {
            $criteriaIds = Criteria::inRandomOrder()->take(fake()->numberBetween(3, 6))->pluck('id');

            $attach = [];
            foreach ($criteriaIds as $criteriaId) {
                $attach[$criteriaId] = ['value' => fake()->boolean($review->rating >= 4 ? 85 : 40)];
            }
            $review->criteria()->attach($attach);

            $review->listing->recalculateRatings();
        });
    }

    /**
     * @return array<int, string>
     */
    private function commentsFor(int $rating): array
    {
        return match (true) {
            $rating >= 5 => [
                'تجربة رائعة من جميع النواحي، الخدمة ممتازة والمكان يستحق الزيارة بالتأكيد.',
                'من أفضل الأماكن التي زرتها، الطاقم متعاون جداً والاهتمام بالتفاصيل واضح.',
                'أنصح به بشدة للعائلات، كل شيء كان منظماً ونظيفاً ومريحاً.',
            ],
            $rating === 4 => [
                'تجربة جيدة جداً وسأكرر الزيارة، هناك بعض التفاصيل البسيطة التي يمكن تحسينها.',
                'مكان جميل ومناسب للعائلة العربية، الخدمة كانت مرضية بشكل عام.',
                'إجمالاً تجربة إيجابية، الأسعار مناسبة مقارنة بجودة الخدمة.',
            ],
            $rating === 3 => [
                'تجربة متوسطة، لا بأس بها لكن كنت أتوقع مستوى أفضل من الخدمة.',
                'المكان مقبول لكن يحتاج إلى تحسين في بعض الجوانب المتعلقة بالراحة.',
                'تجربة عادية، ربما أعطيها فرصة أخرى لاحقاً.',
            ],
            $rating === 2 => [
                'لم تكن التجربة كما توقعت، الخدمة كانت بطيئة والاهتمام بالتفاصيل ضعيف.',
                'هناك حاجة حقيقية لتحسين مستوى الخدمة وسرعة الاستجابة.',
                'التجربة دون المستوى المطلوب مقارنة بالسعر المدفوع.',
            ],
            default => [
                'للأسف تجربة سيئة ولا أنصح بها، لم تُلبَّ أبسط التوقعات.',
                'خدمة ضعيفة جداً ولم أشعر بالراحة خلال الزيارة.',
                'تجربة مخيبة للآمال ولن أكررها مرة أخرى.',
            ],
        };
    }
}
