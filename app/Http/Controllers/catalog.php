<?php
// app/Http/Controllers/CatalogController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class catalog extends Controller
{
    public function index(Request $request)
    {
        // Data dummy – ganti ke DB nanti
        $products = collect([
            [
                'id' => 1, 'name' => 'Keripik Balado', 'slug' => 'keripik-balado',
                'category' => 'keripik', 'price' => 15000, 'points' => 1,
                'image' => 'keripik.jpg', 'tags' => ['Pedas', 'Best Seller']
            ],
            [
                'id' => 2, 'name' => 'Pastel Isi Abon', 'slug' => 'pastel-abon',
                'category' => 'pastel', 'price' => 12000, 'points' => 1,
                'image' => 'pastel.jpg', 'tags' => ['Gurih']
            ],
            [
                'id' => 3, 'name' => 'Tela-Tela Keju', 'slug' => 'tela-keju',
                'category' => 'tela-tela', 'price' => 10000, 'points' => 1,
                'image' => 'tela.jpg', 'tags' => ['Keju']
            ],
        ]);

        $q        = trim($request->get('q', ''));
        $category = $request->get('category');
        $sort     = $request->get('sort', 'pop');

        if ($q !== '') {
            $qLower = mb_strtolower($q);
            $products = $products->filter(fn ($p) =>
                str_contains(mb_strtolower($p['name']), $qLower)
                || str_contains(mb_strtolower($p['category']), $qLower)
            );
        }

        if ($category && in_array($category, ['keripik', 'pastel', 'tela-tela'])) {
            $products = $products->where('category', $category);
        }

        $products = match ($sort) {
            'price_asc'   => $products->sortBy('price'),
            'price_desc'  => $products->sortByDesc('price'),
            'points_desc' => $products->sortByDesc('points'),
            default       => $products, // pop
        };

        return view('catalog.index', [
            'products' => $products->values()->all(),
            'q'        => $q,
            'category' => $category,
            'sort'     => $sort,
        ]);
    }
}
