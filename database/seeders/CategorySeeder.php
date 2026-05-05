<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Корневые категории
        $categories = [
            // Ремонт и строительство
            ['name' => 'Ремонт и строительство', 'slug' => 'remont', 'sort_order' => 1],
            ['name' => 'Сантехника', 'slug' => 'santehnika', 'sort_order' => 2],
            ['name' => 'Электрика', 'slug' => 'elektrika', 'sort_order' => 3],
            ['name' => 'Клининг и уборка', 'slug' => 'cleaning', 'sort_order' => 4],
            
            // Красота и здоровье
            ['name' => 'Красота и здоровье', 'slug' => 'beauty', 'sort_order' => 5],
            ['name' => 'Парикмахерские услуги', 'slug' => 'hairdresser', 'sort_order' => 6],
            ['name' => 'Маникюр и педикюр', 'slug' => 'manicure', 'sort_order' => 7],
            
            // Обучение и консультации
            ['name' => 'Обучение и консультации', 'slug' => 'education', 'sort_order' => 8],
            ['name' => 'Репетиторы', 'slug' => 'repetitors', 'sort_order' => 9],
            ['name' => 'Курсы и тренинги', 'slug' => 'courses', 'sort_order' => 10],
            
            // Дизайн и IT
            ['name' => 'Дизайн и IT', 'slug' => 'design', 'sort_order' => 11],
            ['name' => 'Веб-разработка', 'slug' => 'webdev', 'sort_order' => 12],
            ['name' => 'Графический дизайн', 'slug' => 'graphic', 'sort_order' => 13],
            
            // Бытовые услуги
            ['name' => 'Бытовые услуги', 'slug' => 'household', 'sort_order' => 14],
            ['name' => 'Ремонт техники', 'slug' => 'repair', 'sort_order' => 15],
            ['name' => 'Доставка и грузчики', 'slug' => 'delivery', 'sort_order' => 16],
            
            // Творчество и рукоделие
            ['name' => 'Творчество и рукоделие', 'slug' => 'creative', 'sort_order' => 17],
            ['name' => 'Дизайн интерьера', 'slug' => 'interior', 'sort_order' => 18],
            ['name' => 'Фотография и видео', 'slug' => 'photo', 'sort_order' => 19],
            ['name' => 'Digital Art', 'slug' => 'digitalart', 'sort_order' => 20],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        // Подкатегории
        $subcategories = [
            // Ремонт и строительство
            ['name' => 'Отделочные работы', 'slug' => 'otdelka', 'parent_slug' => 'remont', 'sort_order' => 1],
            ['name' => 'Ремонт квартир', 'slug' => 'remont-kvartir', 'parent_slug' => 'remont', 'sort_order' => 2],
            ['name' => 'Установка окон и дверей', 'slug' => 'windows', 'parent_slug' => 'remont', 'sort_order' => 3],
            
            // Сантехника
            ['name' => 'Установка сантехники', 'slug' => 'ustanovka', 'parent_slug' => 'santehnika', 'sort_order' => 1],
            ['name' => 'Ремонт сантехники', 'slug' => 'remont-santehniki', 'parent_slug' => 'santehnika', 'sort_order' => 2],
            ['name' => 'Засоры и прочистка', 'slug' => 'zasory', 'parent_slug' => 'santehnika', 'sort_order' => 3],
            
            // Электрика
            ['name' => 'Электромонтаж', 'slug' => 'elektromontazh', 'parent_slug' => 'elektrika', 'sort_order' => 1],
            ['name' => 'Замена проводки', 'slug' => 'wiring', 'parent_slug' => 'elektrika', 'sort_order' => 2],
            ['name' => 'Установка освещения', 'slug' => 'lighting', 'parent_slug' => 'elektrika', 'sort_order' => 3],
            
            // Графический дизайн
            ['name' => 'Логотипы и айдентика', 'slug' => 'logos', 'parent_slug' => 'graphic', 'sort_order' => 1],
            ['name' => 'Иллюстрации на заказ', 'slug' => 'illustrations', 'parent_slug' => 'graphic', 'sort_order' => 2],
            ['name' => 'Digital Art', 'slug' => 'digitalart-sub', 'parent_slug' => 'graphic', 'sort_order' => 3],
            
            // Веб-разработка
            ['name' => 'Сайты под ключ', 'slug' => 'sites', 'parent_slug' => 'webdev', 'sort_order' => 1],
            ['name' => 'Интернет-магазины', 'slug' => 'eshop', 'parent_slug' => 'webdev', 'sort_order' => 2],
            ['name' => 'Верстка сайтов', 'slug' => 'layout', 'parent_slug' => 'webdev', 'sort_order' => 3],
            
            // Репетиторы
            ['name' => 'Математика', 'slug' => 'math', 'parent_slug' => 'repetitors', 'sort_order' => 1],
            ['name' => 'Русский язык', 'slug' => 'russian', 'parent_slug' => 'repetitors', 'sort_order' => 2],
            ['name' => 'Английский язык', 'slug' => 'english', 'parent_slug' => 'repetitors', 'sort_order' => 3],
            ['name' => 'Программирование', 'slug' => 'programming', 'parent_slug' => 'repetitors', 'sort_order' => 4],
        ];

        foreach ($subcategories as $sub) {
            $parent = Category::where('slug', $sub['parent_slug'])->first();
            if ($parent) {
                Category::create([
                    'name' => $sub['name'],
                    'slug' => $sub['slug'],
                    'parent_id' => $parent->id,
                    'sort_order' => $sub['sort_order'],
                ]);
            }
        }
    }
}