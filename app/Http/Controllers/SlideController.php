<?php

namespace App\Http\Controllers;

use App\FeaturedPiece as Slide;
use Illuminate\Http\Request;

// TODO Superseded by FeaturedPieceController
// Remove when new homepage is launched.
class SlideController extends Controller
{
    public function index()
    {
        return view('slides.index')
            ->with('slides', Slide::orderBy('id', 'desc')->get());
    }

    public function create()
    {
        return view('slides.form');
    }

    public function store(Request $request)
    {
        $request->validate(Slide::$rules);

        $slide = Slide::create($request->input());
        if ($request->hasFile('image')) {
            $slide
                ->addMediaFromRequest('image')
                ->toMediaCollection('image');
        }

        return redirect()
            ->route('slide.index')
            ->with('message', 'Carousel ' .$slide->name. ' bol vytvorený');
    }

    public function edit(Slide $slide)
    {
        return view('slides.form')->with('slide', $slide);
    }

    public function update(Request $request, Slide $slide)
    {
        $request->validate(Slide::$rules);

        $slide->update($request->input());
        if ($request->hasFile('image')) {
            $slide
                ->addMediaFromRequest('image')
                ->toMediaCollection('image');
        }

        return redirect()
            ->route('slide.index')
            ->with('message', 'Carousel ' .$slide->name. ' bol upravený');
    }

    public function destroy(Slide $slide)
    {
        $slide->delete();

        return redirect()
            ->route('slide.index')
            ->with('message', 'Carousel bol zmazaný');
    }
}
