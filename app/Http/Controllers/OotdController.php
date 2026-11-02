<?php

namespace App\Http\Controllers;

use App\Models\Ootd;
use App\Notifications\Notice;
use App\Services\OutfitImageStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OotdController extends Controller
{
    private const MAX_PHOTO_KB = 10240;

    private const SORTS = [
        'newest' => 'Newest first',
        'oldest' => 'Oldest first',
        'most_worn' => 'Most worn',
        'least_recent' => 'Not worn in a while',
    ];

    public function index(Request $request): View
    {
        $user = $request->user();

        $filters = [
            'q' => trim($request->string('q')->toString()),
            'occasion' => $request->string('occasion')->toString(),
            'favorites' => $request->boolean('favorites'),
            'sort' => $request->string('sort')->toString(),
        ];

        if (! array_key_exists($filters['occasion'], config('ootd.occasions'))) {
            $filters['occasion'] = '';
        }

        if (! array_key_exists($filters['sort'], self::SORTS)) {
            $filters['sort'] = 'newest';
        }

        $ootds = $user->ootds()
            ->withWearStats()
            ->when($filters['q'] !== '', fn ($query) => $query->where('description', 'like', '%'.$filters['q'].'%'))
            ->when($filters['occasion'] !== '', fn ($query) => $query->where('occasion', $filters['occasion']))
            ->when($filters['favorites'], fn ($query) => $query->where('is_favorite', true))
            ->tap(fn ($query) => match ($filters['sort']) {
                'oldest' => $query->oldest(),
                'most_worn' => $query->orderByDesc('wear_logs_count')->latest(),
                'least_recent' => $query->orderByRaw('last_worn_on is not null')->orderBy('last_worn_on')->latest(),
                default => $query->latest(),
            })
            ->paginate(12)
            ->withQueryString();

        return view('ootds.index', [
            'ootds' => $ootds,
            'filters' => $filters,
            'sorts' => self::SORTS,
            'isFiltered' => $filters['q'] !== '' || $filters['occasion'] !== '' || $filters['favorites'],
            'total' => $user->ootds()->count(),
            'wornTodayId' => $user->wornToday()?->id,
        ]);
    }

    public function create(): View
    {
        return view('ootds.create');
    }

    public function store(Request $request, OutfitImageStorage $imageStorage): RedirectResponse
    {
        $data = $request->validate([
            'photo' => $this->photoRules(required: true),
            'description' => ['nullable', 'string', 'max:500'],
            'occasion' => ['nullable', Rule::in(array_keys(config('ootd.occasions')))],
        ], $this->messages());

        $user = $request->user();

        $user->ootds()->create([
            'image_path' => $imageStorage->store($request->file('photo'), $user->id),
            'description' => $data['description'] ?? null,
            'occasion' => $data['occasion'] ?? null,
        ]);

        $count = $user->ootds()->count();

        if (in_array($count, config('ootd.milestones'), true)) {
            $user->notify(Notice::milestone($count));
        }

        return redirect()->route('ootds.index')->with('status', 'OOTD uploaded.');
    }

    public function edit(Ootd $ootd): View
    {
        Gate::authorize('update', $ootd);

        return view('ootds.edit', ['ootd' => $ootd]);
    }

    public function update(Request $request, Ootd $ootd, OutfitImageStorage $imageStorage): RedirectResponse
    {
        Gate::authorize('update', $ootd);

        $data = $request->validate([
            'photo' => $this->photoRules(required: false),
            'description' => ['nullable', 'string', 'max:500'],
            'occasion' => ['nullable', Rule::in(array_keys(config('ootd.occasions')))],
        ], $this->messages());

        $oldPath = $ootd->image_path;

        if ($request->hasFile('photo')) {
            $ootd->image_path = $imageStorage->store($request->file('photo'), $ootd->user_id);
        }

        $ootd->description = $data['description'] ?? null;
        $ootd->occasion = $data['occasion'] ?? null;
        $ootd->save();

        if ($oldPath !== $ootd->image_path) {
            $imageStorage->delete($oldPath);
        }

        return redirect()->route('ootds.index')->with('status', 'OOTD updated.');
    }

    public function destroy(Ootd $ootd): RedirectResponse
    {
        Gate::authorize('delete', $ootd);

        $ootd->delete();

        return redirect()->route('ootds.index')->with('status', 'OOTD deleted.');
    }

    /** @return array<int, string> */
    private function photoRules(bool $required): array
    {
        return [
            $required ? 'required' : 'nullable',
            'image',
            'mimes:jpg,jpeg,png,webp',
            'max:'.self::MAX_PHOTO_KB,
        ];
    }

    /** @return array<string, string> */
    private function messages(): array
    {
        return [
            'photo.uploaded' => 'The photo could not be uploaded. It may be too large — try a smaller one.',
            'photo.max' => 'The photo may not be larger than 10 MB.',
            'photo.mimes' => 'The photo must be a JPG, PNG or WebP image.',
        ];
    }
}
