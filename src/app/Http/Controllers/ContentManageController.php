<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContentRequest;
use App\Models\Content;
use App\Models\Genre;
use App\Models\SubGenre;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ContentManageController extends Controller
{
    public function create()
    {
        return view('contents.form', [
            'content' => new Content(['price' => 0, 'format' => 'external_tool']),
            'genres' => Genre::with('subGenres')->get(),
            'subGenres' => SubGenre::all(),
            'formats' => $this->formats(),
        ]);
    }

    public function store(StoreContentRequest $request)
    {
        $data = $this->payload($request);
        $data['user_id'] = $request->user()->id;
        $data['slug'] = $this->uniqueSlug($request->input('title'));
        $data['published_at'] = now();
        $data['status'] = 'published';

        $content = Content::create($data);
        $this->syncTags($content, $request->input('tags'));

        return redirect()->route('contents.show', $content)->with('status', 'コンテンツを投稿しました。');
    }

    public function edit(Content $content)
    {
        abort_unless($content->user_id === auth()->id(), 403);

        $content->load('tags');

        return view('contents.form', [
            'content' => $content,
            'genres' => Genre::with('subGenres')->get(),
            'subGenres' => SubGenre::all(),
            'formats' => $this->formats(),
        ]);
    }

    public function update(StoreContentRequest $request, Content $content)
    {
        abort_unless($content->user_id === $request->user()->id, 403);

        $data = $this->payload($request, $content);

        if ($content->title !== $request->input('title')) {
            $data['slug'] = $this->uniqueSlug($request->input('title'), $content);
        }

        $content->update($data);
        $this->syncTags($content, $request->input('tags'));

        return redirect()->route('contents.show', $content)->with('status', 'コンテンツ情報を更新しました。');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'exists:contents,id'],
        ]);

        foreach ($request->input('order') as $index => $contentId) {
            Content::where('id', $contentId)
                ->where('user_id', $request->user()->id)
                ->update(['profile_order' => $index + 1]);
        }

        return response()->json(['status' => 'ok']);
    }

    private function payload(StoreContentRequest $request, ?Content $content = null)
    {
        $data = $request->only([
            'title',
            'genre_id',
            'sub_genre_id',
            'format',
            'description',
            'price',
            'license_type',
            'environment',
        ]);

        $data['license_type'] = $data['license_type'] ?: '個人利用・商用利用可';

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail_path'] = $request->file('thumbnail')->store('content-thumbnails', 'public');
        } elseif ($content) {
            $data['thumbnail_path'] = $content->thumbnail_path;
        }

        if ($request->hasFile('content_file')) {
            $file = $request->file('content_file');
            $data['file_path'] = $file->store('content-files', 'public');
            $data['file_size_mb'] = round($file->getSize() / 1024 / 1024, 2);
        } elseif ($content) {
            $data['file_path'] = $content->file_path;
            $data['file_size_mb'] = $content->file_size_mb;
        }

        return $data;
    }

    private function uniqueSlug($title, ?Content $ignore = null)
    {
        $base = Str::slug($title) ?: 'asset';
        $slug = $base;
        $count = 2;

        while (Content::where('slug', $slug)->when($ignore, function ($query) use ($ignore) {
            $query->whereKeyNot($ignore->id);
        })->exists()) {
            $slug = $base.'-'.$count++;
        }

        return $slug;
    }

    private function syncTags(Content $content, ?string $tagText)
    {
        $tagIds = collect(preg_split('/[,、\\s]+/u', $tagText ?: '', -1, PREG_SPLIT_NO_EMPTY))
            ->map(function ($name) {
                $name = trim($name);

                return Tag::firstOrCreate(
                    ['name' => $name],
                    ['slug' => Str::slug($name) ?: Str::random(10)]
                )->id;
            })
            ->unique()
            ->values();

        $content->tags()->sync($tagIds);
    }

    private function formats()
    {
        return [
            'text' => 'テキスト',
            'image' => '画像',
            'gif' => 'GIF',
            'audio' => '音声',
            'video' => '動画',
            'model_3d' => '3Dモデル',
            'animation' => 'アニメーション',
            'system' => 'システム',
            'external_tool' => '外部ツールデータ',
            'other' => 'その他',
        ];
    }
}
