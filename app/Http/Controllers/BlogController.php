<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\SiteSetting;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        $this->ensureBlogEnabled();

        $posts = BlogPost::query()
            ->active()
            ->ordered()
            ->with('media')
            ->paginate(12);

        return view('pages.blog.index', compact('posts'));
    }

    public function show(BlogPost $post): View
    {
        $this->ensureBlogEnabled();
        abort_unless($post->is_active, 404);

        $post->load('media');

        $related = BlogPost::query()
            ->active()
            ->where('id', '!=', $post->id)
            ->ordered()
            ->with('media')
            ->take(3)
            ->get();

        return view('pages.blog.show', compact('post', 'related'));
    }

    private function ensureBlogEnabled(): void
    {
        abort_unless((bool) SiteSetting::current()->show_blog_section, 404);
    }
}
