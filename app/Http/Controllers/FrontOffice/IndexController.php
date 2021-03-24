<?php

namespace App\Http\Controllers\FrontOffice;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Auth;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

use App\Http\Requests\MessageRequest;

use App\Models\Article;
use App\Models\Message;

class IndexController extends Controller
{
    /**
     * @return View
     * @throws BindingResolutionException
     */
    public function index(): View
    {
        $articles = Article::published()
            ->orderBy('priority', 'desc')
            ->orderBy('updated_at', 'desc')
            ->paginate(config('paginate.frontoffice.articles.index'));

        return view()->make('frontoffice.index')->with('articles', $articles);
    }

    /**
     * @return View
     * @throws BindingResolutionException
     */
    public function tableRonde(): View
    {
        return view()->make('frontoffice.table_ronde');
    }
}
