<?php

namespace App\Http\Controllers\FrontOffice;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

use App\Models\Order;
use App\Models\Product;
use App\Models\Article;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return View
     * @throws BindingResolutionException
     */
    public function __invoke(): View
    {
        /**
         * @var User $authUser
         */
        $authUser = Auth::user();

        $products = Product::query()->available()->inStock()->orderBy('priority')->limit(3)->get();
        $orders   = Order::query()->byCustomer($authUser)->orderBy('updated_at')->get();

        $articleBienvenue = Article::query()->where('title', 'texte_bienvenue')->firstOrFail();

        return view()->make(
            'frontoffice.dashboard',
            ['products' => $products, 'orders' => $orders, 'articleBienvenue' => $articleBienvenue]
        );
    }
}
