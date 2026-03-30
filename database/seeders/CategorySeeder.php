<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Основные категории
            [
                'name' => 'Ремонт и строительство',
                'slug' => 'remont-i-stroitelstvo',
                'description' => 'Ремонт квартир, домов, отделочные работы',
                'parent_id' => null,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Уборка и клининг',
                'slug' => 'uborka-i-klining',
                'description' => 'Уборка квартир, офисов, химчистка',
                'parent_id' => null,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Обучение и консультации',
                'slug' => 'obuchenie-i-konsultatsii',
                'description' => 'Репетиторы, курсы, тренинги',
                'parent_id' => null,
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Красота и здоровье',
                'slug' => 'krasota-i-zdorove',
                'description' => 'Парикмахеры, маникюр, массаж, спорт',
                'parent_id' => null,
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Доставка и грузоперевозки',
                'slug' => 'dostavka-i-gruzoperevozki',
                'description' => 'Курьеры, переезды, грузчики',
                'parent_id' => null,
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Фото и видео',
                'slug' => 'foto-i-video',
                'description' => 'Фотографы, видеографы, обработка',
                'parent_id' => null,
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'IT и разработка',
                'slug' => 'it-i-razrabotka',
                'description' => 'Сайты, приложения, программирование',
                'parent_id' => null,
                'sort_order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Дизайн и творчество',
                'slug' => 'dizayn-i-tvorchestvo',
                'description' => 'Дизайн интерьеров, графика, иллюстрации',
                'parent_id' => null,
                'sort_order' => 8,
                'is_active' => true,
            ],
            [
                'name' => 'Автоуслуги',
                'slug' => 'avtouslugi',
                'description' => 'Ремонт авто, шиномонтаж, эвакуатор',
                'parent_id' => null,
                'sort_order' => 9,
                'is_active' => true,
            ],
            [
                'name' => 'Юридические услуги',
                'slug' => 'yuridicheskie-uslugi',
                'description' => 'Консультации юристов, адвокатов',
                'parent_id' => null,
                'sort_order' => 10,
                'is_active' => true,
            ],

            // Подкатегории для "Ремонт и строительство"
            [
                'name' => 'Ремонт квартир',
                'slug' => 'remont-kvartir',
                'description' => 'Косметический и капитальный ремонт',
                'parent_id' => 1,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Электрика',
                'slug' => 'elektrika',
                'description' => 'Монтаж проводки, замена розеток',
                'parent_id' => 1,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Сантехника',
                'slug' => 'santehnika',
                'description' => 'Установка, замена, ремонт',
                'parent_id' => 1,
                'sort_order' => 3,
                'is_active' => true,
            ],

            // Подкатегории для "Уборка и клининг"
            [
                'name' => 'Уборка квартир',
                'slug' => 'uborka-kvartir',
                'description' => 'Регулярная и генеральная уборка',
                'parent_id' => 2,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Уборка офисов',
                'slug' => 'uborka-ofisov',
                'description' => 'Уборка коммерческих помещений',
                'parent_id' => 2,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Химчистка',
                'slug' => 'himchistka',
                'description' => 'Мягкой мебели, ковров',
                'parent_id' => 2,
                'sort_order' => 3,
                'is_active' => true,
            ],

            // Подкатегории для "Обучение и консультации"
            [
                'name' => 'Репетиторы',
                'slug' => 'repetitory',
                'description' => 'Школьные предметы, языки',
                'parent_id' => 3,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Музыка и искусство',
                'slug' => 'muzyka-i-iskusstvo',
                'description' => 'Обучение музыке, рисованию',
                'parent_id' => 3,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Бизнес-консультации',
                'slug' => 'biznes-konsultatsii',
                'description' => 'Маркетинг, управление',
                'parent_id' => 3,
                'sort_order' => 3,
                'is_active' => true,
            ],

            // Подкатегории для "Красота и здоровье"
            [
                'name' => 'Парикмахеры',
                'slug' => 'parikmahery',
                'description' => 'Стрижки, укладки, окрашивание',
                'parent_id' => 4,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Маникюр и педикюр',
                'slug' => 'manikyur-i-pedikyur',
                'description' => 'Ногтевой сервис',
                'parent_id' => 4,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Массаж',
                'slug' => 'massazh',
                'description' => 'Лечебный, спортивный, релакс',
                'parent_id' => 4,
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Фитнес и йога',
                'slug' => 'fitnes-i-yoga',
                'description' => 'Тренировки, растяжка',
                'parent_id' => 4,
                'sort_order' => 4,
                'is_active' => true,
            ],

            // Подкатегории для "IT и разработка"
            [
                'name' => 'Разработка сайтов',
                'slug' => 'razrabotka-saytov',
                'description' => 'Создание и поддержка сайтов',
                'parent_id' => 7,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Мобильные приложения',
                'slug' => 'mobilnye-prilozheniya',
                'description' => 'iOS, Android разработка',
                'parent_id' => 7,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'SEO продвижение',
                'slug' => 'seo-prodvizhenie',
                'description' => 'Оптимизация сайтов',
                'parent_id' => 7,
                'sort_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}