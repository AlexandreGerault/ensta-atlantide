<?php

namespace App\Http\Controllers\FrontOffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\MessageRequest;
use App\Mail\ContactConfirmation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class MessagesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     * @throws BindingResolutionException
     */
    public function index(): View
    {
        /**
         * @var User $user
         */
        $user = Auth::user();
        $messages = $user->messages;

        return view()->make('frontoffice.messages.index', [
            'messages' => $messages,
            'categories_messages' => Message::CATEGORIES
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param MessageRequest $request
     *
     * @return RedirectResponse
     */
    public function store(MessageRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['sender_ip'] = $request->ip();

        $message = Message::create($data);
        $message->author()->associate(Auth::user());
        $message->save();

        Mail::to($message->email)->send(new ContactConfirmation($data));
        return redirect()->back()->with('success', 'Votre demande a bien été prise en compte');
    }

    /**
     * Display the specified resource.
     *
     * @param Message $message
     *
     * @return View
     * @throws BindingResolutionException
     * @throws AuthorizationException
     */
    public function show(Message $message): View
    {
        $this->authorize('view', $message);
        return view()->make('frontoffice.messages.show')->with('message', $message);
    }
}
